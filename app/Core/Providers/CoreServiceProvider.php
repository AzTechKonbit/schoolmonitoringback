<?php

namespace App\Core\Providers;

use App\Core\Database\Seeders\CoreSeeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config.php', 'core');
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
        $this->app->bind(CoreSeeder::class, function () {
            return new CoreSeeder();
        });

        $this->app->tag(CoreSeeder::class, 'module.seeders');
    }
}
