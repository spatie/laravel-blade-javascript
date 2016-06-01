<?php

namespace Spatie\BladeJavaScript\Test;

use Artisan;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\BladeJavaScript\BladeJavaScriptServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
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
     * @param array $withParameters
     *
     * @return string
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function renderView($viewName, $withParameters = [])
    {
        return view($viewName)->with($withParameters)->render();
    }
}
