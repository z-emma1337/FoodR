<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptAlapanyagController extends Controller
{
    /**
     * GET /recept-alapanyagok?recept_id=123
     * Visszaadja a recept összes alapanyagát egyszerű string listaként:
     * ["Csirke, 500 g", "Hagyma, 2 db", "Rizs, 300 g", ...]
     */
    public function index(Request $request)
    {
        $request->validate([
            'recept_id' => 'required|integer|exists:recept,id',
        ]);

        $alapanyagok = DB::table('recept_alapanyag as ra')
            ->join('alapanyag as a', 'ra.alapanyag_id', '=', 'a.id')
            ->where('ra.recept_id', $request->recept_id)
            ->select(
                'a.nev as alapanyag_nev',
                'ra.adag'                     // ← a te jelenlegi oszlopneved
            )
            ->orderBy('a.nev')
            ->get();

        // Egyszerű "Név, adag" lista (null adag esetén csak a név jelenik meg)
        $lista = $alapanyagok->map(function ($item) {
            return $item->alapanyag_nev 
                . ($item->adag ? ', ' . $item->adag : '');
        });

        return response()->json($lista);
    }
}