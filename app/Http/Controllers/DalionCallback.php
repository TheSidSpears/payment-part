<?php

namespace App\Http\Controllers;

use App\Exceptions\SberException;
use App\Models\Transaction;
use App\Services\Log;
use App\Services\Sber\SberPaymentApi;
use Illuminate\Http\JsonResponse;

class DalionCallback extends Controller {

    private SberPaymentApi $sberPayment;
    private Log $log;

    public function __construct(SberPaymentApi $sberPayment, Log $log) {
        $this->sberPayment = $sberPayment;
        $this->log = $log;
    }

    public function deposit(Transaction $transaction): JsonResponse {
        $this->log->dalionRequest(Log::DEPOSIT, $transaction);

        try {
            $this->sberPayment->deposit($transaction);

            return response()->json(true);
        } catch (SberException $e) {
            $this->log->sberResponseError(Log::DEPOSIT);
            return response()->json(false);
        }
    }

    public function reverse(Transaction $transaction): JsonResponse {
        $this->log->dalionRequest(Log::REVERSE, $transaction);

        try {
            $this->sberPayment->reverse($transaction);

            return response()->json(true);
        } catch (SberException $e) {
            $this->log->sberResponseError(Log::REVERSE);
            return response()->json(false);
        }
    }

    public function refund(Transaction $transaction): JsonResponse {
        $this->log->dalionRequest(Log::REFUND, $transaction);

        try {
            $this->sberPayment->refund($transaction);

            return response()->json(true);
        } catch (SberException $e) {
            $this->log->sberResponseError(Log::REFUND);
            return response()->json(false);
        }
    }

    public function receipt(Transaction $transaction): JsonResponse {
        $this->log->dalionRequest(Log::RECEIPT, $transaction);

        try {
            $receipt = $this->sberPayment->getReceiptStatus($transaction);

            return response()->json($receipt);
        } catch (SberException $e) {
            $this->log->sberResponseError(Log::RECEIPT);
            return response()->json(false);
        }
    }
}
