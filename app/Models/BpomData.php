<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;
use Illuminate\Support\Str;

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
        $kategori = Str::lower((string) $this->kategori);
        $imageType = match (true) {
            in_array($kategori, ['kosmetik', 'beauty'], true) => 'cosmetic',
            in_array($kategori, ['obat', 'medicine'], true) => 'medicine',
            in_array($kategori, ['pangan', 'makanan', 'minuman', 'suplemen'], true) => 'product',
            default => 'bpom',
        };

        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->nama_produk,
            'brand' => $this->merk,
            'barcode' => $this->barcode,
            'category' => $this->kategori,
        ], $imageType);
    }

    public function getImageAttribute($value)
    {
        return $this->image_url;
    }

    public function getNamaProductAttribute($value)
    {
        return $this->nama_produk;
    }

    public function getBrandAttribute($value)
    {
        return $this->merk;
    }

    public function getStatusAttribute($value)
    {
        return $this->status_halal ?? $this->status_keamanan ?? 'syubhat';
    }

    public function getKomposisiAttribute($value)
    {
        return $this->ingredients_text ?: $this->analisis_kandungan;
    }

    public function getInfoGiziAttribute($value)
    {
        return $this->analisis_kandungan ?: $this->analisis_halal;
    }

    public function getCategoryNameAttribute($value)
    {
        return $this->kategori;
    }

    public function getSourceLabelAttribute($value)
    {
        return 'BPOM / External';
    }

    public function getImageFallbackUrlAttribute($value): string
    {
        return app(DisplayImageService::class)->fallbackUrl($this->kategori, 'bpom');
    }
}
