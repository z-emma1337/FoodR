<?php

namespace App\Http\Middleware;

use App\Models\Interakciok;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        $user = $request->user();
        $likedCount = 0;

        if ($user) {
            $likedCount = Interakciok::where('felhasznalo_id', $user->id)
                ->where('liked', 1)
                ->count();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'nev' => $user->nev,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                ] : null,
            ],
            'likedCount' => $likedCount,
        ];
    }
}