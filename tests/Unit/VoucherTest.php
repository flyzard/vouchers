<?php

namespace Flyzard\Vouchers\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Flyzard\Vouchers\Tests\TestCase;
use Flyzard\Vouchers\Models\Voucher;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_voucher_has_a_title()
    {
        $voucher = Voucher::factory()->create(['title' => 'Fake Title', 'code' => 'some_code']);
        $this->assertEquals('Fake Title', $voucher->title);
    }
}
