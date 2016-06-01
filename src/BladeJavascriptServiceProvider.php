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
            return "<?= \Spatie\BladeJavaScript\Renderer::render({$this->removeBrackets($expression)}); ?>";
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-blade-javascript.php',
            'laravel-blade-javascript'
        );
    }

    protected function removeBrackets(string $expression): string
    {
        return trim($expression, '()');
    }
}
