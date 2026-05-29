<?php

namespace App\Providers;

use App\Events\ExpenseCreated;
use App\Listeners\LogExpenseActivity;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Enforce HTTPS in production or when running on DigitalOcean domain
        if ($this->app->environment('production') || str_contains(request()->getHost(), 'ondigitalocean.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register event → listener mapping
        // When ExpenseCreated is fired anywhere in the app,
        // Laravel automatically calls LogExpenseActivity::handle()
        Event::listen(
            ExpenseCreated::class,
            LogExpenseActivity::class,
        );
    }
}
