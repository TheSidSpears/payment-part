<?php

namespace App\Services;


use App\Exceptions\SberException;
use App\Exceptions\TransactionServiceException;
use App\Http\Requests\RegisterOrder;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\Sber\SberPaymentApi;
use App\Services\Sber\SberRegisterParams;
use Illuminate\Support\Facades\DB;

class TransactionService {
    private SberPaymentApi $sberPayment;

    public function __construct(SberPaymentApi $sberPayment) {
        $this->sberPayment = $sberPayment;
    }

    public function orderStatus(Transaction $transaction): array {
        $sberOrderStatus = $this->sberPayment->getOrderStatus($transaction);

        return [
            'transaction' => $transaction->load('customer'),
            'amount' => $sberOrderStatus['amount'],
            'status' => $sberOrderStatus['actionCodeDescription'] ?: 'Успешно',
        ];
    }

    public function transact(Transaction $transaction, RegisterOrder $request, object $receiptData): string {
        $sberRegisterParams = new SberRegisterParams($request, $transaction->id, $receiptData);

        try {
            $sberResponse = $this->sberPayment->register($transaction, $sberRegisterParams);
        } catch (SberException $e) { throw $e; }

        $transaction->sber_order_id = $sberResponse['orderId'];
        $transaction->save();

        return $sberResponse['formUrl'];
    }

    public function registerNewTransaction(RegisterOrder $request): Transaction {
        try {
            DB::beginTransaction();
            $customer = Customer::create($request->query('customer'));
            $customer->save();

            $transaction = new Transaction();
            $transaction->product_uuid = $request->query('product')['uuid'];
            $transaction->merchant()->associate(Merchant::current());
            $transaction->customer()->associate($customer);
            $transaction->custom_data = $request->query('custom_data');
            $transaction->save();
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollback();
            throw new TransactionServiceException('Внутренняя ошибка');
        }

        return $transaction;
    }

    public function setStatus(Transaction $transaction, string $status): void {
        $transaction->status = $status;
        $transaction->save();
    }
}
