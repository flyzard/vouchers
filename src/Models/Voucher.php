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
        'limit',
        'uses',
        'properties',
        'redeemer_restricted'
    ];

    protected $dates = [
        'from_date',
        'to_date',
    ];

    protected $casts = [
        'properties' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('vouchers.table', 'vouchers');
    }

    public function redeemers(): BelongsToMany
    {
        return $this->belongsToMany(config('vouchers.user_model'), config('vouchers.relation_table'))->withPivot('redeemed_at');
    }

    protected static function newFactory(): VoucherFactory
    {
        return VoucherFactory::new();
    }
}
