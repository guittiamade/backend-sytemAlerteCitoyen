<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\NotificationCustom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $q = NotificationCustom::with(['utilisateur', 'alerte'])->orderByDesc('id');
        if ($u = $request->query('utilisateur_id')) {
            $q->where('utilisateur_id', $u);
        }
        if ($a = $request->query('alerte_id')) {
            $q->where('alerte_id', $a);
        }
        if (!is_null($s = $request->query('statut'))) {
            $q->where('statut', (bool)$s);
        }
        $notifications = $q->paginate(15)->withQueryString();
        $users = User::orderBy('name')->get(['id','name']);
        $alertes = Alerte::orderByDesc('id')->limit(200)->get(['id','titre']);
        return view('admin.notifications', compact('notifications','users','alertes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required','string'],
            'utilisateur_id' => ['required','integer','exists:users,id'],
            'alerte_id' => ['nullable','integer','exists:alertes,id'],
            'envoyer' => ['nullable','boolean'],
        ]);

        $payload = [
            'message' => $data['message'],
            'utilisateur_id' => $data['utilisateur_id'],
            'alerte_id' => $data['alerte_id'] ?? null,
            'statut' => (bool)($data['envoyer'] ?? false),
            'date_envoi' => ($data['envoyer'] ?? false) ? Carbon::now() : null,
        ];
        NotificationCustom::create($payload);
        return redirect()->route('admin.notifications')->with('ok','Notification créée');
    }

    public function update(Request $request, NotificationCustom $notification): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required','string'],
            'statut' => ['nullable','boolean'],
        ]);
        $notification->update([
            'message' => $data['message'],
            'statut' => (bool)($data['statut'] ?? $notification->statut),
            'date_envoi' => ($data['statut'] ?? null) ? Carbon::now() : $notification->date_envoi,
        ]);
        return back()->with('ok','Notification mise à jour');
    }

    public function destroy(NotificationCustom $notification): RedirectResponse
    {
        $notification->delete();
        return back()->with('ok','Notification supprimée');
    }
}
