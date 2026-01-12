<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Felhasznalo;

class UserController extends Controller
{
    public function checkUsername(Request $request)
    {
        $username = $request->query('username'); // GET paraméter

        $available = !Felhasznalo::where('nev', $username)->exists();

        return response()->json(['available' => $available]);
    }
}
