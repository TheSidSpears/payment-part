<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Tests\TestCase;

class DalionCallbackTest extends TestCase {
    use ApiAuth;

    public function testDeposit(): void {
        $r = $this->withHeaders($this->headers())
            ->get(route('deposit', ['transaction' => Transaction::find(10)]))
            ->dump();

        self::assertEquals('true', $r->content());
    }

    public function testReverse(): void {
        $r = $this->withHeaders($this->headers())
            ->get(route('reverse', ['transaction' => Transaction::find(10)]))
            ->dump();

        self::assertEquals('true', $r->content());
    }

    public function testRefund(): void {
        $r = $this->withHeaders($this->headers())
            ->get(route('refund', ['transaction' => Transaction::find(10)]))
            ->dump();

        self::assertEquals('true', $r->content());
    }

    public function testReceipt(): void {
        $r = $this->withHeaders($this->headers())
            ->get(route('receipt', ['transaction' => Transaction::find(10)]))
            ->dump();

        self::assertEquals('true', $r->content());
    }
}
