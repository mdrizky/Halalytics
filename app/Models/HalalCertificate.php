<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalalCertificate extends Model
{
    protected $fillable = [
        'certificate_number', 'product_id', 'product_name',
        'manufacturer', 'issuing_body', 'issued_at', 'expires_at',
        'status', 'certificate_file', 'qr_data',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
        'qr_data'    => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)  { return $query->where('status', 'active'); }
    public function scopeExpired($query) { return $query->where('status', 'expired'); }
    public function scopeRevoked($query) { return $query->where('status', 'revoked'); }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '<=', now()->addDays($days))
                     ->where('expires_at', '>', now());
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    /** Verify a certificate by its number, returning status + details. */
    public static function verify(string $certNumber): ?array
    {
        $cert = static::where('certificate_number', $certNumber)->first();

        if (!$cert) return null;

        // Auto-update expired certificates
        if ($cert->status === 'active' && $cert->expires_at->isPast()) {
            $cert->update(['status' => 'expired']);
        }

        return [
            'valid'              => $cert->is_valid,
            'certificate_number' => $cert->certificate_number,
            'product_name'       => $cert->product_name,
            'manufacturer'       => $cert->manufacturer,
            'issuing_body'       => $cert->issuing_body,
            'issued_at'          => $cert->issued_at->format('d M Y'),
            'expires_at'         => $cert->expires_at->format('d M Y'),
            'status'             => $cert->status,
            'days_until_expiry'  => $cert->days_until_expiry,
        ];
    }
}
