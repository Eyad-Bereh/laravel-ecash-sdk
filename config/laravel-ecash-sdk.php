<?php

// config for IXCoders/LaravelEcash

return [
    /**
     * Checkout types
     * 
     * Defines the available checkout types for the system.
     */
    "checkout_types" => [
        "CARD",
        "QR"
    ],

    /**
     * Terminal key
     * 
     * Defines the terminal key which will be used.
     */
    "terminal_key" => env("ECASH_TERMINAL_KEY"),

    /**
     * Merchant ID
     * 
     * Defines the Merchant ID of the account that will be used. 
     */
    "merchant_id"   =>  env("ECASH_MERCHANT_ID"),

    /**
     * Merchant secret
     * 
     * Defines the secret key that links to the merchant account.
     */
    "merchant_secret" => env("ECASH_MERCHANT_SECRET"),

    /**
     * Currencies
     * 
     * Defines the currencies that are currently supported by the system.
     */
    "currencies" => ["SYP"],

    /**
     * Redirect route
     * 
     * Defines the name of the route which the user will be redirected to after the operation ends.
     */
    "redirect_route" => "ecash.redirect",

    /**
     * Callback route
     * 
     * Defines the name of the route which will be called upon the operation finish.
     */
    "callback_route" => "ecash.callback"
];
