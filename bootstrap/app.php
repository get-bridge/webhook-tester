<?php

use App\Http\Middleware\ThrottleRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;
use Sentry\SentrySdk;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Routes are registered without the "web" group: webhooks are posted by
        // third parties, so sessions and CSRF verification must not apply.
        using: function () {
            require __DIR__.'/../routes/web.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Captured webhook payloads must be stored exactly as received, so the
        // default TrimStrings/ConvertEmptyStringsToNull stack is not used.
        $middleware->use([
            PreventRequestsDuringMaintenance::class,
        ]);

        $middleware->alias([
            'throttle' => ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->render(function (Throwable $e, Request $request) {
            if (!($request->ajax() || $request->wantsJson() || $request->isJson())) {
                return null;
            }

            // The front end reads validation errors as a flat {field: [messages]} object.
            if ($e instanceof ValidationException) {
                return response()->json($e->errors(), $e->status);
            }

            $message = 'An internal error occurred';

            if ($e instanceof HttpException) {
                $message = $e->getMessage();
            }

            $eventId = SentrySdk::getCurrentHub()->getLastEventId();

            $response = [
                'success' => false,
                'error' => [
                    'message' => $message,
                    'id' => $eventId ? (string)$eventId : null,
                ],
            ];

            if ($e->getCode()) {
                $response['error']['code'] = $e->getCode();
            }

            if (config('app.debug')) {
                $response['error']['exception'] = sprintf('[%s] %s', get_class($e), $e->getMessage());
                $response['error']['trace'] = $e->getTrace();
                $response['error']['file'] = $e->getFile();
                $response['error']['line'] = $e->getLine();
            }

            if ($e instanceof HttpException) {
                return response()->json($response, $e->getStatusCode());
            }

            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
