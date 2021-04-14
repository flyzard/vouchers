<?php

declare(strict_types=1);

namespace Flyzard\Vouchers;

use Flyzard\Vouchers\Events\VoucherCreated;
use Flyzard\Vouchers\Models\Voucher;

class Vouchers
{
    /**
     * Create a new voucher. 
     * When no given code, generates a random code
     * 
     * @param string $voucherCode OPTIONAL The code of the voucher to be created
     * @return Voucher
     */
    public function createVoucher($voucherCodeMask = null, $title = "", $maxLength = null)
    {
        $voucher = Voucher::create([
            'code' => (new VouchersCodeGenerator($voucherCodeMask, $maxLength))->uniqueCode(),
            'title' => $title
        ]);

        event(new VoucherCreated($voucher));

        return $voucher;
    }
}
