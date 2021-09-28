<?php


namespace App\Services\Dalion;


use App\Models\Transaction;

class DalionApiMock implements DalionApi {

    public function carAvailableForReserve(string $uuid): bool {
        return json_decode('{"available": true, "price_reserve": 100}')->available;
    }

    public function holdCarAndCreateContract(Transaction $transaction, string $amount): object {
        return json_decode('{"receiptData": {"name": "Предоплата за автомобиль 333 по договору 111"}}');
    }

    public function transactionFailed(Transaction $transaction): bool {
        return true;
    }

    public function sendPaymentApprove(Transaction $transaction): bool {
        return true;
    }
}
