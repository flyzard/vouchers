<?php

namespace Flyzard\Vouchers\Tests\Unit;

use Flyzard\Vouchers\Exceptions\InvalidMaskException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Flyzard\Vouchers\Tests\TestCase;
use Flyzard\Vouchers\Facades\Vouchers;
use Flyzard\Vouchers\VouchersCodeGenerator;

class VouchersCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private $generator;

    public function setUp(): void
    {
        parent::setUp();

        $this->generator = new VouchersCodeGenerator;
    }


    /** @test **/
    public function the_generator_returns_a_string()
    {
        $this->assertIsString($this->generator->generateCode());
    }

    /** @test **/
    public function get_all_generated_codes()
    {
        $codes = [];

        for ($i = 0; $i < 10; $i++) {
            $codes[] = $this->generator->generateCode();
        }

        $this->assertArraysAreSimilar($this->generator->getExistingCodes(), $codes);
    }


    /** @test **/
    public function generates_unique_random_codes()
    {
        $code_one = $this->generator->generateCode();

        $code_two = $this->generator->generateCode();

        $this->assertStringNotContainsString($code_one, $code_two, "The codes should be unique!");
    }


    /** @test **/
    public function a_generator_may_have_a_mask()
    {
        $mask = "****";
        $this->generator->setMask("****");
        $this->assertEquals($mask, $this->generator->getMask());
    }

    /** @test */
    public function a_generator_may_have_a_max_length()
    {
        $len = 10;
        $this->generator->setMaxLength($len);
        $this->assertEquals($len, $this->generator->getMaxLength());
    }

    /** @test **/
    public function a_generator_replaces_the_mask_pattern_for_a_code()
    {
        $code = $this
            ->generator
            ->setMask("mask-****_end")
            ->uniqueCode();

        $this->assertEquals(substr($code, 0, 5), "mask-", "The begining of the masks is not the expected!");
        $this->assertEquals(substr($code, -4), "_end", "The end of the masks is not the expected!");
    }

    // /** @test **/
    // public function a_exception_is_raised_when_the_mask_is_not_valid()
    // {
    //     $this->expectException(InvalidMaskException::class);

    //     Vouchers::createVoucher("mask-invalid_no_stars_end");

    //     // Try to generate the same code already on the database
    //     $this
    //         ->generator
    //         ->setMask("mask-invalid_no_stars_end")
    //         ->uniqueCode();
    // }
}
