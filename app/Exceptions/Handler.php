<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e){
        if ($e instanceof ModelNotFoundException && $request->wantsJson() && $request->expectsJson()) {
            return response()->json([
                'status'  => false,
                'message' => 'Resource Not Found!'
            ], 404);
        }

        if ($e instanceof TokenMismatchException) {
            if($request->wantsJson()){
                return response()->json([
                    'status'  => false,
                    'message' => 'Page expired!'
                ], 419);
            }
            return redirect()->route('login');
        }

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception){
        return $request->expectsJson()
            ? response()->json([
                'status'      => false,
                'message'     => $exception->getMessage(),
                'redirect_to' => route('login')
            ], 401)
            : redirect()->route('login');
    }

    protected function invalidJson($request, ValidationException $exception) {
        return response()->json([
            'status'  => false,
            'message' => $exception->getMessage(),
            'errors'  => $exception->errors(),
        ], $exception->status);
    }
}
