<?php

namespace Flyzard\Vouchers\Traits;

use Flyzard\Vouchers\Exceptions\UserHasNoVoucherException;
use Flyzard\Vouchers\Facades\Vouchers;
use Flyzard\Vouchers\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Redeemer has one or more vouchers that it can redeem
 */
trait Redeemer
{
    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, config('vouchers.redeemer_table'));
    }

    /**
     * Get the value of the voucher on the total value passed
     */
    public function evaluate(string $code, float $totalValue): Voucher
    {
        $voucher = Vouchers::check($code);

        if ($this->checkForUser($voucher)) {
            if ($voucher->type == Voucher::VALUE_TYPE) {
                $voucher->discount = $voucher->amount;
            }

            if ($voucher->type == Voucher::PERCENTAGE_TYPE) {
                $voucher->discount = $totalValue * $voucher->amount;
            }

            return $voucher;
        }

        return 0;
    }

    public function checkForUser($voucher): bool
    {
        if ($voucher->redeemer_restricted) {
            $userVoucher = DB::table(config('vouchers.redeemer_table'))
                ->where([
                    'voucher_id' => $voucher->id,
                    'user_id' => $this->id,
                    'status' => true,
                    'redeemed_at' => null
                ])->exists();

            if (!$userVoucher) {
                throw new UserHasNoVoucherException("The user cannot redeem this voucher", 1);
            }
        }

        return true;
    }

    /**
     * Redeem a certain voucher by the code
     * 
     * @throws Exception
     */
    public function redeem(string $code, float $totalValue): bool
    {
        if ($voucher = $this->evaluate($code, $totalValue)) {
            DB::table(config('vouchers.redeemer_table'))
                ->where([
                    'voucher_id' => $voucher->id,
                    'user_id' => $this->id,
                    'status' => true,
                    'redeemed_at' => null
                ])->update(['redeemed_at' => now()]);

            return $voucher->id;
        }

        return false;
    }
}
