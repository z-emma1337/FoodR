<?php

namespace App\Http\Controllers;
use App\Models\kommentek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class KommentController extends Controller
{
    protected $table = 'kommentek';
    public function komment(Request $request)
    {
        $request->validate([
            'recept_id' => ['required', 'exists:recept,id'],
            'szoveg' => ['required', 'string', 'max:1000'],
        ]);

        Kommentek::create([
            'felhasznalo_id' => Auth::id(),
                'felhasznalo_nev' => Auth::user()->nev,
                'pfpurl' => Auth::user()->profilkepurl,
            'recept_id' => $request->recept_id,
            'komment' => $request->szoveg,
        ]);

return redirect()->back();
    }

    public function getKommentek($recept_id)
    {
        $kommentek = Kommentek::where('recept_id', $recept_id)->get();
        return response()->json($kommentek);
    }

    public function deleteKomment($id)
    {
        $komment = Kommentek::findOrFail($id);

        if ($komment->felhasznalo_id !== Auth::id()) {
            return response()->json(['error' => 'Nincs jogosultságod törölni ezt a kommentet.'], 403);
        }

        $komment->delete();

        return redirect()->back();
    }
}