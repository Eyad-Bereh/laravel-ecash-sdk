<?php

namespace IXCoders\LaravelEcash\Test\Unit;

use IXCoders\LaravelEcash\Tests\TestCase;

class VerificationCodeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
