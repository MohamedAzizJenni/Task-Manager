<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     */
    protected $middleware = [
        // Minimal global middleware stack
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // Optional: If you decide to use web routes with sessions/cookies
        ],

        'api' => [
            // Default middleware for APIs
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // ✅ Your custom CORS middleware
            \App\Http\Middleware\Cors::class,
        ],
    ];

    /**
     * Route middleware.
     */
    protected $routeMiddleware = [
        // Only define what you actually use
    ];
}
