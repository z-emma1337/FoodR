<?php

namespace App\Http\Controllers;

use App\Models\Recept;                // Importáljuk a Recept modellt az adatbázis lekérdezésekhez (recept tábla)
use App\Models\Alapanyag;            // Importáljuk az Alapanyag modellt (alapanyag tábla)
use App\Models\ReceptAlapanyag;      // Importáljuk a ReceptAlapanyag modellt (pivot tábla a recept és alapanyag között)
use Illuminate\Http\Request;         // Importáljuk a Request osztályt a bemeneti adatok kezeléséhez és validációhoz
use Illuminate\Support\Facades\DB;   // Importáljuk a DB facade-et nyers SQL lekérdezésekhez (Query Builder)
use Illuminate\Validation\Rule;      // Importáljuk a Rule osztályt speciális validációs szabályokhoz (pl. unique kombináció)

// Ez a controller kezeli a recept_alapanyag pivot táblát, ami összeköti a recepteket az alapanyagokkal.
// Mivel az adatbázisban NINCS felhasznalo_id oszlop sem a recept, sem a recept_alapanyag táblában,
// ezért NINCS tulajdonosi ellenőrzés (bármely felhasználó bármely recept alapanyagait kezelheti).
// Ha ez nem kívánt, akkor hozzá kell adni felhasznalo_id-t a recept táblához (lásd korábbi javaslatok).
// A controller RESTful API végpontokat definiál: index (lista), store (létrehozás), show (lekérdezés), update (módosítás), destroy (törlés).
// Minden válasz JSON formátumú, ami alkalmas API-hoz (pl. frontend vagy mobil app számára).
// A leírásod alapján: az index metódusban lekéred a hozzávalókat egy recepthez, és kiírod, miből hány gramm kell.
// Ezért az index fogja ezt kezelni (egy adott recept_id alapján listázza az alapanyagokat és mennyiségeket).
class ReceptAlapanyagController extends Controller
{
    /**
     * GET /recept-alapanyagok?recept_id=123
     * Lista: egy adott recepthez tartozó összes alapanyagot ad vissza.
     * Bemenet: recept_id a query stringben (pl. ?recept_id=123).
     * Kimenet: JSON-ben a recept alapinfói + az alapanyagok listája (nevükkel és mennyiségükkel).
     * Részletes működés:
     * 1. Validálja a recept_id-t (kötelező, egész szám, létező recept).
     * 2. Lekérdezi a receptet (csak az ID és név kell a válaszhoz).
     * 3. Lekérdezi az összes hozzárendelt alapanyagot a pivot táblából (join az alapanyag nevéhez).
     * 4. Rendezés: alapanyag neve szerint ABC-ben.
     * 5. Visszaadja JSON-ben, pl.:
     *    {
     *      "recept": {"id": 123, "nev": "Csirkepaprikás"},
     *      "alapanyagok": [
     *        {"id": 1, "alapanyag_id": 5, "alapanyag_nev": "Csirke", "mennyiseg": "500 g", ...}
     *      ]
     *    }
     * Ez pont azt csinálja, amit kértél: lekéred a hozzávalókat és kiírod, miből hány gramm kell.
     */
    public function index(Request $request)
    {
        // 1. Validáció: ellenőrizzük a bemeneti recept_id-t.
        // required: muszáj megadni (pl. GET query-ben).
        // integer: csak egész szám lehet.
        // exists: létező ID legyen a recept táblában (ha nincs → 422 hiba).
        $request->validate([
            'recept_id' => 'required|integer|exists:recept,id',
        ]);

        // 2. Lekérdezzük a receptet az ID alapján.
        // findOrFail: ha nincs ilyen ID → 404 Not Found hiba.
        $recept = Recept::findOrFail($request->recept_id);

        // 3. Lekérdezzük az összes alapanyagot ehhez a recepthez.
        // DB::table használata: nyers Query Builder, mert egyszerű join és select kell.
        // 'as ra': alias a recept_alapanyag táblának.
        // join: összekötjük az alapanyag táblával az ID-n keresztül, hogy megkapjuk az alapanyag nevét.
        // where: csak ehhez a recept_id-hez tartozó rekordok.
        // select: csak a szükséges mezők (pivot ID, alapanyag ID és név, mennyiség, timestamp-ek).
        // orderBy: ABC sorrend az alapanyag neve szerint.
        // get(): végrehajtja a lekérdezést, collection-t ad vissza.
        $alapanyagok = DB::table('recept_alapanyag as ra')
            ->join('alapanyag as a', 'ra.alapanyag_id', '=', 'a.id')
            ->where('ra.recept_id', $recept->id)
            ->select(
                'a.nev as alapanyag_nev',   // Alapanyag neve (a join-ból).
                'ra.mennyiseg',             // Mennyiség (pl. "200 g").
            )
            ->orderBy('a.nev')
            ->get();

        // 4. JSON válasz visszaadása.
        // response()->json: HTTP 200 OK státusszal.
        // 'recept': csak ID és név (nem az egész modell, hogy ne legyen túl sok adat).
        // 'alapanyagok': a lista, ahol látható, miből hány gramm kell.
        return response()->json([
            'recept' => $recept->only(['id', 'nev']),
            'alapanyagok' => $alapanyagok,
        ]);
    }

