<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Direction;
use App\Models\Profile;
use App\Models\TypeAlerte;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAlerteController extends Controller
{
    public function index(Request $request): View
    {
        $q = Alerte::with(['type','direction','gestionnaire'])
            ->orderByDesc('id');

        if ($s = $request->query('statut')) {
            $q->where('statut', $s);
        }
        if ($t = $request->query('type_alerte_id')) {
            $q->where('type_alerte_id', $t);
        }
        if ($d = $request->query('direction_id')) {
            $q->where('direction_id', $d);
        }
        if ($text = trim((string)$request->query('q'))) {
            $q->where(function ($sub) use ($text) {
                $sub->where('titre', 'like', "%{$text}%")
                    ->orWhere('description', 'like', "%{$text}%")
                    ->orWhere('localisation', 'like', "%{$text}%");
            });
        }
        if ($df = $request->query('date_from')) {
            $q->whereDate('created_at', '>=', $df);
        }
        if ($dt = $request->query('date_to')) {
            $q->whereDate('created_at', '<=', $dt);
        }

        $alertes = $q->paginate(15)->withQueryString();
        $types = TypeAlerte::orderBy('nom')->get();
        $directions = Direction::orderBy('description')->get();
        $profilGestionnaire = Profile::where('nom', 'gestionnaire')->first();
        $gestionnaires = $profilGestionnaire
            ? User::where('profile_id', $profilGestionnaire->id)->orderBy('name')->get()
            : collect();

        return view('admin.alertes', compact('alertes','types','directions','gestionnaires'));
    }

    public function show(Alerte $alerte): View
    {
        $alerte->load(['type','direction','gestionnaire','citoyen']);
        $directions = Direction::orderBy('description')->get();
        $profilGestionnaire = Profile::where('nom', 'gestionnaire')->first();
        $gestionnaires = $profilGestionnaire
            ? User::where('profile_id', $profilGestionnaire->id)->orderBy('name')->get()
            : collect();
        return view('admin.alerte_show', compact('alerte','directions','gestionnaires'));
    }

    public function update(Request $request, Alerte $alerte): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['nullable','in:en_attente,en_cours,termine'],
            'direction_id' => ['nullable','integer','exists:directions,id'],
            'gestionnaire_id' => ['nullable','integer','exists:users,id'],
        ]);

        $alerte->update($data);
        return back()->with('ok', 'Alerte mise à jour');
    }
}
