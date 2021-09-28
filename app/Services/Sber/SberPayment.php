<?php

namespace App\Services\Sber;


use App\Exceptions\SberException;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\Log;
use Illuminate\Support\Facades\Http;

class SberPayment implements SberPaymentApi {

    private Log $log;
    private ?Merchant $merchant;

    public function __construct(Log $log) {
        $this->log = $log;
    }

    public function register(Transaction $transaction, SberRegisterParams $params): array {
        return $this->defineMerchant($transaction)
            ->post($params->route(), $params->data());
    }

    public function getOrderStatus(Transaction $transaction): array {
        return $this->defineMerchant($transaction)
            ->post('getOrderStatusExtended', ['orderId' => $transaction->sber_order_id]);
    }

    public function deposit(Transaction $transaction): array {
        return $this->defineMerchant($transaction)
            ->post('deposit', ['orderId' => $transaction->sber_order_id, 'amount' => 0]);
    }

    public function reverse(Transaction $transaction): array {
        return $this->defineMerchant($transaction)
            ->post('reverse', ['orderId' => $transaction->sber_order_id]);
    }

    public function refund(Transaction $transaction): array {
        return $this->defineMerchant($transaction)
            ->post('refund', ['orderId' => $transaction->sber_order_id, 'amount' => 0]);
    }

    public function getReceiptStatus(Transaction $transaction): array {
        return $this->defineMerchant($transaction)
            ->post('getReceiptStatus', ['orderId' => $transaction->sber_order_id]);
    }

    protected function defineMerchant(Transaction $transaction): SberPayment {
        $this->merchant = $transaction->merchant;
        return $this;
    }

    protected function post(string $route, array $data = []): array {
        $response = Http::asForm()
            ->post($this->url($route), $this->addAuthCredentials($data))
            ->json();

        $this->log->requestToSber($route, $data, $response);

        if (isset($response['errorCode']) && $response['errorCode'] !== '0') {
            throw new SberException($response['errorMessage'], $response['errorCode']);
        }

        return $response;
    }

    protected function addAuthCredentials(array $data): array {
        $authCredentials = [
            'userName' => $this->merchant->sberLogin(),
            'password' => $this->merchant->sberPassword(),
        ];

        return array_merge($data, $authCredentials);
    }

    protected function url(string $route): string {
        $env = $this->merchant->isSberProduction()
            ? 'production'
            : 'testing';

        return route("sber.$env.$route");
    }
}
