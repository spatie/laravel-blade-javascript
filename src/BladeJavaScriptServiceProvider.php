<?php

namespace Spatie\BladeJavaScript;

use Illuminate\Support\ServiceProvider;

class BladeJavaScriptServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/bladeJavaScript'),
        ], 'views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bladeJavaScript');

        if (! isset($this->app['blade.compiler'])) {
            $this->app['view'];
        }

        $this->app['blade.compiler']->directive('javascript', function ($expression) {
            $expression = $this->makeBackwardsCompatible($expression);

            return "<?= app('\Spatie\BladeJavaScript\Renderer')->render{$expression}; ?>";
        });
        
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }
        
        $this->publishes([
            __DIR__.'/../config/blade-javascript.php' => config_path('blade-javascript.php'),
        ], 'config');
    }

    public function makeBackwardsCompatible($expression)
    {
        return "({$expression})";
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/blade-javascript.php',
            'blade-javascript'
        );
    }
}
