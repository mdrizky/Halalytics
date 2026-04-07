<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use App\Models\ExpertWallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $wallet = $this->walletForUser($request->user()->id_user);

        return $this->successResponse([
            'balance' => (int) $wallet->balance,
            'total_earned' => (int) $wallet->total_earned,
        ], 'Saldo wallet berhasil diambil.');
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1000',
            'bank_name' => 'nullable|string|max:100',
            'account_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
        ]);

        $wallet = $this->walletForUser($request->user()->id_user);

        if ($validated['amount'] > $wallet->balance) {
            return $this->errorResponse('Saldo tidak mencukupi untuk penarikan.', 422);
        }

        $transaction = DB::transaction(function () use ($wallet, $validated) {
            $wallet->decrement('balance', $validated['amount']);

            return WalletTransaction::create([
                'expert_wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $validated['amount'],
                'description' => trim(sprintf(
                    'Permintaan tarik dana%s%s',
                    ! empty($validated['bank_name']) ? ' ke ' . $validated['bank_name'] : '',
                    ! empty($validated['account_number']) ? ' (' . $validated['account_number'] . ')' : ''
                )),
                'reference_id' => 'withdraw:' . now()->timestamp,
            ]);
        });

        return $this->successResponse([
            'balance' => (int) $wallet->fresh()->balance,
            'transaction' => $this->transactionPayload($transaction),
        ], 'Permintaan tarik dana berhasil dibuat.');
    }

    public function transactions(Request $request)
    {
        $wallet = $this->walletForUser($request->user()->id_user);

        $transactions = $wallet->transactions()
            ->latest()
            ->get()
            ->map(fn (WalletTransaction $transaction) => $this->transactionPayload($transaction));

        return $this->successResponse($transactions, 'Riwayat wallet berhasil diambil.');
    }

    private function walletForUser(int $userId): ExpertWallet
    {
        $expert = Expert::firstOrCreate(
            ['user_id' => $userId],
            ['specialization' => 'Konsultan Umum', 'price_per_session' => 0]
        );

        return ExpertWallet::firstOrCreate(
            ['expert_id' => $expert->id],
            ['balance' => 0, 'total_earned' => 0]
        );
    }

    private function transactionPayload(WalletTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => (int) $transaction->amount,
            'description' => $transaction->description,
            'reference_id' => $transaction->reference_id,
            'created_at' => optional($transaction->created_at)->toISOString(),
        ];
    }
}
