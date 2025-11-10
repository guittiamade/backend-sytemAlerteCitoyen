<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index(Request $request)
    {
        $query = Alerte::with(['citoyen', 'gestionnaire', 'direction', 'type']);
        // Filtrer par rôle
        $role = optional($request->user()->profile)->nom;
        if ($role === 'citoyen') {
            $query->where('citoyen_id', $request->user()->id);
        } elseif ($role === 'gestionnaire') {
            // peut voir tout; optionnellement limiter aux gérés
        } elseif ($role === 'direction') {
            $dirId = $request->user()->direction_id;
            if ($dirId) {
                $query->where('direction_id', $dirId);
            }
        }

        // Filtres
        if ($status = $request->query('statut')) {
            $query->where('statut', $status);
        }
        if ($typeId = $request->query('type_alerte_id')) {
            $query->where('type_alerte_id', $typeId);
        }
        // direction_id exposé pour gestionnaire/admin; pour direction le filtre est déjà appliqué ci-dessus
        if (in_array($role, ['gestionnaire','super_admin'])) {
            if ($dir = $request->query('direction_id')) {
                $query->where('direction_id', $dir);
            }
        }
        if ($text = trim((string)$request->query('q'))) {
            $query->where(function ($sub) use ($text) {
                $sub->where('titre', 'like', "%{$text}%")
                    ->orWhere('description', 'like', "%{$text}%")
                    ->orWhere('localisation', 'like', "%{$text}%");
            });
        }
        if ($df = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $df);
        }
        if ($dt = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $dt);
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        return $query->latest()->paginate($perPage)->appends($request->query());
    }

    public function store(Request $request, NotificationService $notifier)
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'string'],
            'localisation' => ['nullable', 'string'],
            'type_alerte_id' => ['required', 'exists:types_alertes,id'],
        ]);
        $data['citoyen_id'] = $request->user()->id;
        $alerte = Alerte::create($data);
        $alerte->histories()->create([
            'user_id' => $request->user()->id,
            'action' => 'created',
            'to_status' => $alerte->statut,
        ]);
        $notifier->notify($request->user()->id, 'Votre signalement a été soumis.', $alerte->id);
        return response()->json($alerte->load('type'), 201);
    }

    public function show(Alerte $alerte)
    {
        return $alerte->load(['citoyen', 'gestionnaire', 'direction', 'type']);
    }

    public function update(Request $request, Alerte $alerte)
    {
        $data = $request->validate([
            'titre' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'photo' => ['sometimes', 'nullable', 'string'],
            'localisation' => ['sometimes', 'nullable', 'string'],
            'type_alerte_id' => ['sometimes', 'exists:types_alertes,id'],
        ]);
        $alerte->update($data);
        return $alerte->refresh();
    }

    public function changerStatut(Request $request, Alerte $alerte, NotificationService $notifier)
    {
        $validated = $request->validate([
            'statut' => ['required', 'in:en_attente,en_cours,termine'],
            'direction_id' => ['required_if:statut,en_cours', 'nullable', 'exists:directions,id'],
        ]);
        $from = $alerte->statut;
        $alerte->fill($validated);
        $actorRole = optional($request->user()->profile)->nom;
        if ($actorRole === 'gestionnaire' && ($validated['statut'] ?? null) === 'en_cours') {
            // Lier le gestionnaire uniquement lors de l'affectation initiale
            $alerte->gestionnaire_id = $request->user()->id;
        }
        $alerte->save();
        $alerte->histories()->create([
            'user_id' => $request->user()->id,
            'action' => 'status_changed',
            'from_status' => $from,
            'to_status' => $alerte->statut,
        ]);
        // Notifications selon workflow
        if ($alerte->statut === 'en_cours') {
            // 1) Affectation à une direction: informer uniquement la direction ciblée et le citoyen
            $directionUsers = User::whereHas('profile', fn($q) => $q->where('nom', 'direction'))
                ->where('direction_id', $alerte->direction_id)
                ->pluck('id')
                ->all();
            if (!empty($directionUsers)) {
                $notifier->notifyMany($directionUsers, 'Nouvelle alerte à traiter', $alerte->id);
            }
            $notifier->notify($alerte->citoyen_id, 'Votre alerte est en cours de traitement.', $alerte->id);
        } elseif ($alerte->statut === 'termine') {
            // 2) Résolution par la direction: informer le gestionnaire pour approbation
            if ($alerte->gestionnaire_id) {
                $notifier->notify($alerte->gestionnaire_id, 'La direction indique que l’alerte est résolue. Merci d’approuver.', $alerte->id);
            } else {
                // fallback: notifier tous les gestionnaires
                $gestionnaires = User::whereHas('profile', fn($q) => $q->where('nom', 'gestionnaire'))
                    ->pluck('id')->all();
                $notifier->notifyMany($gestionnaires, 'Une alerte a été marquée comme résolue. Merci d’approuver.', $alerte->id);
            }
        } else {
            $notifier->notify($alerte->citoyen_id, 'Mise à jour du statut: ' . $alerte->statut, $alerte->id);
        }
        return $alerte->refresh();
    }

    public function approuverResolution(Request $request, Alerte $alerte, NotificationService $notifier)
    {
        // Le gestionnaire confirme la résolution
        $alerte->statut = 'termine';
        $alerte->gestionnaire_id = $request->user()->id;
        $alerte->save();
        $notifier->notify($alerte->citoyen_id, 'Votre alerte est résolue. Merci pour votre signalement.', $alerte->id);
        return $alerte->refresh();
    }
}


