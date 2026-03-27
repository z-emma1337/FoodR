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

     public function show()
    {
        $felhasznalo_allergenek = [];
        if (Auth::id()) {
            $felhasznalo_allergenek = Auth::user()->allergenek()->pluck('allergen_id');
        }

return response()->json($felhasznalo_allergenek);
    }
}