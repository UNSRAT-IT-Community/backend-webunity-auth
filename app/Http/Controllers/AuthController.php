<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use App\ThirdParty\Internals;
use App\Exceptions\UnauthorizedAccess;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class AuthController extends Controller
{
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
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'is_verified' => $user->is_verified,
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
                'username' => Internals::getUserContextUsername(),
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
}

