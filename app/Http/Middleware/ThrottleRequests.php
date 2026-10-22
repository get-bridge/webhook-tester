<?php

namespace App\Http\Middleware;

use App\Events\IpThrottled;
use Illuminate\Routing\Middleware\ThrottleRequests as LaravelThrottleRequests;

class ThrottleRequests extends LaravelThrottleRequests
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @param int $maxAttempts
     * @param callable|null $responseCallback
     * @return \Illuminate\Http\Exceptions\ThrottleRequestsException|\Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
    {
        broadcast(new IpThrottled($request->ip()));

        return parent::buildException($request, $key, $maxAttempts, $responseCallback);
    }
}
