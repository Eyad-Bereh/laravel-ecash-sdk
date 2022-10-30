<?php

namespace IXCoders\LaravelEcash\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class EcashPaymentCallbackController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    final public function __invoke(Request $request)
    {
        $this->beforeStorage();

        $this->afterStorage();
    }

    abstract protected function beforeStorage();

    abstract protected function afterStorage();
}
