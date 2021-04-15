<?php

declare(strict_types=1);

namespace Flyzard\Vouchers;

use Flyzard\Vouchers\Events\VoucherCreated;
use Flyzard\Vouchers\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class Vouchers
{

    /** @var VouchersCodeGenerator */
    private $codeGenerator;

    public function __construct(VouchersCodeGenerator $codeGenerator)
    {
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * Create a new voucher. 
     * When no given code, generates a random code
     * 
     * @param string $voucherCode OPTIONAL The code of the voucher to be created
     * @return Voucher
     */
    public function createVoucher(
        string $title = "",
        array $redeemerIds = [],
        $from_date = null,
        $to_date = null,
        int $limit = null,
        array $properties = null,
        string $voucherCodeMask = null,
        int $maxLength = 8,
        string $charSet = null
    ) {
        $this->setCodeGeneratorParams($voucherCodeMask, $maxLength, $charSet);;
        if ($voucher = $this->storeVoucher($title, $redeemerIds, $from_date, $to_date, $limit, $properties)) {
            VoucherCreated::dispatch($voucher);
            // $ee = Event::dispatch(VoucherCreated::class, $voucher);
            // $isto = event(new VoucherCreated($voucher));
        }

        return $voucher;

        return null;
    }

    private function storeVoucher(
        string $title,
        array $redeemers = [],
        $from_date = null,
        $to_date = null,
        $limit = null,
        $properties = null
    ) {

        $redeemer_restricted = !empty($redeemers);

        $voucher = Voucher::create([
            'code' => $this->codeGenerator->uniqueCode(),
            'title' => $title,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'limit' => $limit,
            'conditions' => $properties,
            'redeemer_restricted' => $redeemer_restricted
        ]);

        if ($redeemer_restricted) {
            foreach ($redeemers as $redeemer) {
                DB::table(config('vouchers.redeemer_table'))
                    ->insert([
                        'user_id' => $redeemer,
                        'voucher_id' => $voucher->id
                    ]);
            }
        }

        return $voucher;
    }

    private function setCodeGeneratorParams($voucherCodeMask, $maxLength, $charSet)
    {
        if ($voucherCodeMask) {
            $this->codeGenerator->setMask($voucherCodeMask);
        }

        if ($maxLength) {
            $this->codeGenerator->setMaxLength($maxLength);
        }

        if ($charSet) {
            $this->codeGenerator->setCharSet($charSet);
        }
    }
}
