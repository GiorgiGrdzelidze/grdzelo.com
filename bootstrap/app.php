<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('subscriptions:generate-reminders')->dailyAt('06:00');
        $schedule->command('subscriptions:send-reminders')->dailyAt('08:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            SetLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // SetLocale must run before SubstituteBindings so that translatable
        // route-model resolution (e.g. resolving Hobby by `slug->{locale}`)
        // sees the URL-segment locale, not the framework default.
        $middleware->priority([
            InvokeDeferredCallbacks::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SetLocale::class,
            SubstituteBindings::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if ($request->is('admin', 'admin/*') || $request->is('api/*')) {
                return $response;
            }

            if (! $request->expectsJson() && $exception instanceof NotFoundHttpException) {
                return Inertia::render('Public/NotFound', [
                    'status' => 404,
                ])->toResponse($request)->setStatusCode(404);
            }

            if (
                ! $request->expectsJson()
                && $exception instanceof HttpExceptionInterface
                && in_array($exception->getStatusCode(), [403, 419, 500, 503], true)
            ) {
                return Inertia::render('Public/NotFound', [
                    'status' => $exception->getStatusCode(),
                    'message' => $exception->getMessage() ?: null,
                ])->toResponse($request)->setStatusCode($exception->getStatusCode());
            }

            return $response;
        });
    })->create();
