<?php

declare(strict_types=1);

namespace Flyzard\Vouchers;

use Carbon\Carbon;
use Flyzard\Vouchers\Events\VoucherCreated;
use Flyzard\Vouchers\Exceptions\ExpiredVoucherException;
use Flyzard\Vouchers\Exceptions\MaxUsesVoucherException;
use Flyzard\Vouchers\Exceptions\NotActiceVoucherException;
use Flyzard\Vouchers\Exceptions\VoucherCodeNotValidException;
use Flyzard\Vouchers\Models\Voucher;
use Illuminate\Support\Facades\DB;

class Vouchers
{
    /** @var VouchersCodeGenerator */
    public $codeGenerator;

    private $props = [
        'title' => "voucher",
        'amount' => 0,
        'type' => Voucher::VALUE_TYPE,
        'redeemers' => [],
        'from_date' => null,
        'to_date' => null,
        'limit' => null,
        'properties' => []
    ];

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
    public function createVoucher()
    {
        if ($voucher = $this->storeVoucher()) {
            VoucherCreated::dispatch($voucher);

            return $voucher;
        }

        return null;
    }

    private function storeVoucher()
    {
        $redeemer_restricted = !empty($this->props['redeemers']);

        $voucher = Voucher::create(
            array_merge(
                ['code' => $this->codeGenerator->uniqueCode(), 'redeemer_restricted' => $redeemer_restricted],
                $this->props
            )
        );

        if ($redeemer_restricted) {
            foreach ($this->props['redeemers'] as $redeemer) {
                DB::table(config('vouchers.redeemer_table'))
                    ->insert([
                        'user_id' => $redeemer->id,
                        'voucher_id' => $voucher->id
                    ]);
            }
        }

        return $voucher;
    }

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->props['title'];
    }

    /**
     * Set the value of title
     */
    public function setTitle($title): self
    {
        $this->props['title'] = $title;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->props['amount'];
    }

    /**
     * Set the value of amount
     */
    public function setAmount($amount): self
    {
        $this->props['amount'] = $amount;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->props['type'];
    }

    /**
     * Set the value of type
     */
    public function setType($type): self
    {
        $this->props['type'] = $type;

        return $this;
    }

    /**
     * Get the value of redeemers
     */
    public function getRedeemers()
    {
        return $this->props['redeemers'];
    }

    /**
     * Set the value of redeemers
     */
    public function setRedeemers($redeemers): self
    {
        $this->props['redeemers'] = $redeemers;

        return $this;
    }

    /**
     * Get the value of from_date
     */
    public function getFromDate()
    {
        return $this->props['from_date'];
    }

    /**
     * Set the value of from_date
     */
    public function setFromDate($from_date): self
    {
        $this->props['from_date'] = $from_date;

        return $this;
    }

    /**
     * Get the value of to_date
     */
    public function getToDate()
    {
        return $this->props['to_date'];
    }

    /**
     * Set the value of to_date
     */
    public function setToDate($to_date): self
    {
        $this->props['to_date'] = $to_date;

        return $this;
    }

    /**
     * Get the value of limit
     */
    public function getLimit()
    {
        return $this->props['limit'];
    }

    /**
     * Set the value of limit
     */
    public function setLimit($limit): self
    {
        $this->props['limit'] = $limit;

        return $this;
    }

    /**
     * Get the value of properties
     */
    public function getProperties()
    {
        return $this->props['properties'];
    }

    /**
     * Set the value of properties
     */
    public function setProperties($properties): self
    {
        $this->props['properties'] = $properties;

        return $this;
    }

    public function setCodeGeneratorParams(string $voucherCodeMask = "null", int $maxLength = null, string $charSet = null)
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

    /**
     * Get the value of voucherCodeMask
     */
    public function getVoucherCodeMask()
    {
        return $this->codeGenerator->getMask();
    }

    /**
     * Set the value of voucherCodeMask
     */
    public function setVoucherCodeMask($voucherCodeMask): self
    {
        $this->codeGenerator->setMask($voucherCodeMask);

        return $this;
    }

    /**
     * Get the value of maxLength
     */
    public function getMaxLength()
    {
        return $this->codeGenerator->getMaxLength();
    }

    /**
     * Set the value of maxLength
     */
    public function setMaxLength($maxLength): self
    {
        $this->codeGenerator->setMaxLength($maxLength);

        return $this;
    }

    /**
     * Get the value of charSet
     */
    public function getCharSet()
    {
        return $this->codeGenerator->getCharSet();
    }

    /**
     * Set the value of charSet
     */
    public function setCharSet($charSet): self
    {
        $this->codeGenerator->setCharSet($charSet);

        return $this;
    }

    /**
     * Checks if given coupon is usable by a user
     */
    public static function check(string $code): Voucher
    {
        if (!$voucher = Voucher::where(['code' => $code, 'status' => 1])->first()) {
            throw new VoucherCodeNotValidException("The voucher doesn't not exist or is not active!", 1);
        }

        if ($voucher->to_date && Carbon::now()->gte($voucher->to_date)) {
            throw new ExpiredVoucherException("The voucher has already expired", 1);
        }

        if ($voucher->from_date && Carbon::now()->lte($voucher->from_date)) {
            throw new NotActiceVoucherException("The voucher is not yet redeemable", 1);
        }

        if ($voucher->limit && ($voucher->limit <= $voucher->uses)) {
            throw new MaxUsesVoucherException("The voucher was already redeemed the maximum number of times", 1);
        }

        return $voucher;
    }
}
