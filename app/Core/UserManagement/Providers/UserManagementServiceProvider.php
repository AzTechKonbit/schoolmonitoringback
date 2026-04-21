<?php

namespace App\Core\UserManagement\Providers;

use App\Core\Models\User;
use App\Core\UserManagement\Database\Seeders\UserManagementSeeder;
use App\Core\UserManagement\Relations\ProfileRelation;
use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config.php', 'usermanagement');
    }

    public function boot(): void
    {
        $this->registerMigrations();
        $this->registerRoutes();
        $this->registerSeeder();
        $this->registerRelation();
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
        $this->app->bind(UserManagementSeeder::class, function () {
            return new UserManagementSeeder();
        });

        $this->app->tag(UserManagementSeeder::class, 'module.seeders');
    }

    private function registerRelation()
    {
        User::resolveRelationUsing('profile', function ($userModel) {
            return new ProfileRelation($userModel);
        });

    }
}
