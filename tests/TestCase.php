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

    protected function assertArraysAreSimilar($a, $b)
    {
        if (count(array_diff_assoc($a, $b))) {
            $this->assertTrue(false, "the arrays are not similar");
        }

        foreach ($a as $k => $v) {
            if ($v !== $b[$k]) {
                $this->assertTrue(false, "the arrays are not similar");
                return false;
            }
        }

        $this->assertTrue(true);
    }
}
