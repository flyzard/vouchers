<?php

namespace Flyzard\Vouchers\Facades;

use Illuminate\Support\Facades\Facade;

class Vouchers extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'vouchers';
    }
}
