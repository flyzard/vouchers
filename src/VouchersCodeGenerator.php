<?php

namespace Flyzard\Vouchers;

use Flyzard\Vouchers\Exceptions\InvalidMaskException;
use Flyzard\Vouchers\Models\Voucher;

class VouchersCodeGenerator
{
    private string $charSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    private int $maxlength = 8;
    private array $existingCodes = [];
    private string $mask = "****-****";

    public function __construct(string $maskCode = null, int $maxlength = null)
    {
        $this->mask = $maskCode ?? $this->mask;
        $this->maxlength = $maxlength ?? $this->maxlength;
    }

    /**
     * Get a unique generated code
     */
    public function uniqueCode(): string
    {
        $code = $this->generateCode();

        while (!$this->codeIsUnique($code)) {
            $code = $this->generateCode();
        }

        return $code;
    }

    /**
     * Generates unique voucher codes
     */
    public function generateCode(): string
    {
        if ($len = $this->maskLenght()) {
            return $this->unmaskedCode($len);
        } elseif (!empty($this->mask)) {
            // In case the mask is setup without characters to be replaced (fixed given code)
            if (!$this->codeIsUnique($this->mask)) {
                var_dump($this->mask);
                throw new InvalidMaskException("The voucher code setted up on the mask is already in use!", 1);
            }
            return $this->mask;
        }

        // If the mask is not setted up, generate a random code based on the maxLenght
        $this->maxlength = $this->maxlength > 0 ? $this->maxlength : 8;
        $size = strlen($this->charSet);
        $code = '';

        for ($i = 0; $i < $this->maxlength; $i++) {
            $code .= $this->charSet[rand(0, $size - 1)];
        }

        return $code;
    }

    /**
     * Gets the already created codes on the database
     */
    public function getExistingCodes()
    {
        $this->existingCodes = Voucher::pluck('code')->toArray();

        return $this->existingCodes;
    }

    /**
     * Sets the mask for the code. The '*' in the mask would be
     * replaced by the given character set, the given string
     * or the already set up character set on this class.
     */
    public function setMask($mask = "****-****")
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * Gets the mask for the code. The '*' in the mask would be 
     * replaced by the given character set, the given string
     * or the already set up character set on this class.
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * Sets the mask for the code. The '*' in the mask would be 
     * replaced by the given character set, the given string
     * or the already set up character set on this class.
     */
    public function setMaxLength($maxlength = 8)
    {
        $this->maxlength = $maxlength;

        return $this;
    }

    /**
     * Gets the mask for the code. The '*' in the mask would be 
     * replaced by the given character set, the given string
     * or the already set up character set on this class.
     */
    public function getMaxLength()
    {
        return $this->maxlength;
    }

    /**
     * Checks the quantity of characters to replace on the mask
     * 
     * @return int the quantity of '*' in the mask
     */
    private function maskLenght(): int
    {
        if (empty($this->mask)) {
            return 0;
        }

        $nChars = count_chars($this->mask, 1);

        if (isset($nChars[42])) {
            return $nChars[42];
        }

        return 0;
    }

    private function codeIsUnique($code)
    {
        // Get the existing codes if not yet set
        $this->existingCodes || $this->getExistingCodes();

        return !in_array($code, $this->existingCodes);
    }

    private function unmaskedCode($len)
    {
        $size = strlen($this->charSet);
        $mask = $this->mask;
        for ($i = 0; $i < $len; $i++) {
            $mask = preg_replace('/\*/', $this->charSet[rand(0, $size - 1)], $mask, 1);
        }
        return $mask;
    }
}
