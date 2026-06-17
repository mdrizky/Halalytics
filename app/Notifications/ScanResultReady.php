<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProductAnalysisResult;

class ScanResultReady extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $analysisResultId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast']; // Or 'fcm' if FCM is fully configured
    }

    public function toMail(object $notifiable): MailMessage
    {
        $result = ProductAnalysisResult::find($this->analysisResultId);
        $productName = $result->product->nama_product ?? 'Produk Anda';
        $verdict = str_replace(['_', ''], [' ', ''], $result->halal_verdict);

        return (new MailMessage)
            ->subject("Hasil Analisis Produk {$productName} Sudah Siap!")
            ->line("Hasil analisis halal dan kesehatan untuk produk **{$productName}** sudah tersedia.")
            ->line("Verdict Halal: **{$verdict}**")
            ->line("Nutri Score: **{$result->nutri_score}**")
            ->action('Lihat Hasil Lengkap', url('/user/scan-history/' . $result->id))
            ->line('Terima kasih telah menggunakan Halalytics!');
    }

    public function toArray(object $notifiable): array
    {
        $result = ProductAnalysisResult::find($this->analysisResultId);
        $productName = $result->product->nama_product ?? 'Produk Anda';
        $verdict = str_replace(['_', ''], [' ', ''], $result->halal_verdict);
        
        // Add this for push notification payload if FCM is used
        return [
            'title' => 'Hasil Scan Produk Siap!',
            'body' => "Analisis untuk {$productName} siap. Status Halal: {$verdict}, Nutri Score: {$result->nutri_score}",
            'action_url' => url('/user/scan-history/' . $result->id),
            'type' => 'scan_result',
            'product_id' => $result->product_id,
            'analysis_id' => $result->id,
        ];
    }
}
