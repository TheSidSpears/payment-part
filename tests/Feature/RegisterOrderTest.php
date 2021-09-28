<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegisterOrderTest extends TestCase {
    use ApiAuth;

    public function testCanReserveCar(): void {
         $this->withHeaders($this->headers())
            ->get(route('can_reserve_car', ['uuid' => '9e7eca36-d62b-11ea-bab1-00155d8a5101']))
            ->dump();
    }

    public function testDoPayment(): void {
        $sberPaymentForm = $this->getPaymentFormUrl()->dump()->json('formUrl');

        self::assertSame('3dsec.sberbank.ru', parse_url($sberPaymentForm)['host']);
        self::assertSame(Transaction::latest()->first()->status, 'registered');
    }

    protected function getPaymentFormUrl(): TestResponse {
        return $this->withHeaders($this->headers())
            ->get(route('payment_form_url', [
                'customer' => [
                    'name' => 'Bob',
                    'second_name' => 'Washington',
                    'patronymic' => 'Ivanovich',
                    'phone' => '79621234567',
                    'email' => '//E-MAIL',
                    'passport_serial' => 1234,
                    'passport_num' => 123456,
                    'passport_date' => '31.12.2012',
                ],
                'product' => [
                    'uuid' => 'b1790fb0-52f7-11ea-ba92-00155d030302',
                    'title' => 'Mercedes-Benz CLA 200 комплектация Sport двигатель 1.3 литра (150 л.с.) Черный',
                    'vin' => '//VIN',
                    'price' => 100,
                ],
                'custom_data' => [
                    'convenientTimeToCall' => '15:00',
                ],
                'settings' => [
                    'returnUrl' => 'https://site/payment/success',
                    'failUrl' => 'https://site/payment/failed',
                    'paymentType' => '//PAYMENT-TYPE'
                ]
            ]));
    }
}
