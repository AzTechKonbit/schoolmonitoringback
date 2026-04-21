<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\PersonalAccessToken;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('schedule', function () {
    PersonalAccessToken::where('updated_at', '<', now()->subMonth())
        ->delete();
    $this->comment("Token deleted");
})->purpose('Run clean token Access');
