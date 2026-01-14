<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

\Illuminate\Support\Facades\Schedule::command('sources:run')
    ->hourly()
    ->between("09:00", '22:30')
    ->withoutOverlapping();

\Illuminate\Support\Facades\Schedule::command('sources:marketplace999:metric:sale-dynamics')
    ->weeklyOn(1, '09:00')
    ->withoutOverlapping();
\Illuminate\Support\Facades\Schedule::command('sources:marketplace999:metric:ppm')
    ->weeklyOn(1, '09:00')
    ->withoutOverlapping();
