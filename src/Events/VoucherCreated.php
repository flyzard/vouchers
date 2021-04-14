<?php

declare(strict_types=1);

namespace Flyzard\Vouchers\Events;

use Flyzard\Vouchers\Models\Voucher;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class VoucherCreated
{
    use Dispatchable, SerializesModels;

    private Voucher $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }
}
