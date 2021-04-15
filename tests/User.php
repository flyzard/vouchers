<?php

namespace Flyzard\Vouchers\Tests;

use Flyzard\Vouchers\Traits\Redeemer;

class User extends \Illuminate\Foundation\Auth\User
{
    use Redeemer;
}
