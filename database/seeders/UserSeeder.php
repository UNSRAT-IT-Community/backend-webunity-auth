<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        DB::table('users')->insert([
            [
                'id' => '3550f1cc-3097-489d-bd16-fc5444f5a594',
                'name' => 'John Doe',
                'nim' => '210211060001',
                'email' => 'john@example.com',
                'profile_picture' => 'https://example.com/path/to/image1.jpg',
                'role_id' => 'a8bfd898-1670-4578-81aa-bd672ada389d', // member
                'division_id' => '051ac379-78d6-42b0-80cc-03d1e004b485', // ui/ux
                'password' => Hash::make('password'),
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'id' => '802944bd-8136-4a21-8977-16bc78d7c2bb',
                'name' => 'Jane Smith',
                'nim' => '210211060002',
                'email' => 'jane@example.com',
                'profile_picture' => 'https://example.com/path/to/image2.jpg',
                'role_id' => '332c9430-d8e9-4233-97d2-5107a6d0fbe8', // committee
                'division_id' => '9806afa6-7098-4f07-b254-83cbd4c34107', // front-end
                'password' => Hash::make('password'),
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'id' => 'a8901718-9578-43b9-ae7b-02058573ab12',
                'name' => 'Alice Johnson',
                'nim' => '210211060003',
                'email' => 'alice@example.com',
                'profile_picture' => 'https://example.com/path/to/image3.jpg',
                'role_id' => '3550f1cc-3097-489d-bd16-fc5444f5a594', // coordinator
                'division_id' => 'a571704e-ca59-4807-8303-719c5fe67b71', // back-end
                'password' => Hash::make('password'),
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]
        ]);
    }
}