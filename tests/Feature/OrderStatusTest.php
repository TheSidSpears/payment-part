<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Tests\TestCase;

class OrderStatusTest extends TestCase {
    use ApiAuth;

    public function testGetOrderStatus(): void {
        $this->withHeaders($this->headers())
            ->get(route('order_status', ['transaction' => Transaction::find(1)]))
            ->dump()
            ->assertJsonPath('orderStatus.errorCode', '0');
    }

    public function testGetOrderStatusBySberId(): void {
        $this->withHeaders($this->headers())
            ->get(route('order_status_by_sber_id', ['sberOrderId' => '//order-id']))
            ->dump()
            ->assertJsonPath('orderStatus.errorCode', '0');
    }
}
