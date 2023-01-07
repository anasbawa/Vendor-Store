<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'credit_card',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (QueryException $e) {
            // نستخدم هذا التابع لمنع تخزين الاكسبشن في ال لوغ فايل
            if ($e->getCode() === '23000') {
                // منعنا التخزين وخزنا الرسالة التي نريدها عند حدوث هذا الاكسبشن
                Log::channel('sql')->warning($e->getMessage()); // custom channel (we can find it in config->loging.php)
                return false; // منع التخزين
            }

            return true; // السماح بالتخزين
        });

        $this->renderable(function (QueryException $e, Request $request) {
            if ($e->getCode() == 23000) {
                $message = 'Foreign key constraint failed';
            } else {
                $message = $e->getMessage();
            }

            if ($request->expectsJson()) { // in case of API
                return response()->json([
                    'message' => $message,
                ], 400);
            }

            return redirect()
                ->back()
                ->withInput() // تفعيل وضع الفلاش
                ->withErrors([ // تفعيل وضع الرجوع برسالة الخطأ
                    'message' => $e->getMessage(),
                ])
                ->with('info', $message);
        });
    }
}
