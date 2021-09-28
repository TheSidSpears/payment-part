<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\Dalion\DalionApi;
use App\Services\Log;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SberCallback extends Controller {
    private DalionApi $dalion;
    private TransactionService $transactionService;
    private Log $log;

    protected Transaction $transaction;
    protected string $status;
    protected string $operation;

    public function __construct(DalionApi $dalion, TransactionService $transactionService, Log $log) {
        $this->dalion = $dalion;
        $this->log = $log;
        $this->transactionService = $transactionService;
    }

    public function __invoke(Request $request) {
        $this->log->sberCallback($request);
        $this->initVars($request);

        $this->updateTransactionStatus();

        if ($this->paymentApproved()) {
            $this->sendPaymentApproveToDalion();
            $this->sendPaymentApproveToMerchant();
        }

        return response(''); // не убирать значения по умолчанию, иначе вернет ResponseFactory
    }

    protected function initVars(Request $request): void {
        $this->transaction = Transaction::findOrFail($request->get('orderNumber'));
        $this->status = $request->get('status');
        $this->operation = $request->get('operation');
    }

    protected function paymentApproved(): bool {
        return $this->status === '1' && $this->operation === 'approved';
    }

    protected function updateTransactionStatus(): void {
        $this->transaction->status = $this->operation;
        $this->transaction->save();
    }

    protected function sendPaymentApproveToDalion(): void {
        $handled = $this->dalion->sendPaymentApprove($this->transaction);
        $this->log->dalionPaymentApproveStatus($this->transaction, $handled);
    }

    protected function sendPaymentApproveToMerchant(): void {
        $response = Http::asJson()
            ->acceptJson()
            ->post(
                $this->transaction->merchant->payment_approve_url,
                $this->transactionService->orderStatus($this->transaction)
            );

        $this->log->merchantPaymentHandlingStatus($response, $this->transaction);
    }
}
