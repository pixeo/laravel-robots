<?php
namespace Pixeo\RobotsTxt;

use Illuminate\Support\ServiceProvider;
use Pixeo\RobotsTxt\Controllers\RobotsTxtController;

class RobotsTxtProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
            __DIR__.'/../config/robots-txt.php' => config_path('robots-txt.php'),
             ]
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->app->make(RobotsTxtController::class);

        $this->mergeConfigFrom(__DIR__.'/../config/robots-txt.php', 'robots-txt');
    }
}
