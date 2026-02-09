<?php

namespace App\Http\Middleware;

use App\Models\Interakciok;
use Illuminate\Foundation\Inspiring;
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
                'user' => $request->user(),
            ],
            'likedCount' => $likedCount,
        ];
    }
}