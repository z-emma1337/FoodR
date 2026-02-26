<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllergenController extends Controller
{
    public function index()
    {
        $allergenek = Allergen::all()->pluck('nev')->values();
        return response()->json($allergenek);
    }
}