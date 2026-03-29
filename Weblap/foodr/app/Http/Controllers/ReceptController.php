<?php

namespace App\Http\Controllers;

use App\Models\Recept;
use App\Models\Alapanyag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptController extends Controller
{

public function index(Request $request)
{

    if ($request->isMethod('post')) {
        $recipeId = $request->input('recipe_id');

        if ($recipeId && Auth::check()) {
            Recept::findOrFail($recipeId)
                ->interakciok()
                ->where('felhasznalo_id', Auth::id())
                ->update(['liked' => 0]);
        }

    }

    return Recept::with([
        'receptAlapanyagok.alapanyag.allergenek',
        'interakciok',
    ])
        ->get()
        ->map(function ($recept) {

            $osszesAllergen = $recept->receptAlapanyagok
                ->pluck('alapanyag.allergenek')
                ->flatten()
                ->unique('id');

            $allergenek = $osszesAllergen
                ->whereNotIn('id', [6, 7])
                ->pluck('nev')
                ->values();

            $allergenIds = $osszesAllergen
                ->whereNotIn('id', [6, 7])
                ->pluck('id')
                ->values();

            // Diéta logika
            $dietTags = [];
            if ($osszesAllergen->where('id', 6)->isEmpty()) { // nincs hús -> vegetáriánus
                $dietTags[] = ['nev' => 'Vegetáriánus', 'allergen_id' => 6];
            }
            if ($osszesAllergen->where('id', 7)->isEmpty() && $osszesAllergen->where('id', 6)->isEmpty()) { // nincs tej, tojás stb. -> vegán
                $dietTags[] = ['nev' => 'Vegán', 'allergen_id' => 7];
            }

            $hozzavalok = $recept->receptAlapanyagok->map(fn($ra) => [
                'nev' => $ra->alapanyag->nev,
                'adag' => $ra->adag ?? 'ízlés szerint'
            ]);

            $felhasznaloId = Auth::id() ?? 0;

            $liked = $recept->interakciok
                ->where('felhasznalo_id', $felhasznaloId)
                ->first()
                ->liked ?? 0;

            return [
                'id' => $recept->id,
                'nev' => $recept->nev,
                'leiras' => $recept->leiras,
                'ido' => $recept->ido,
                'adag' => $recept->adag,
                'kep_url' => $recept->kep_url,
                'allergenek' => $allergenek->toArray(),
                'allergen_id' => array_merge($allergenIds->toArray(), array_column($dietTags, 'allergen_id')),
                'hozzavalok' => $hozzavalok,
                'liked' => $liked,
                'felhasznalo_id' => $recept->felhasznalo_id
            ];
        });
}
    public function ReceptHozzaadasa(Request $request)
    {

        $request->validate([
            'receptNev' => 'required|string|max:255',
            'receptIdo' => 'required|integer|min:1',
            'receptAdag' => 'required|integer|min:1',
            'receptHozzavalok' => 'required|array',
            'receptLeirasok' => 'required|array',
            'kep' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
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
        $recept->update(['kep_url' => '/imgs/Receptek/' . $kepnev]);


        foreach ($request->receptHozzavalok as $hozzavalo) {
            $alapanyag = Alapanyag::firstOrCreate([
                'nev' => $hozzavalo['nev']
            ]);

            $recept->alapanyagok()->syncWithoutDetaching([
                $alapanyag->id => ['adag' => $hozzavalo['adag']]
            ]);
        }

        return redirect()->back();
    }
}