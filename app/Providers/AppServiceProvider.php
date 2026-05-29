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
        // Register event → listener mapping
        // When ExpenseCreated is fired anywhere in the app,
        // Laravel automatically calls LogExpenseActivity::handle()
        Event::listen(
            ExpenseCreated::class,
            LogExpenseActivity::class,
        );
    }
}
