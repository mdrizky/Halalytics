<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['expert_wallet_id', 'type', 'amount', 'description', 'reference_id'];

    public function wallet()
    {
        return $this->belongsTo(ExpertWallet::class, 'expert_wallet_id');
    }
}
