<?php

namespace Flyzard\Vouchers\Tests\Unit;

use Flyzard\Vouchers\Events\VoucherCreated;
use Flyzard\Vouchers\Exceptions\UserHasNoVoucherException;
use Flyzard\Vouchers\Facades\Vouchers;
use Flyzard\Vouchers\Models\Voucher;
use Flyzard\Vouchers\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class CreateVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->title = 'The title for the voucher';

        $this->generator = Vouchers::setTitle('The title for the voucher')
            ->setRedeemers([])
            ->setFromDate(now())
            ->setToDate(date("Y-m-d H:i:s", strtotime('tomorrow')))
            ->setLimit(2)
            ->setProperties([])
            ->setVoucherCodeMask('prefix-****-sufix')
            ->setMaxLength(8);
    }

    /** @test **/
    public function a_event_is_emited_when_a_voucher_is_created()
    {
        Event::fake();

        $voucher = $this->generator->createVoucher();

        Event::assertDispatched(VoucherCreated::class);

        Event::assertDispatched(function (VoucherCreated $event) use ($voucher) {
            return ($event->getVoucher()->id === $voucher->id
                && $this->title == $event->getVoucher()->title);
        });
    }

    /** @test **/
    public function a_voucher_is_created()
    {
        $voucher = $this->generator->createVoucher();

        $this->assertDatabaseHas(config('vouchers.table', 'vouchers'), [
            'code' => $voucher->code,
            'title' => $this->title
        ]);
    }

    /** @test **/
    public function a_voucher_may_belong_to_one_or_more_redeemers()
    {
        $users = $this->getUsersArray();

        $voucher = $this
            ->generator
            ->setAmount(0.1)
            ->setType(Voucher::PERCENTAGE_TYPE)
            ->setRedeemers($users)
            ->createVoucher();

        $this->assertEquals(10, $users[0]->evaluate($voucher->code, 100)->discount);

        $voucher = $this
            ->generator
            ->setAmount(9)
            ->setType(Voucher::VALUE_TYPE)
            ->setRedeemers($users)
            ->createVoucher();

        $this->assertEquals(9, $users[0]->evaluate($voucher->code, 100)->discount);

        $anotherUser = $this->createUser();

        $this->expectException(UserHasNoVoucherException::class);

        $this->assertEquals(9, $anotherUser->evaluate($voucher->code, 100)->discount);
    }

    /** @test **/
    public function a_voucher_gets_correctly_redeemed()
    {
        $users = $this->getUsersArray();

        $voucher = $this
            ->generator
            ->setAmount(0.1)
            ->setType(Voucher::PERCENTAGE_TYPE)
            ->setRedeemers($users)
            ->createVoucher();

        $this->assertEquals($voucher->id, $users[0]->redeem($voucher->code, 100));

        $anotherUser = $this->createUser();

        $this->expectException(UserHasNoVoucherException::class);

        $this->assertEquals(9, $anotherUser->redeem($voucher->code, 100));
    }

    private function getUsersArray()
    {
        $users = [];

        for ($i = 0; $i < 3; $i++) {
            $users[] = $this->createUser();
        }

        return $users;
    }
}
