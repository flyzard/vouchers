<?php

namespace Flyzard\Vouchers\Exceptions;

class InvalidMaskException extends \Exception
{
    protected $message = 'The given mask is not valid.';
}
