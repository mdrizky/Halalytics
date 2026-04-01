<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifPrefController extends Controller
{
    /** GET /api/user/notification-preferences */
    public function get()
    {
        $prefs = UserNotificationPreference::forUser(Auth::user()->id_user);

        return response()->json([
            'success' => true,
            'data'    => [
                'medication_reminders' => $prefs->medication_reminders,
                'promo_deals'          => $prefs->promo_deals,
                'weekly_report'        => $prefs->weekly_report,
                'favorite_updates'     => $prefs->favorite_updates,
                'new_products'         => $prefs->new_products,
                'watchlist_alerts'     => $prefs->watchlist_alerts,
                'security_alerts'      => $prefs->security_alerts,
            ],
        ]);
    }

    /** PUT /api/user/notification-preferences */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'medication_reminders' => 'sometimes|boolean',
            'promo_deals'          => 'sometimes|boolean',
            'weekly_report'        => 'sometimes|boolean',
            'favorite_updates'     => 'sometimes|boolean',
            'new_products'         => 'sometimes|boolean',
            'watchlist_alerts'     => 'sometimes|boolean',
            'security_alerts'      => 'sometimes|boolean',
        ]);

        $prefs = UserNotificationPreference::forUser(Auth::user()->id_user);
        $prefs->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Preferensi notifikasi berhasil diperbarui.',
            'data'    => $prefs->fresh()->only([
                'medication_reminders', 'promo_deals', 'weekly_report',
                'favorite_updates', 'new_products', 'watchlist_alerts', 'security_alerts',
            ]),
        ]);
    }
}
