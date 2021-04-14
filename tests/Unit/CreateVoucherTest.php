<?php

namespace Flyzard\Vouchers\Tests\Unit;

use Flyzard\Vouchers\Events\VoucherCreated;
use Flyzard\Vouchers\Facades\Vouchers;
use Flyzard\Vouchers\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class CreateVoucherTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function a_event_is_emited_when_a_voucher_is_created()
    {
        Event::fake();

        $voucher = Vouchers::createVoucher();

        Event::assertDispatched(VoucherCreated::class, function ($event) use ($voucher) {
            return $event->getVoucher()->id === $voucher->id;
        });
    }

    /** @test **/
    public function a_voucher_is_created()
    {
        $voucher = Vouchers::createVoucher("brand-****-season", "Season brand code");

        $this->assertDatabaseHas('vouchers', [
            'code' => $voucher->code,
            'title' => "Season brand code"
        ]);
    }
}
