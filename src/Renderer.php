<?php

namespace Spatie\BladeJavaScript;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Renderer
{
    protected $namespace;

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

        return '<script type="text/javascript">'.$this->buildJavaScriptSyntax($variables).'</script>';
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
        $js = $this->buildNamespaceDeclaration();

        foreach ($variables as $key => $value) {
            $js .= $this->buildVariableInitialization($key, $value);
        }

        return $js;
    }

    protected function buildNamespaceDeclaration(): string
    {
        if ($this->namespace == '') {
            return '';
        }

        return "window.{$this->namespace} = window.{$this->namespace} || {};";
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return string
     */
    protected function buildVariableInitialization(string $key, $value)
    {
        $delimiter = $this->namespace ? '.' : '';

        return "{$this->namespace}{$delimiter}{$key} = {$this->optimizeValueForJavaScript($value)};";
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
        $transformers = $this->getAllTransformers();

        $transformers = $transformers->filter(function (Transformer $transformer) use ($value) {
           return $transformer->canTransform($value);
        });

        if ($transformers->isEmpty()) {
            throw new Exception("Cannot transform value {$value}");
        }

        return $transformers->first()->transform($value);
    }

    public function getAllTransformers(): Collection
    {
        return collect(glob(__DIR__.'/Transformers/*.php'))->map(function ($fileName) {
            $className = pathinfo($fileName, PATHINFO_FILENAME);

            $fullClassName = '\\Spatie\\BladeJavaScript\\Transformers\\'.$className;

            return new $fullClassName();
        });
    }
}
