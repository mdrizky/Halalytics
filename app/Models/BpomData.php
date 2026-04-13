<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class BpomData extends Model
{
    protected $table = 'bpom_data';

    protected $fillable = [
        'nomor_reg',
        'kategori',
        'nama_produk',
        'merk',
        'pendaftar',
        'alamat_produsen',
        'kemasan',
        'bentuk_sediaan',
        'tanggal_terbit',
        'masa_berlaku',
        'ingredients_text',
        'analisis_halal',
        'analisis_kandungan',
        'status_keamanan',
        'skor_keamanan',
        'status_halal',
        'sumber_data',
        'image_url',
        'barcode',
        'submitted_by',
        'verification_status',
        'is_verified_manually',
        'verified_by',
        'verified_at',
        'last_synced_at',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'masa_berlaku' => 'date',
        'is_verified_manually' => 'boolean',
        'verified_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'skor_keamanan' => 'integer',
    ];

    // ========== SCOPES ==========

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeAman($query)
    {
        return $query->where('status_keamanan', 'aman');
    }

    public function scopeBahaya($query)
    {
        return $query->where('status_keamanan', 'bahaya');
    }

    // ========== RELATIONSHIPS ==========

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by', 'id_user');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }

    // ========== HELPERS ==========

    public function getAnalisisHalalArrayAttribute()
    {
        return json_decode($this->analisis_halal, true) ?? [];
    }

    public function getAnalisisKandunganArrayAttribute()
    {
        return json_decode($this->analisis_kandungan, true) ?? [];
    }

    public function isExpired()
    {
        if (!$this->masa_berlaku) return false;
        return $this->masa_berlaku->isPast();
    }

    public function getStatusBadgeColor()
    {
        return match ($this->status_keamanan) {
            'aman' => 'green',
            'waspada' => 'yellow',
            'bahaya' => 'red',
            default => 'gray',
        };
    }

    public function getImageUrlAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->nama_produk,
            'brand' => $this->merk,
            'barcode' => $this->barcode,
            'category' => $this->kategori,
        ], in_array($this->kategori, ['kosmetik', 'beauty'], true) ? 'cosmetic' : 'bpom');
    }
}
