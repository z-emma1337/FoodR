<?php

namespace App\Http\Controllers;

use App\Models\Recept;
use App\Models\Alapanyag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptController extends Controller
{
    public function ReceptHozzaadasa(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Bejelentkezés szükséges'], 401);
        }

        $request->validate([
            'receptNev' => 'required|string|max:255',
            'receptIdo' => 'required|integer|min:1',
            'receptAdag' => 'required|integer|min:1',
            'receptHozzavalok' => 'required|array',
            'receptLeirasok' => 'required|array',
            'kep' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $felhasznaloId = Auth::id();

        $leiras = '';
        foreach ($request->receptLeirasok as $i => $l) {
            $leiras .= ($i + 1) . ". " . $l . " ";
        }

        $recept = Recept::create([
            'nev' => $request->receptNev,
            'leiras' => $leiras,
            'ido' => $request->receptIdo,
            'adag' => $request->receptAdag,
            'felhasznalo_id' => $felhasznaloId,
        ]);


            $kep = $request->file('kep');
            $kiterj = $kep->getClientOriginalExtension();
            $kepnev = $recept->id . '.' . $kiterj;


            $kep->move(public_path('imgs/Receptek'), $kepnev);

            $recept->update([
                'kep_url' => '/imgs/Receptek/' . $kepnev
            ]);
        

        foreach ($request->receptHozzavalok as $hozzavalo) {
            $alapanyag = Alapanyag::firstOrCreate([
                'nev' => $hozzavalo['nev']
            ]);

            $recept->alapanyagok()->syncWithoutDetaching([
                $alapanyag->id => ['adag' => $hozzavalo['adag']]
            ]);
        }

        return response()->json(['success' => true, 'recept_id' => $recept->id]);
    }
}