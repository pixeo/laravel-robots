<?php

Route::get('/robots.txt', [\Pixeo\RobotsTxt\Controllers\RobotsTxtController::class, 'index'])
    ->name('robots.txt');