    /**
     * POST /recept-alapanyagok
     * Új alapanyag hozzárendelése egy recepthez.
     * Bemenet: JSON body-ben: recept_id, alapanyag_id, mennyiseg (opcionális).
     * Részletes működés:
     * 1. Validálja a bemenetet (recept_id és alapanyag_id kötelező, létezzenek; mennyiség max 50 char; unique párosítás).
     * 2. Létrehozza a pivot rekordot.
     * 3. Visszaadja az új rekord adatait JSON-ben 201 Created státusszal.
     * Nincs tulajdonosi ellenőrzés, mert nincs felhasznalo_id.
     */
    public function store(Request $request)
    {
        // 1. Validáció: ellenőrizzük a POST body-t.
        // recept_id: kötelező, egész, létező recept.
        // alapanyag_id: kötelező, egész, létező alapanyag.
        // mennyiseg: opcionális, string, max 50 char (pl. "200 g", "ízlés szerint").
        // unique szabály: egy recepthez egy alapanyag csak egyszer szerepelhet (Rule::unique + where kombinációval).
        $validated = $request->validate([
            'recept_id'     => 'required|integer|exists:recept,id',
            'alapanyag_id'  => 'required|integer|exists:alapanyag,id',
            'mennyiseg'     => 'nullable|string|max:50',
            'alapanyag_id'  => Rule::unique('recept_alapanyag')
                ->where('recept_id', $request->recept_id),  // Ha már van ilyen páros → 422 hiba.
        ]);

        // 2. Létrehozzuk a pivot rekordot az Eloquent modellen keresztül.
        // create: beszúrja az új rekordot, auto-increment ID-t kap.
        // mennyiseg: ha nem jött → null.
        $pivot = ReceptAlapanyag::create([
            'recept_id'    => $validated['recept_id'],
            'alapanyag_id' => $validated['alapanyag_id'],
            'mennyiseg'    => $validated['mennyiseg'] ?? null,
        ]);

        // 3. Lekérdezzük az alapanyag nevét a válaszhoz (nem muszáj, de hasznos a frontendnek).
        $alapanyag = Alapanyag::find($pivot->alapanyag_id);

        // 4. JSON válasz 201 Created státusszal.
        return response()->json([
            'message' => 'Alapanyag sikeresen hozzáadva a recepthez',
            'data' => [
                'id'            => $pivot->id,
                'alapanyag_id'  => $alapanyag->id,
                'alapanyag_nev' => $alapanyag->nev,
                'mennyiseg'     => $pivot->mennyiseg,
            ]
        ], 201);
    }

