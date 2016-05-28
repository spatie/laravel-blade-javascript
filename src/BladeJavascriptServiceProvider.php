<?php

namespace Spatie\BladeJavascript;

use Illuminate\Support\ServiceProvider;

class BladeJavascriptServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-blade-javascript.php' => config_path('laravel-blade-javascript.php'),
        ], 'config');

        Blade::directive('javascript', function ($expression) {
            return "<?php \Spatie\BladeJavascript\Renderer::render({$expression}); ?>";
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-blade-javascript.php',
            'laravel-blade-javascript'
        );
    }
}
