<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('الجلسة منتهية، يرجى تحديث الصفحة والمحاولة مرة أخرى.'),
                ], 419);
            }

            $redirectTo = url()->previous();
            if (empty($redirectTo) || $redirectTo === $request->fullUrl()) {
                $redirectTo = route('admin.login');
            }

            return redirect($redirectTo)
                ->withInput($request->except('_token'))
                ->withErrors([
                    'token' => __('الجلسة منتهية، يرجى إعادة المحاولة.'),
                ]);
        }

        return parent::render($request, $e);
    }
}
