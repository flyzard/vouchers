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

    public function setUp(): void
    {
        parent::setUp();

        $this->generalVoucherParams = [
            'The title for the voucher', // title
            [], // redeemerIds
            date("Y-m-d H:i:s", time()), // from_date
            date("Y-m-d H:i:s", strtotime('tomorrow')), // to_date
            2, // limit
            [], // properties
            'prefix-****-sufix', // voucherCodeMask
            8
        ];
    }

    /** @test **/
    public function a_event_is_emited_when_a_voucher_is_created()
    {
        Event::fake();

        $title = $this->generalVoucherParams[0];

        $voucher = Vouchers::createVoucher(...$this->generalVoucherParams);

        Event::assertDispatched(VoucherCreated::class);

        Event::assertDispatched(function (VoucherCreated $event) use ($voucher, $title) {
            return ($event->getVoucher()->id === $voucher->id
                && $title == $event->getVoucher()->title);
        });
    }

    /** @test **/
    public function a_voucher_is_created()
    {
        $voucher = Vouchers::createVoucher(...$this->generalVoucherParams);

        $this->assertDatabaseHas(config('vouchers.table', 'vouchers'), [
            'code' => $voucher->code,
            'title' => $this->generalVoucherParams[0]
        ]);
    }

    //  /** @test **/
    // public function a_voucher_can_have_one_or_more_redeemer()
    // {

    //     // $voucher = Vouchers::createUserVoucher($this->user, "prefix-****-sufix", "Season brand code");
    // }
}
