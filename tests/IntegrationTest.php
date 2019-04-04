<?php

namespace Pixeo\RobotsTxt\Tests;

use Orchestra\Testbench\TestCase;
use Pixeo\RobotsTxt\RobotsTxtProvider;

abstract class IntegrationTest extends TestCase
{
    /**
     * Get the service providers for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [RobotsTxtProvider::class];
    }
}