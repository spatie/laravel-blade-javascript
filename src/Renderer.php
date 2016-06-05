<?php

namespace Spatie\BladeJavaScript;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Spatie\BladeJavaScript\Transformers\ArrayTransformer;
use Spatie\BladeJavaScript\Transformers\BooleanTransformer;
use Spatie\BladeJavaScript\Transformers\NullTransformer;
use Spatie\BladeJavaScript\Transformers\NumericTransformer;
use Spatie\BladeJavaScript\Transformers\ObjectTransformer;
use Spatie\BladeJavaScript\Transformers\StringTransformer;
use Spatie\BladeJavaScript\Transformers\Transformer;

class Renderer
{
    protected $namespace;

    protected $transformers = [
        ArrayTransformer::class,
        BooleanTransformer::class,
        NullTransformer::class,
        NumericTransformer::class,
        ObjectTransformer::class,
        StringTransformer::class,
    ];

    public function __construct(Repository $config)
    {
        $this->namespace = $config->get('laravel-blade-javascript.namespace') ?? 'window';
    }

    /**
     * @param array ...$arguments
     *
     * @return string
     */
    public function render(...$arguments): string
    {
        $variables = $this->normalizeArguments($arguments);

        return '<script type="text/javascript">' . $this->buildJavaScriptSyntax($variables) . '</script>';
    }

    /**
     * @param $arguments
     *
     * @return mixed
     */
    protected function normalizeArguments(array $arguments)
    {
        if (count($arguments) == 2) {
            return [$arguments[0] => $arguments[1]];
        }

        if ($arguments[0] instanceof Arrayable) {
            return $arguments[0]->toArray();
        }

        if (!is_array($arguments[0])) {
            $arguments[0] = [$arguments[0]];
        }

        return $arguments[0];
    }

    public function buildJavaScriptSyntax(array $variables): string
    {
        return collect($variables)
            ->map(function ($value, $key) {
                return $this->buildVariableInitialization($key, $value);
            })
            ->reduce(function ($javaScriptSyntax, $variableInitialization) {
                return $javaScriptSyntax . $variableInitialization;
            }, $this->buildNamespaceDeclaration());
    }

    protected function buildNamespaceDeclaration(): string
    {
        if ($this->namespace == '') {
            return '';
        }

        return "window['{$this->namespace}'] = window['{$this->namespace}'] || {};";
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return string
     */
    protected function buildVariableInitialization(string $key, $value)
    {
        $variableName = $this->namespace ? "{$this->namespace}['{$key}']" : $key;

        return "{$variableName} = {$this->optimizeValueForJavaScript($value)};";
    }

    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws Exception
     */
    protected function optimizeValueForJavaScript($value): string
    {
        $transformers = $this->getAllTransformers()
            ->filter(function (Transformer $transformer) use ($value) {
                return $transformer->canTransform($value);
            });

        if ($transformers->isEmpty()) {
            throw new Exception("Cannot transform value {$value}");
        }

        return $transformers->first()->transform($value);
    }

    public function getAllTransformers(): Collection
    {
        return collect($this->transformers)->map(function ($className) {
            return new $className();
        });
    }
}
