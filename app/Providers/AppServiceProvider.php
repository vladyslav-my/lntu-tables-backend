<?php

namespace App\Providers;

use App\Models\BookedTable;
use App\Observers\BookedTableObserver;
use Illuminate\Http\Resources\Json\JsonResource;
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
        BookedTable::observe(BookedTableObserver::class);
        JsonResource::withoutWrapping();
    }
}
