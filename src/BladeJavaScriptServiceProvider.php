<?php

namespace Spatie\BladeJavaScript;

use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\ServiceProvider;

class BladeJavaScriptServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/blade-javascript.php' => config_path('blade-javascript.php'),
        ], 'config');

        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('javascript', function ($expression) {
                $expression = $this->makeBackwardsCompatible($expression);

                return "<?= app('\Spatie\BladeJavaScript\Renderer')->render{$expression}; ?>";
            });
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
            __DIR__.'/../config/blade-javascript.php',
            'blade-javascript'
        );
    }
}
