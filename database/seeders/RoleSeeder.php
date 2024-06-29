<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        DB::table('roles')->insert([
            ['id' => 'a8bfd898-1670-4578-81aa-bd672ada389d', 'name' => 'member', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => '332c9430-d8e9-4233-97d2-5107a6d0fbe8', 'name' => 'committee', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => '3550f1cc-3097-489d-bd16-fc5444f5a594', 'name' => 'coordinator', 'created_at' => $timestamp, 'updated_at' => $timestamp]
        ]);
    }
}