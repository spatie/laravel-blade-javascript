<?php

namespace Spatie\BladeJavaScript;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;

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
     * @return array
     */
    protected function normalizeArguments($arguments)
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

    /**
     * @param array $variables
     *
     * @return array
     */
    public function buildJavaScriptSyntax($variables)
    {
        $js = $this->buildNamespaceDeclaration();

        foreach ($variables as $key => $value) {
            $js .= $this->buildVariableInitialization($key, $value);
        }

        return $js;
    }

    /**
     * @return string
     */
    protected function buildNamespaceDeclaration()
    {
        if ($this->namespace == 'window') {
            return '';
        }

        return "window.{$this->namespace} = window.{$this->namespace} || {};";
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function buildVariableInitialization($key, $value)
    {
        return "{$this->namespace}.{$key} = {$this->optimizeValueForJavaScript($value)};";
    }

    /**
     * @param string $value
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function optimizeValueForJavaScript($value)
    {
        $transformers = $this->getAllTransformers();

        $transformers = $transformers->filter(function (Transformer $transformer) use ($value) {
           return $transformer->canTransform($value);
        });

        if ($transformers->isEmpty()) {
            throw new \Exception("Cannot transform value {$value}");
        }

        return $transformers->first()->transform($value);
    }

    public function getAllTransformers()
    {
        return collect(glob(__DIR__.'/Transformers/*.php'))->map(function ($fileName) {
            $className = pathinfo($fileName, PATHINFO_FILENAME);

            $fullClassName = '\\Spatie\\BladeJavaScript\\Transformers\\'.$className;

            return new $fullClassName();
        });
    }
}
