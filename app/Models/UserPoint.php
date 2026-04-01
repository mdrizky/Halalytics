<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id', 'points', 'source', 'description', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'points'   => 'integer',
    ];

    // ── Relationships ──────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // ── Scopes ─────────────────────────────────────
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeThisMonth($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }

    // ── Helpers ─────────────────────────────────────
    public static function award(int $userId, int $points, string $source, string $description, array $metadata = []): self
    {
        return static::create([
            'user_id'     => $userId,
            'points'      => $points,
            'source'      => $source,
            'description' => $description,
            'metadata'    => $metadata ?: null,
        ]);
    }

    public static function totalForUser(int $userId): int
    {
        return (int) static::forUser($userId)->sum('points');
    }

    public static function levelForPoints(int $points): array
    {
        return match (true) {
            $points >= 10000 => ['name' => 'Legenda',    'badge' => 'diamond',  'min' => 10000, 'next' => null],
            $points >= 5000  => ['name' => 'Master',     'badge' => 'gold',     'min' => 5000,  'next' => 10000],
            $points >= 2000  => ['name' => 'Ahli',       'badge' => 'purple',   'min' => 2000,  'next' => 5000],
            $points >= 500   => ['name' => 'Penjelajah', 'badge' => 'blue',     'min' => 500,   'next' => 2000],
            default          => ['name' => 'Pemula',     'badge' => 'green',    'min' => 0,     'next' => 500],
        };
    }
}
