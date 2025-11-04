<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Direction;
use App\Models\TypeAlerte;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'alertes_total' => Alerte::count(),
            'alertes_en_attente' => Alerte::where('statut', 'en_attente')->count(),
            'alertes_en_cours' => Alerte::where('statut', 'en_cours')->count(),
            'alertes_termine' => Alerte::where('statut', 'termine')->count(),
            'directions' => Direction::count(),
            'types' => TypeAlerte::count(),
            'utilisateurs' => User::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}


