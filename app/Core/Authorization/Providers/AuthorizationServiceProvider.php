<?php

namespace App\Core\Authorization\Providers;

use App\Core\Authorization\Database\Seeders\AuthorizationSeeder;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config.php', 'authorization');
    }

    public function boot(): void
    {
        $this->registerMigrations();
        $this->registerRoutes();
        $this->registerSeeder();
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__) . '/Database/Migrations');
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(dirname(__DIR__) . '/Routes/api.php');
    }
    protected function registerSeeder()
    {
        $this->app->bind(AuthorizationSeeder::class, function () {
            return new AuthorizationSeeder();
        });
        $this->app->tag(AuthorizationSeeder::class, 'module.seeders');
    }
}
