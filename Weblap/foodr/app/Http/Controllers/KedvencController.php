<?php

namespace App\Http\Controllers;

use App\Models\Kedvenc;
use App\Models\Recept;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class KedvencController extends Controller
{
    /**
     * Get all favorites for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        $favorites = Kedvenc::where('felhasznalo_id', $user->id)
            ->with(['recept.receptAlapanyagok.alapanyag.allergenek'])
            ->get()
            ->map(function ($kedvenc) {
                $recept = $kedvenc->recept;
                
                // Ugyanaz a logika, mint a /recipes endpoint-ban
                $osszesAllergen = $recept->receptAlapanyagok
                    ->pluck('alapanyag.allergenek')
                    ->flatten()
                    ->unique('id');
                
                $allergenek = $osszesAllergen
                    ->whereNotIn('id', [6, 7])
                    ->pluck('nev')
                    ->values();
                
                $nemVegetarianusSzuro = $osszesAllergen->where('id', 6)->isNotEmpty();
                $nemVeganSzuro = $osszesAllergen->where('id', 7)->isNotEmpty();
                
                $dietTags = [];
                
                if (!$nemVegetarianusSzuro) {
                    $dietTags[] = 'Vegetáriánus';
                }
                
                if (!$nemVeganSzuro && !$nemVegetarianusSzuro) {
                    $dietTags[] = 'Vegán';
                }
                
                $osszesTag = array_merge($allergenek->toArray(), $dietTags);
                
                return [
                    'id' => $recept->id,
                    'nev' => $recept->nev,
                    'leiras' => $recept->leiras,
                    'ido' => $recept->ido,
                    'adag' => $recept->adag,
                    'kep_url' => $recept->kep_url,
                    'allergenek' => $osszesTag,
                    'kedvenc_id' => $kedvenc->id,
                ];
            });

        return response()->json($favorites);
    }

    /**
     * Add a recipe to favorites.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recept_id' => ['required', 'exists:recept,id']
        ]);

        $user = Auth::user();

        // Check if already favorited
        $exists = Kedvenc::where('felhasznalo_id', $user->id)
            ->where('recept_id', $validated['recept_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ez a recept már a kedvencek között van.'
            ], 409);
        }

        $kedvenc = Kedvenc::create([
            'felhasznalo_id' => $user->id,
            'recept_id' => $validated['recept_id']
        ]);

        return response()->json([
            'message' => 'Recept hozzáadva a kedvencekhez!',
            'kedvenc' => $kedvenc
        ], 201);
    }

    /**
     * Remove a recipe from favorites.
     */
    public function destroy(int $receptId): JsonResponse
    {
        $user = Auth::user();

        $kedvenc = Kedvenc::where('felhasznalo_id', $user->id)
            ->where('recept_id', $receptId)
            ->first();

        if (!$kedvenc) {
            return response()->json([
                'message' => 'Ez a recept nem található a kedvencek között.'
            ], 404);
        }

        $kedvenc->delete();

        return response()->json([
            'message' => 'Recept eltávolítva a kedvencekből.'
        ]);
    }

    /**
     * Check if a recipe is favorited by the user.
     */
    public function check(int $receptId): JsonResponse
    {
        $user = Auth::user();

        $isFavorite = Kedvenc::where('felhasznalo_id', $user->id)
            ->where('recept_id', $receptId)
            ->exists();

        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }
}