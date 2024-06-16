<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function insert(Request $request)
    {
        Division::create(['name' => $request->name]);
        return response()->json(['message' => 'Succesfully insert Division', 'data' => $request->name]);
    }
}
