<?php


namespace App\Services\Dalion;
use App\Exceptions\DalionException;
use App\Models\Transaction;

interface DalionApi {

    public function carAvailableForReserve(string $uuid): bool;

    /**
     * @param Transaction $transaction
     * @param string $amount
     * @return object
     * @throws DalionException
     */
    public function holdCarAndCreateContract(Transaction $transaction, string $amount): object;

    public function transactionFailed(Transaction $transaction): bool;
    public function sendPaymentApprove(Transaction $transaction): bool;
}
