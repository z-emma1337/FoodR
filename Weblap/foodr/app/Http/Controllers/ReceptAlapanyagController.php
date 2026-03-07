<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptAlapanyagController extends Controller
{

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
                'ra.adag'
            )
            ->orderBy('a.nev')
            ->get();

        $lista = $alapanyagok->map(function ($item) {
            return $item->alapanyag_nev 
                . ($item->adag ? ', ' . $item->adag : '');
        });

        return response()->json($lista);
    }
}