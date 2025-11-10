<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function citoyen(Request $request)
    {
        $userId = $request->user()->id;
        return [
            'total' => Alerte::where('citoyen_id', $userId)->count(),
            'en_attente' => Alerte::where('citoyen_id', $userId)->where('statut', 'en_attente')->count(),
            'en_cours' => Alerte::where('citoyen_id', $userId)->where('statut', 'en_cours')->count(),
            'termine' => Alerte::where('citoyen_id', $userId)->where('statut', 'termine')->count(),
        ];
    }

    public function gestionnaire(Request $request)
    {
        $uid = $request->user()->id;
        return [
            'assignes' => Alerte::where('gestionnaire_id', $uid)->count(),
            'en_cours' => Alerte::where('gestionnaire_id', $uid)->where('statut', 'en_cours')->count(),
            'termine' => Alerte::where('gestionnaire_id', $uid)->where('statut', 'termine')->count(),
        ];
    }

    public function direction(Request $request)
    {
        $directionId = $request->user()->direction_id;
        if (!$directionId) {
            return [
                'receptionnes' => 0,
                'en_cours' => 0,
                'termine' => 0,
            ];
        }
        return [
            'receptionnes' => Alerte::where('direction_id', $directionId)->count(),
            'en_cours' => Alerte::where('direction_id', $directionId)->where('statut', 'en_cours')->count(),
            'termine' => Alerte::where('direction_id', $directionId)->where('statut', 'termine')->count(),
        ];
    }

    public function admin()
    {
        return [
            'total' => Alerte::count(),
            'en_attente' => Alerte::where('statut', 'en_attente')->count(),
            'en_cours' => Alerte::where('statut', 'en_cours')->count(),
            'termine' => Alerte::where('statut', 'termine')->count(),
        ];
    }
}


