<?php

namespace IXCoders\LaravelEcash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use IXCoders\LaravelEcash\Http\Requests\EcashPaymentCallbackRequest;
use IXCoders\LaravelEcash\LaravelEcash;

final class EcashPaymentCallbackController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    final public function __invoke(EcashPaymentCallbackRequest $request)
    {
        LaravelEcash::updateTransactionEntry($request->all());
    }
}
