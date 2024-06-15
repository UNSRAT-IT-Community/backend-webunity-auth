<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\ThirdParty\Internals;
use App\Exceptions\UnauthorizedAccess;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $role= Role::where("name", "Anggota")->first();
        $role_id = $role->id;
        try {
            $this->validate($request, [
                'name' => 'required|string',
                'nim' => 'required|unique:users|numeric',
                'email' => 'required|email|unique:users',
                'profile_picture' => 'required',
                'password' => 'required|min:6',
            ]);

            User::create([
                'name' => $request->input('name'),
                'nim' => $request->input('nim'),
                'email' => $request->input('email'),
                'profile_picture' => $request->input('profile_picture'),
                'role_id' => $role_id,
                'division_id' => $this->getDivision($request->input('division')),
                'password' => Hash::make($request->input('password')),
            ]);

            return $this->sendSuccessResponse(null, 'Registration Successful');
        } catch (ValidationException $e) {
            return $this->sendBadRequestResponse($e->errors());
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return $this->sendInternalServerErrorResponse($e);
        }
    }
    public function login(Request $request)
    {
        try {
            // Validasi form login
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Response 422 jika validasi gagal
            if ($validator->fails()) {
                return $this->sendBadRequestResponse($validator->errors());
            }

            $token = $this->emailPasswordLogin($request->email, $request->password);
            return $this->sendSuccessResponse($token, "Login success");

        } catch (UnauthorizedAccess $e) {
            return $this->sendUnauthorizedResponse("Invalid email or password.");
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    private function emailPasswordLogin(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (
            empty($user) ||
            !$user->is_accepted == "accepted"||
            !Hash::check($password, $user->password)
        ) {
            throw new UnauthorizedAccess("Invalid email or password");
        }

        return $this->generatePairToken($user);
    }
    private function ttl(int $minute): int
    {
        return time() + ($minute * 60);
    }

    private function generatePairToken($user)
    {

        /** Set TTL
         * 1 day for auth token
         * 7 days for default refresh token
         */
        $accessTokenTTL = $this->ttl(1440);
        $refreshTokenTTL = $this->ttl(10080);

        $accessTokenPayload = [
            'iss' => 'https://unityunsrat.dev',
            'aud' => 'https://unityunsrat.dev',
            'iat' => time(),
            'exp' => $accessTokenTTL,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'division_id' => $user->division_id,
                'role_id' => $user->role_id,
                'is_accepted' => $user->is_accepted,
            ]
        ];

        $refreshTokenPayload = $accessTokenPayload;
        unset($refreshTokenPayload['data']);
        $refreshTokenPayload['data']['id'] = $user->id;
        $refreshTokenPayload['exp'] = $refreshTokenTTL;

        $privateKey = file_get_contents(base_path('/' . env('JWT_PRIVATE_KEY')));

        $jwtPair = [
            'access_token' => JWT::encode($accessTokenPayload, $privateKey, 'RS256'),
            'refresh_token' => JWT::encode($refreshTokenPayload, $privateKey, 'RS256'),
        ];

        return $jwtPair;
    }

    public function refreshTokenLogin()
    {
        try {
            $userId = Internals::getUserContextId();
            $user = User::find($userId);

            if (empty($user)) {
                throw new UnauthorizedAccess("Invalid token");
            }

            $newToken = $this->generatePairToken($user);
            return $this->sendSuccessResponse($newToken, "Login success");
        } catch (Throwable $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
    private function generateExpiredToken()
    {
        $accessTokenPayload = [
            'iss' => 'https://unityunsrat.dev',
            'aud' => 'https://unityunsrat.dev',
            'iat' => time(),
            'exp' => time(),
            'data' => Crypt::encrypt([
                'id' => Internals::getUserContextId(),
                'email' => Internals::getUserContextEmail(),
                'name' => Internals::getUserContextUsername(),
            ])
        ];

        $refreshTokenPayload = $accessTokenPayload;
        unset($refreshTokenPayload['data']);
        $refreshTokenPayload['data']['id'] = Internals::getUserContextId();

        $privateKey = file_get_contents(base_path('/' . env('JWT_PRIVATE_KEY')));

        $jwtPair = [
            'access_token' => JWT::encode($accessTokenPayload, $privateKey, 'RS256'),
            'refresh_token' => JWT::encode($refreshTokenPayload, $privateKey, 'RS256'),
        ];

        return $jwtPair;
    }

    public function logout(Request $request)
    {
        try {
            $token = $this->generateExpiredToken();
            return $this->sendSuccessResponse($token, 'Logout success');
        } catch (Throwable $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    private function getDivision($division_name)
    {
        $division = Division::where("name", $division_name)->first();
        if($division == null) return response()->json(['message' => 'Divisi tersebut tidak ada']);
        $division_id = $division->id;
        return $division_id;
    }

}

