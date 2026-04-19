<?php

return [
    App\Providers\AppServiceProvider::class,
    \App\Core\Providers\CoreServiceProvider::class,
    \App\Core\Authorization\Providers\AuthorizationServiceProvider::class,
    \App\Core\UserManagement\Providers\UserManagementServiceProvider::class,
];
