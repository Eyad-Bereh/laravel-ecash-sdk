<?php

namespace IXCoders\LaravelEcash\Utilities;

use Illuminate\Support\Str;

class VerificationTokenManager
{
    public function __construct(
        private string $merchant_id,
        private string $merchant_secret
    ) {
    }

    public function getVerificationToken(string $transaction_number, string $amount, string $reference): string
    {
        $combination = $this->merchant_id .
            $this->merchant_secret .
            $transaction_number .
            $amount .
            mb_convert_encoding($reference, 'ASCII', 'UTF-8');

        $hash = md5($combination);

        return Str::upper($hash);
    }

    public function checkVerificationToken(string $hash, string $transaction_number, string $amount, string $reference): bool
    {
        $current = $this->getVerificationToken($transaction_number, $amount, $reference);
        $hash = Str::upper($hash);

        return strcmp($current, $hash) === 0;
    }
}
