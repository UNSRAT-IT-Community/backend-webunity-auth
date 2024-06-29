<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        DB::table('divisions')->insert([
            ['id' => '9806afa6-7098-4f07-b254-83cbd4c34107', 'name' => 'front-end', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 'a571704e-ca59-4807-8303-719c5fe67b71', 'name' => 'back-end', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => '051ac379-78d6-42b0-80cc-03d1e004b485', 'name' => 'ui/ux', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => '48457ac4-d3b9-4dea-8270-267d21dc6b79', 'name' => 'machine learning', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => '18019df9-986f-4e4d-8443-5dc090017d69', 'name' => 'unity engineer', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => '32cdb932-739d-44cf-bb62-6340a7890b9d', 'name' => '3d artist', 'created_at' => $timestamp, 'updated_at' => $timestamp]
        ]);
    }
}