<?php

namespace IXCoders\LaravelEcash\Test\Unit;

use IXCoders\LaravelEcash\Tests\TestCase;
use IXCoders\LaravelEcash\Utilities\VerificationCodeManager;

class VerificationCodeTest extends TestCase
{
    private const MERCHANT_ID = 'A97B49BC-D6B9-48C3-B44D-25BD0E0AFC41';

    private const MERCHANT_SECRET = 'k6jc1pkSCSrbKMvAWG0b0LKtFBb4hvzsraXU5iSvQj0L9fd9o10h1oRhEXVUJVy7';

    private VerificationCodeManager $vcm;

    protected function setUp(): void
    {
        $this->vcm = new VerificationCodeManager(self::MERCHANT_ID, self::MERCHANT_SECRET);

        parent::setUp();
    }

    /**
     * @test
     *
     * @dataProvider amount_and_reference_data_provider
     */
    public function generated_verification_code_matches_expected_verification_code(int $amount, string $reference, string $expected_hash)
    {
        $generated_hash = $this->vcm->getVerificationCode($amount, $reference);
        $this->assertEquals($expected_hash, $generated_hash);
    }

    private function amount_and_reference_data_provider()
    {
        return [
            'Amount = 1000 & Reference = Order #247' => [1000, 'Order #247', 'EF1B9260D37FE50A9AFABFB6670B7E7C'],
            'Amount = 5000 & Reference = Test' => [5000, 'Test', 'F58DE6D6D534629C83764F153A0ABC14'],
        ];
    }
}
