<?php

namespace Spatie\BladeJavaScript;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeJavaScriptServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-blade-javascript.php' => config_path('laravel-blade-javascript.php'),
        ], 'config');

        Blade::directive('javascript', function ($expression) {
            $expression = $this->makeBackwardsCompatible($expression);

            return "<?= app('\Spatie\BladeJavaScript\Renderer')->render{$expression}; ?>";
        });
    }

    public function makeBackwardsCompatible($expression)
    {
        if (starts_with(app()->version(), ['5.1', '5.2'])) {
            return $expression;
        }

        return "({$expression})";
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-blade-javascript.php',
            'laravel-blade-javascript'
        );
    }
}
