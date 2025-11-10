<?php

namespace App\Services;

use App\Models\NotificationCustom;
use App\Models\User;
use App\Mail\AlertNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notify(int $utilisateurId, string $message, ?int $alerteId = null): void
    {
        NotificationCustom::create([
            'utilisateur_id' => $utilisateurId,
            'message' => $message,
            'alerte_id' => $alerteId,
            'date_envoi' => null,
            'statut' => false,
        ]);

        $user = User::find($utilisateurId);
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->queue(new AlertNotificationMail($message, $alerteId));
            } catch (\Throwable $e) {
                Log::warning('Email notification failed, logged instead', [
                    'user_id' => $utilisateurId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Notify multiple users at once.
     *
     * @param array<int> $userIds
     */
    public function notifyMany(array $userIds, string $message, ?int $alerteId = null): void
    {
        foreach (array_unique($userIds) as $uid) {
            $this->notify((int) $uid, $message, $alerteId);
        }
    }
}


