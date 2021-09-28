<?php


namespace App\Services\Sber;

use App\Exceptions\SberException;
use App\Models\Transaction;

interface SberPaymentApi {
    /**
     * @param Transaction $transaction
     * @return array
     * @throws SberException
     */
    public function getOrderStatus(Transaction $transaction): array;

    /**
     * @param Transaction $transaction
     * @param SberRegisterParams $params
     * @return array
     * @throws SberException
     */
    public function register(Transaction $transaction,SberRegisterParams $params): array;

    /**
     * @param Transaction $transaction
     * @return array
     * @throws SberException
     */
    public function deposit(Transaction $transaction): array;

    /**
     * @param Transaction $transaction
     * @return array
     * @throws SberException
     */
    public function reverse(Transaction $transaction): array;

    /**
     * @param Transaction $transaction
     * @return array
     * @throws SberException
     */
    public function refund(Transaction $transaction): array;

    /**
     * @param Transaction $transaction
     * @return array
     * @throws SberException
     */
    public function getReceiptStatus(Transaction $transaction): array;
}
