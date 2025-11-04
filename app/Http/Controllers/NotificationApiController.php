<?php

namespace App\Http\Controllers;

use App\Models\NotificationCustom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $q = NotificationCustom::query()
            ->where('utilisateur_id', $request->user()->id)
            ->orderByDesc('id');
        if (!is_null($s = $request->query('statut'))) {
            $q->where('statut', (bool)$s);
        }
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        return $q->paginate($perPage)->appends($request->query());
    }

    public function update(Request $request, NotificationCustom $notification)
    {
        $user = $request->user();
        $role = optional($user->profile)->nom;
        if ($notification->utilisateur_id !== $user->id && $role !== 'super_admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'statut' => ['required','boolean'],
        ]);
        $notification->update([
            'statut' => (bool)$data['statut'],
            'date_envoi' => $data['statut'] ? now() : $notification->date_envoi,
        ]);
        return $notification->refresh();
    }
}
