<?php

namespace Spatie\BladeJavaScript\Test;

use File;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\BladeJavaScript\BladeJavaScriptServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BladeJavaScriptServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [__DIR__.'/resources/views']);
    }

    /**
     * @param $viewName
     * 
     * @return string
     */
    public function renderView($viewName) {
        return view($viewName)->render();
    }
}