<?php

namespace Spatie\BladeJavaScript;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\BladeJavaScript\Transformers\Transformer;
use Spatie\BladeJavaScript\Exceptions\Untransformable;
use Spatie\BladeJavaScript\Transformers\NullTransformer;
use Spatie\BladeJavaScript\Transformers\ArrayTransformer;
use Spatie\BladeJavaScript\Transformers\ObjectTransformer;
use Spatie\BladeJavaScript\Transformers\StringTransformer;
use Spatie\BladeJavaScript\Transformers\BooleanTransformer;
use Spatie\BladeJavaScript\Transformers\NumericTransformer;

class Renderer
{
    protected $namespace = 'window';

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
        $this->namespace = $config->get('blade-javascript.namespace', 'window');
    }

    /**
     * @param array ...$arguments
     *
     * @return string
     *
     * @throws \Throwable
     */
    public function render(...$arguments): string
    {
        $variables = $this->normalizeArguments($arguments);

        return view('bladeJavaScript::index', [
            'javaScript' => $this->buildJavaScriptSyntax($variables),
        ])->render();
    }

    /**
     * @param $arguments
     *
     * @return mixed
     */
    protected function normalizeArguments(array $arguments)
    {
        if (count($arguments) === 2) {
            return [$arguments[0] => $arguments[1]];
        }

        if ($arguments[0] instanceof Arrayable) {
            return $arguments[0]->toArray();
        }

        if (! is_array($arguments[0])) {
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
                return $javaScriptSyntax.$variableInitialization;
            }, $this->buildNamespaceDeclaration());
    }

    protected function buildNamespaceDeclaration(): string
    {
        if (empty($this->namespace)) {
            return '';
        }

        return "window['{$this->namespace}'] = window['{$this->namespace}'] || {};";
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return string
     */
    protected function buildVariableInitialization(string $key, $value)
    {
        $variableName = $this->namespace ? "window['{$this->namespace}']['{$key}']" : "window['{$key}']";

        return "{$variableName} = {$this->optimizeValueForJavaScript($value)};";
    }

    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws \Spatie\BladeJavaScript\Exceptions\Untransformable
     */
    protected function optimizeValueForJavaScript($value): string
    {
        return $this->getTransformer($value)->transform($value);
    }

    public function getAllTransformers(): Collection
    {
        return collect($this->transformers)->map(function (string $className): Transformer {
            return new $className();
        });
    }

    /**
     * @param mixed $value
     *
     * @return \Spatie\BladeJavaScript\Transformers\Transformer
     *
     * @throws \Spatie\BladeJavaScript\Exceptions\Untransformable
     */
    public function getTransformer($value): Transformer
    {
        foreach ($this->getAllTransformers() as $transformer) {
            if ($transformer->canTransform($value)) {
                return $transformer;
            }
        }

        throw Untransformable::noTransformerFound($value);
    }
}
