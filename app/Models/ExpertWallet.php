<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertWallet extends Model
{
    protected $fillable = ['expert_id', 'balance', 'total_earned'];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'expert_wallet_id');
    }
}
