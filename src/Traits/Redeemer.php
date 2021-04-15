<?php

namespace Flyzard\Vouchers\Traits;

use Flyzard\Vouchers\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Redeemer has one or more vouchers that it can redeem
 */
trait Redeemer
{
    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, config('vouchers.redeemer_table'));
    }
}
