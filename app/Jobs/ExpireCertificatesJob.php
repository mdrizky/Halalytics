<?php

namespace App\Jobs;

use App\Models\HalalCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ExpireCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120;

    public function handle(): void
    {
        // Mark expired certificates
        $expiredCount = HalalCertificate::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            Log::info("Marked {$expiredCount} halal certificates as expired.");
        }

        // Alert about soon-to-expire certificates (within 30 days)
        $soonExpiring = HalalCertificate::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(30)])
            ->get();

        foreach ($soonExpiring as $cert) {
            $daysLeft = now()->diffInDays($cert->expires_at);
            Log::info("Certificate #{$cert->certificate_number} for '{$cert->product_name}' expires in {$daysLeft} days.");
        }

        Log::info("Certificate expiry check complete. Expired: {$expiredCount}, Soon expiring: {$soonExpiring->count()}");
    }
}
