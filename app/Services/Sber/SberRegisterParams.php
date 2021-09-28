<?php


namespace App\Services\Sber;


use App\Http\Requests\RegisterOrder;
use Carbon\Carbon;
use Illuminate\Support\Arr;


class SberRegisterParams {
    public const PAYMENT_TYPES = ['register', 'registerPreAuth'];

    protected int $orderNumber;
    protected string $route;
    private array $query;
    private object $customReceiptData;

    public function __construct(RegisterOrder $request, int $orderNumber, object $customReceiptData) {
        $this->query = $request->query();
        $this->orderNumber = $orderNumber;
        $this->customReceiptData = $customReceiptData;
    }

    public function data(): array {
        return [
            'orderNumber' => $this->orderNumber,
            'description' => 'Предоплата за автомобиль '. $this->query('product.title'),
            'amount' => $this->query('product.price'),
            'email' => $this->query('customer.email'),
            'phone' => '+' . $this->query('customer.phone'),
            'returnUrl' => $this->query('settings.returnUrl'),
            'failUrl' => $this->query('settings.failUrl'),
            'sessionTimeoutSecs' => config('sber.sessionTimeout'),
            'features' => 'FORCE_FULL_TDS',
            'jsonParams' => json_encode([
                'VIN' => $this->query('product.vin')], JSON_THROW_ON_ERROR),
            'orderBundle' => json_encode([
                'orderCreationDate' => Carbon::now()->format('c'),
                'customerDetails' => [
                    'email' => $this->query('customer.email'),
                    'phone' => '+' . $this->query('customer.phone'),
                    'fullName' => implode(' ', [
                        $this->query('customer.second_name'),
                        $this->query('customer.name'),
                        $this->query('customer.patronymic')
                    ]),
                    'passport' => $this->query('customer.passport_serial') . $this->query('customer.passport_num')
                ],
                'cartItems' => ['items' => [[
                    'positionId' => 1,
                    'name' => $this->customReceiptData->name,
                    'quantity' => ['value' => 1, 'measure' => 'штук'],
                    'itemCode' => $this->query('product.vin'),
                    'itemPrice' => $this->query('product.price'),
                    'itemCurrency' => 643,
                    'tax' => [
                        'taxType' => 6,
                        'taxSum' => $this->query('product.price') / 5 // 20%
                    ],
                    'itemAttributes' => [
                        'attributes' => [
                            ['name' => 'paymentMethod', 'value' => 2],
                            ['name' => 'paymentObject', 'value' => 1],
                        ]
                    ]
                ]]]
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
        ];
    }

    protected function query(string $key) {
        return Arr::get($this->query, $key);
    }

    public function route(): string {
        return $this->query('settings.paymentType');
    }
}
