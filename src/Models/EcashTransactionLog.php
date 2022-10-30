<?php

namespace IXCoders\LaravelEcash\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcashTransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "checkout_type",
        "amount",
        "reference",
        "currency",
        "language",
        "verification_code",
        "is_successful",
        "message",
        "transaction_number",
        "token",
        "notes"
    ];

    protected $casts = [
        "amount" => "integer"
    ];
}
