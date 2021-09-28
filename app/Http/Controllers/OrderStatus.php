<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\Log;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class OrderStatus extends Controller {

    private Log $log;
    public function __construct(Log $log) {
        $this->log = $log;
    }

    public function byTransaction(Transaction $transaction, TransactionService $transactionService): JsonResponse {
        $this->log->orderStatus($transaction);

        return response()->json(
            $transactionService->orderStatus($transaction)
        );
    }

    public function bySberOrderId(string $sberOrderId, TransactionService $transactionService): JsonResponse {
        $transaction = Transaction::query()
            ->where('sber_order_id', $sberOrderId)->firstOrFail();

        return $this->byTransaction($transaction, $transactionService);
    }
}
