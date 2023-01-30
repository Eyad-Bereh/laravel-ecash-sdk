<?php

namespace IXCoders\LaravelEcash\Utilities;

use Illuminate\Support\Str;

class VerificationCodeManager
{
    public function __construct(
        private string $merchant_id,
        private string $merchant_secret
    ) {
    }

    public function getVerificationCode(int $amount, string $reference): string
    {
        $combination = $this->merchant_id .
            $this->merchant_secret .
            $amount .
            mb_convert_encoding($reference, 'ASCII', 'UTF-8');

        $hash = md5($combination);

        return Str::upper($hash);
    }

    public function checkVerificationCode(string $hash, string $amount, string $reference): bool
    {
        $current = $this->getVerificationCode($amount, $reference);
        $hash = Str::upper($hash);

        return strcmp($current, $hash) === 0;
    }
}
