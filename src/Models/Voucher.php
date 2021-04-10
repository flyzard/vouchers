<?php

declare(strict_types=1);

namespace Flyzard\Vouchers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Flyzard\Vouchers\Database\Factories\VoucherFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'status',
        'from_date',
        'to_date',
        'params'
    ];

    public function redeemers(): BelongsToMany
    {
        return $this->belongsToMany(config('vouchers.user_model'), 'user_voucher');
    }

    protected static function newFactory(): VoucherFactory
    {
        return VoucherFactory::new();
    }
}
