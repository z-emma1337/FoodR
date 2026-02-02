<?php

namespace App\Http\Controllers;

use App\Models\Interakciok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterakcioController extends Controller
{
    /**
     * Recept likeolása
     * 
     * Ez a metódus fut le, amikor a felhasználó jobbra húzza a kártyát (LIKE)
     */
    public function likeRecept(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Bejelentkezés szükséges'], 401);
        }

        // Validáljuk a bejövő adatot
        $request->validate([
            'recept_id' => 'required|exists:recept,id'
        ]);

        // Lekérjük a bejelentkezett felhasználó ID-ját
        $felhasznaloId = Auth::id();

        // updateOrCreate: 
        // - Ha már van ilyen interakció (ugyanaz a felhasználó + recept) -> FRISSÍTI
        // - Ha nincs még -> LÉTREHOZZA
        $interakcio = Interakciok::updateOrCreate(
            [
                // Ezekkel a feltételekkel KERESI a rekordot
                'felhasznalo_id' => $felhasznaloId,
                'recept_id' => $request->recept_id
            ],
            [
                // Ha megtalálta -> ezeket az értékeket FRISSÍTI
                // Ha nem találta -> ezekkel az értékekkel LÉTREHOZZA
                'liked' => 1, // 1 = LIKE
                'mentett' => 0, // Egyelőre nem mentjük
                'vote' => 0 // Egyelőre nincs szavazás
            ]
        );


    }

    /**
     * Recept dislike-olása
     * 
     * Ez a metódus fut le, amikor a felhasználó balra húzza a kártyát (DISLIKE)
     */
    public function dislikeRecept(Request $request)
    {
        // Validáljuk a bejövő adatot
        $request->validate([
            'recept_id' => 'required|exists:recept,id'
        ]);

        $felhasznaloId = Auth::id();

        $interakcio = Interakciok::updateOrCreate(
            [
                'felhasznalo_id' => $felhasznaloId,
                'recept_id' => $request->recept_id
            ],
            [
                'liked' => 2, // 2 = DISLIKE
                'mentett' => 0,
                'vote' => 0
            ]
        );

    }

    /**
     * Interakció lekérdezése (opcionális)
     * 
     * Ezzel megnézheted, hogy egy receptet már likeolt/dislikeolt-e a user
     */
    public function getInterakcio($receptId)
    {
        $felhasznaloId = Auth::id();

        $interakcio = Interakciok::where('felhasznalo_id', $felhasznaloId)
            ->where('recept_id', $receptId)
            ->first();

        if (!$interakcio) {
            return response()->json([
                'success' => true,
                'liked' => 0, // Még nincs interakció
                'message' => 'Nincs még interakció'
            ]);
        }

    }
}