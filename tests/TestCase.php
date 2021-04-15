<?php

namespace Flyzard\Vouchers\Tests;

use Flyzard\Vouchers\VouchersServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // $this->userMock = \Mockery::mock('Flyzard\\Vouchers\\Tests\\User');
        // dd(get_class($this->userMock));

        // $this->app->bind('App\Models\User', function ($app) {
        //     return new $this->userMock();
        // });

        // $this->app->instance('App\\Models\\User', $this->userMock);

        $this->user = $this->createUser();
    }

    protected function createUser()
    {
        $user = new User();

        $user->id = rand(1, 2000);

        return $user;
    }

    protected function getPackageProviders($app)
    {
        return [
            VouchersServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('vouchers.user_model', \Flyzard\Vouchers\Tests\User::class);

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
