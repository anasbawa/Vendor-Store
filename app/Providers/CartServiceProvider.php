<?php

namespace App\Providers;

use App\Repositories\Cart\CartModelRepositories;
use App\Repositories\Cart\CartRepositories;
use App\Repositories\Cart\CartRepository;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Write Object(CartModelRepositories) In Service Container Under Inteface(CartRepository) Name
        $this->app->bind(CartRepository::class, function() {
            return new CartModelRepositories();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
