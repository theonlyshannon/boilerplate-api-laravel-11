<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind(\App\Interfaces\ExampleRepositoryInterface::class, \App\Repositories\ExampleRepository::class);
        $this->app->bind(\App\Interfaces\AuthRepositoryInterface::class, \App\Repositories\AuthRepository::class);
    }




    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
