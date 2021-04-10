<?php

namespace Flyzard\Vouchers\Traits;

use Flyzard\Vouchers\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasVouchers
{
    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, 'user_voucher');
    }

    // /**
    //  * Checks if a redeemer has a voucher with the given code
    //  */
    // public function validate()
    // {
    //     return ;
    // }
}