    /**
     * GET /recept-alapanyagok/{id}
     * Egy konkrét hozzárendelés (pivot rekord) adatainak lekérdezése.
     * Bemenet: {id} az URL-ben (route model binding).
     * Kimenet: JSON-ben a rekord részletei (beleértve az alapanyag nevét).
     * Részletes működés:
     * 1. Route model binding: Laravel automatikusan lekérdezi a ReceptAlapanyag modellt az ID alapján (ha nincs → 404).
     * 2. Lekérdezi az alapanyag nevét a reláción keresztül (feltételezve, hogy van belongsTo az Alapanyag modellben).
     * 3. Visszaadja JSON-ben.
     */
    public function show(ReceptAlapanyag $receptAlapanyag)
    {
        // 1. Lekérdezzük az alapanyagot a reláción keresztül.
        // $receptAlapanyag->alapanyag: belongsTo reláció (kell definiálni a modellben: public function alapanyag() { return $this->belongsTo(Alapanyag::class); }).
        $alapanyag = $receptAlapanyag->alapanyag;

        // 2. JSON válasz.
        return response()->json([
            'id'            => $receptAlapanyag->id,
            'recept_id'     => $receptAlapanyag->recept_id,
            'alapanyag_id'  => $receptAlapanyag->alapanyag_id,
            'alapanyag_nev' => $alapanyag->nev,
            'mennyiseg'     => $receptAlapanyag->mennyiseg,
        ]);
    }

    /**
     * PATCH/PUT /recept-alapanyagok/{id}
     * Hozzárendelés módosítása (főleg a mennyiség frissítése).
     * Bemenet: {id} URL-ben + JSON body-ben: mennyiseg (opcionális).
     * Részletes működés:
     * 1. Route model binding: lekérdezi a rekordot.
     * 2. Validálja a mennyiséget.
     * 3. Frissíti a rekordot (ha nem jött új érték → marad a régi).
     * 4. Visszaadja az frissített adatokat JSON-ben.
     */
    public function update(Request $request, ReceptAlapanyag $receptAlapanyag)
    {
        // 1. Validáció: csak a mennyiséget ellenőrizzük.
        $validated = $request->validate([
            'mennyiseg' => 'nullable|string|max:50',
        ]);

        // 2. Frissítjük a rekordot.
        // update: menti az adatbázisba.
        // ?? operátor: ha nincs új érték → marad a régi.
        $receptAlapanyag->update([
            'mennyiseg' => $validated['mennyiseg'] ?? $receptAlapanyag->mennyiseg,
        ]);

        // 3. Lekérdezzük az alapanyag nevét.
        $alapanyag = $receptAlapanyag->alapanyag;

        // 4. JSON válasz.
        return response()->json([
            'message' => 'Mennyiség sikeresen módosítva',
            'data' => [
                'id'            => $receptAlapanyag->id,
                'alapanyag_nev' => $alapanyag->nev,
                'mennyiseg'     => $receptAlapanyag->mennyiseg,
            ]
        ]);
    }

    /**
     * DELETE /recept-alapanyagok/{id}
     * Alapanyag eltávolítása a receptből (pivot rekord törlése).
     * Bemenet: {id} URL-ben.
     * Részletes működés:
     * 1. Route model binding: lekérdezi a rekordot.
     * 2. Lekérdezi az alapanyag nevét a válaszhoz.
     * 3. Törli a rekordot (delete: hard delete, mert nincs soft delete feltételezve).
     * 4. Visszaadja a siker üzenetet JSON-ben.
     */
    public function destroy(ReceptAlapanyag $receptAlapanyag)
    {
        // 1. Lekérdezzük az alapanyag nevét az üzenethez.
        $alapanyagNev = $receptAlapanyag->alapanyag->nev;

        // 2. Töröljük a rekordot.
        $receptAlapanyag->delete();

        // 3. JSON válasz.
        return response()->json([
            'message' => "Alapanyag eltávolítva a receptből: {$alapanyagNev}"
        ]);
    }

    // A create és edit metódusok üresen maradnak, mert API-ban nincs szükség formokra (frontend kezeli).
    // Ha webes felület kell, akkor ide jöhetne view return.
    public function create()
    {
        //
    }

    public function edit(ReceptAlapanyag $receptAlapanyag)
    {
        //
    }
}