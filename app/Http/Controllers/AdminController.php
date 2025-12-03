<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Direction;
use App\Models\TypeAlerte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

        $days = collect(range(6, 0))->map(fn (int $offset): Carbon => Carbon::today()->subDays($offset));
        $trend = $days->map(fn (Carbon $day): array => [
            'label' => $day->isoFormat('DD MMM'),
            'value' => Alerte::whereDate('created_at', $day)->count(),
        ]);

        $statusBreakdown = [
            'En attente' => $stats['alertes_en_attente'],
            'En cours' => $stats['alertes_en_cours'],
            'Terminées' => $stats['alertes_termine'],
        ];

        $recentAlertes = Alerte::with(['type', 'direction'])
            ->latest()
            ->limit(5)
            ->get();

        $topTypes = TypeAlerte::withCount('alertes')
            ->orderByDesc('alertes_count')
            ->limit(4)
            ->get();

        return view('admin.dashboard', compact('stats', 'trend', 'statusBreakdown', 'recentAlertes', 'topTypes'));
    }
}


