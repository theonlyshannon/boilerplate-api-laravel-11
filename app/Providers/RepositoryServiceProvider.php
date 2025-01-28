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
