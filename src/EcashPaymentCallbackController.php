<?php

namespace IXCoders\LaravelEcash;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class EcashPaymentCallbackController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    final public function __invoke(Request $request)
    {
        LaravelEcash::updateTransactionLogEntry($request->all());
    }
}
