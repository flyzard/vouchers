<?php

namespace Flyzard\Vouchers\Tests;

use Flyzard\Vouchers\VouchersServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            VouchersServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . '/../database/migrations/create_vouchers_table.php.stub';

        (new \CreateVouchersTable)->up();
    }
}
