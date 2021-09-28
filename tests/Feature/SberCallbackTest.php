<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Tests\TestCase;

class SberCallbackTest extends TestCase
{
    use ApiAuth;

    public function testSberCallback(): void
    {
        $this->withHeaders($this->headers())
            ->get(route('sber_callback', [
                'orderNumber' => 106,
                'operation'   => 'approved',
                'status'      => 1,
                'checksum' => '//CHECKSUM'
            ]))
            ->dump()
            ->assertStatus(200);

        self::assertSame(Transaction::find(1)->status, 'approved');
    }
}
