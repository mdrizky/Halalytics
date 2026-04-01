<?php

use App\Models\ConsultationSession;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) ($user->id_user ?? $user->id) === (int) $id;
});

Broadcast::channel('consultation.{sessionId}', function ($user, $sessionId) {
    $session = ConsultationSession::with('specialist')->find($sessionId);

    if (!$session) {
        return false;
    }

    $userId = (int) ($user->id_user ?? $user->id);

    if ((int) $session->user_id === $userId) {
        return [
            'id' => $userId,
            'name' => $user->full_name ?? $user->username,
            'role' => 'user',
        ];
    }

    if ($session->specialist && (int) $session->specialist->user_id === $userId) {
        return [
            'id' => $userId,
            'name' => $user->full_name ?? $user->username,
            'role' => 'specialist',
        ];
    }

    return false;
});
