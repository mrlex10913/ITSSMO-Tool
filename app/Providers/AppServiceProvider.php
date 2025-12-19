<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::aliasMiddleware('ip.filter', \App\Http\Middleware\DesktopBorrwersIpFilter::class);
        // Register model observers for audit trails
        \App\Models\Helpdesk\Ticket::observe(\App\Observers\TicketObserver::class);
        \App\Models\Helpdesk\TicketComment::observe(\App\Observers\TicketCommentObserver::class);

        // Ensure factories are discovered for namespaced models (e.g., App\Models\Helpdesk\*)
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            // Convert App\Models\Helpdesk\Ticket => Database\Factories\TicketFactory
            $class = class_basename($modelName);

            return 'Database\\Factories\\'.$class.'Factory';
        });
    }
}
