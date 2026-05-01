<?php

namespace App\Http\Controllers;

use App\Models\Interakciok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterakcioController extends Controller
{

    public function likeRecept(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Bejelentkezés szükséges'], 401);
        }

        $request->validate([
            'recept_id' => 'required|exists:recept,id'
        ]);

        $felhasznaloId = Auth::id();

        Interakciok::updateOrCreate(
            [
                'felhasznalo_id' => $felhasznaloId,
                'recept_id' => $request->recept_id
            ],
            [
                'liked' => 1,
            ]
        );

        return redirect()->back();
    }


    public function dislikeRecept(Request $request)
    {
        $request->validate([
            'recept_id' => 'required|exists:recept,id'
        ]);

        $felhasznaloId = Auth::id();

        Interakciok::updateOrCreate(
            [
                'felhasznalo_id' => $felhasznaloId,
                'recept_id' => $request->recept_id
            ],
            [
                'liked' => 2,
            ]
        );

        return redirect()->back();
    }

    public function UnlikeRecept(Request $request)
    {
        $request->validate([
            'recept_id' => 'required|exists:recept,id'
        ]);

        $felhasznaloId = Auth::id();

        Interakciok::updateOrCreate(
            [
                'felhasznalo_id' => $felhasznaloId,
                'recept_id' => $request->recept_id
            ],
            [
                'liked' => 2,
            ]
        );

        return redirect()->back();
    }


    public function getInterakcio($receptId)
    {
        $felhasznaloId = Auth::id();

        $interakcio = Interakciok::where('felhasznalo_id', $felhasznaloId)
            ->where('recept_id', $receptId)
            ->first();

        if (!$interakcio) {
            return response()->json([
                'success' => true,
                'liked' => 0,
                'message' => 'Nincs még interakció'
            ]);
        }

    }
}