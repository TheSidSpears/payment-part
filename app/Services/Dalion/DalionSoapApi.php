<?php


namespace App\Services\Dalion;

use App\Exceptions\DalionException;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DalionSoapApi implements DalionApi {

    protected DalionSoapClient $client;

    public function __construct() {
        $this->client = new DalionSoapClient(config('dalion.webservice.endpoint'), [
            'login' => config('dalion.webservice.login'),
            'password' => config('dalion.webservice.password')
        ]);
    }

    public function carAvailableForReserve(string $uuid): bool {
        $response = $this->client
            ->censored_func_1([
                'car_id' => $uuid
            ])->return;

        return json_decode($response)->available;
    }

    public function holdCarAndCreateContract(Transaction $transaction, string $amount): object {
        $time = $this->nSecLater();

        $receiptData = $this->client->__soapCall('censored_func_2', [[
            'car_id' => $transaction->product_uuid,
            'time' => $time,
            'data' => json_encode([
                'transaction_id' => $transaction->id,
                'car_id' => $transaction->product_uuid,
                'customer' => $transaction->customer,
                'custom_data' => $transaction->custom_data,
                'amount' => $amount,
                'time' => $time,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ]])
            ->return;


        if ($receiptData === false || $receiptData === 'false') {
            throw new DalionException('Автомобиль уже забронирован');
        }

        return json_decode($receiptData);
    }

    public function transactionFailed(Transaction $transaction): bool {
        return $this->client
            ->censored_func_3([
                'transaction_id' => $transaction->id,
                'status' => false
            ])->return;
    }

    public function sendPaymentApprove(Transaction $transaction): bool {
        return $this->client
            ->censored_func_4([
                'transaction_id' => $transaction->id,
            ])->return;
    }

    protected function nSecLater(): string {
        $n = config('sber.sessionTimeout');
        // Буферное время исключает вероятность того, что человек оплатит заказ в последнюю секунду перед таймаутом,
        // а запрос в далион придёт чуть после таймаута и не сможет быть из-за этого обработан
        $buffer = 10;
        // 2020-11-24T09:22:33+00:00
        $nSecLater = Carbon::now()->addSeconds($n + $buffer)->format('c');
        // 2020-11-24T09:22:33
        return Str::substr($nSecLater, 0, -6);
    }
}
