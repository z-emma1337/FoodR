<?php

namespace App\Http\Controllers;

use App\Models\Interakciok;
use App\Models\Recept;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KedvencController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        $favorites = DB::table('recept as r')
            ->join('interakciok as i', 'r.id', '=', 'i.recept_id')
            ->leftJoin('recept_alapanyag as ra', 'r.id', '=', 'ra.recept_id')
            ->leftJoin('alapanyag_allergenek as aa', 'ra.alapanyag_id', '=', 'aa.alapanyag_id')
            ->leftJoin('allergen as al', 'aa.allergen_id', '=', 'al.id')
            ->where('i.felhasznalo_id', $user->id)
            ->where('i.liked', 1)
            ->select(
                'r.id',
                'r.nev',
                'r.leiras',
                'r.ido',
                'r.adag',
                'r.kep_url',
                DB::raw('GROUP_CONCAT(DISTINCT al.id) as allergen_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT al.nev) as allergen_names')
            )
            ->groupBy('r.id', 'r.nev', 'r.leiras', 'r.ido', 'r.adag', 'r.kep_url')
            ->orderBy('i.updated_at', 'desc')
            ->get()
            ->map(function ($recept) {
                $allergenIds = $recept->allergen_ids ? explode(',', $recept->allergen_ids) : [];
                $allergenNames = $recept->allergen_names ? explode(',', $recept->allergen_names) : [];
                
                $allergenek = [];
                foreach ($allergenIds as $index => $id) {
                    if ($id != 6 && $id != 7) {
                        $allergenek[] = $allergenNames[$index];
                    }
                }
                
                $nemVegetarianusSzuro = in_array('6', $allergenIds);
                $nemVeganSzuro = in_array('7', $allergenIds);
                
                if (!$nemVegetarianusSzuro) {
                    $allergenek[] = 'Vegetáriánus';
                }
                
                if (!$nemVeganSzuro && !$nemVegetarianusSzuro) {
                    $allergenek[] = 'Vegán';
                }
                
                return [
                    'id' => $recept->id,
                    'nev' => $recept->nev,
                    'leiras' => $recept->leiras,
                    'ido' => $recept->ido,
                    'adag' => $recept->adag,
                    'kep_url' => $recept->kep_url,
                    'allergenek' => array_unique($allergenek),
                ];
            });

        return response()->json($favorites);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'recept_id' => ['required', 'exists:recept,id']
        ]);

        $user = Auth::user();

        $interaction = Interakciok::where('felhasznalo_id', $user->id)
            ->where('recept_id', $validated['recept_id'])
            ->first();

        if ($interaction) {
            if ($interaction->liked == 1) {
                return back()->with('error', 'Ez a recept már a kedvencek között van.');
            }
            
            $interaction->liked = 1;
            $interaction->save();
        } else {
            $interaction = Interakciok::create([
                'felhasznalo_id' => $user->id,
                'recept_id' => $validated['recept_id'],
                'liked' => 1,
            ]);
        }

        return back()->with('success', 'Recept hozzáadva a kedvencekhez!');
    }

    public function destroy(int $receptId)
    {
        $user = Auth::user();

        $interaction = Interakciok::where('felhasznalo_id', $user->id)
            ->where('recept_id', $receptId)
            ->first();

        if (!$interaction || $interaction->liked != 1) {
            return back()->with('error', 'Ez a recept nem található a kedvencek között.');
        }

        $interaction->liked = 2;
        $interaction->save();

        return back()->with('success', 'Recept eltávolítva a kedvencekből.');
    }

    public function check(int $receptId): JsonResponse
    {
       $user = Auth::user();
       $isFavorite = Interakciok::where('felhasznalo_id', $user->id)
           ->where('recept_id', $receptId)
           ->where('liked', 1)
           ->exists();
       
       return response()->json(['is_favorite' => $isFavorite]);
    }
}