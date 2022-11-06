<?php

namespace IXCoders\LaravelEcash;

use Illuminate\Database\Eloquent\Model;

class EcashTransactionLog extends Model
{
    protected $fillable = [
        'checkout_type',
        'amount',
        'reference',
        'currency',
        'language',
        'verification_code',
        'is_successful',
        'message',
        'transaction_number',
        'token',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];
}
