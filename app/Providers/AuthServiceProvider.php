<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Product' => 'App\Policies\ModelPolicy',
        // 'App\Models\Role' => 'App\Policies\ModelPolicy',
        // 'App\Models\Category' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */

    public function register()
    {
        parent::register();

        $this->app->bind('abilities', function() { // Write in service container
            return include base_path('data/abilities.php');
        });
    }

    public function boot()
    {
        $this->registerPolicies();

        // Gate::define('categories.view', function($user) {
        //     return true;
        // });

        // Gate::before(function ($user, $ability) { // يتم استدعاؤها قبل الميثود الخاصة بالصلاحيات
        //     if ($user->super_admin) {
        //         return true;
        //     }
        // });

        foreach ($this->app->make('abilities') as $code => $lable) { // Read from service container
            Gate::define($code, function($user) use ($code) {
                return $user->hasAbility($code);
            });
        }
    }
}
