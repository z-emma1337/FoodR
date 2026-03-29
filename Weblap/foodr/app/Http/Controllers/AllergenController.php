<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use App\Models\FelhasznaloAllergenek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllergenController extends Controller
{
    public function index()
    {
        $allergenek = Allergen::select('id', 'nev')->get();
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

    public function felhasznaloallergenhozzaad(Request $request)
    {

        FelhasznaloAllergenek::where('felhasznalo_id', Auth::id())->delete();
        if ($request->allergen_id != null) {
            foreach ($request->allergen_id as $id) {
                FelhasznaloAllergenek::firstOrCreate([
                    'felhasznalo_id' => Auth::id(),
                    'allergen_id' => $id
                ]);
            }
        }



    }
}