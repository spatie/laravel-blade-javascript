<?php

namespace Spatie\BladeJavaScript;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;

class Renderer
{
    protected $namespace;

    /**
     * All transformable types.
     *
     * @var array
     */
    protected $types = [
        'String',
        'Array',
        'Object',
        'Numeric',
        'Boolean',
        'Null'
    ];

    public function __construct(Repository $config) {
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
     * @return array
     */
    protected function normalizeArguments($arguments)
    {
        if ($arguments[0] instanceof Arrayable) {
            return $arguments[0]->toArray();
        }

        if (count($arguments) == 2) {
            return [$arguments[0] => $arguments[1]];
        }

        return $arguments[0];
    }

    /**
     * @param  array $variables
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
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected function buildVariableInitialization($key, $value)
    {
        return "{$this->namespace}.{$key} = {$this->optimizeValueForJavaScript($value)};";
    }

    /**
     * @param  string $value
     * @throws Exception
     * @return string
     */
    protected function optimizeValueForJavaScript($value)
    {
        // For every transformable type, let's see if
        // it needs to be transformed for JS-use.

        foreach ($this->types as $transformer) {
            $js = $this->{"transform{$transformer}"}($value);

            if (!is_null($js)) {
                return $js;
            }
        }
    }

    /**
     * @param  string $value
     * @return string
     */
    protected function transformString($value)
    {
        if (is_string($value)) {
            return "'{$this->escape($value)}'";
        }
    }

    /**
     * @param  array $value
     * @return string
     */
    protected function transformArray($value)
    {
        if (is_array($value)) {

            return json_encode($value);
        }
    }

    /**
     * @param  mixed $value
     * @return mixed
     */
    protected function transformNumeric($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
    }

    /**
     * @param  boolean $value
     * @return string
     */
    protected function transformBoolean($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
    }

    /**
     * @param  object $value
     * @return string
     * @throws Exception
     */
    protected function transformObject($value)
    {
        if (!is_object($value)) {
            return;
        }

        if ($value instanceof JsonSerializable || $value instanceof StdClass) {
            return json_encode($value);
        }

        if (method_exists($value, 'toJson')) {
            return $value;
        }

        if (!method_exists($value, '__toString')) {
            throw new Exception('Cannot transform this object to JavaScript.');
        }

        return "'{$value}'";
    }

    /**
     * @param  mixed $value
     * @return string|null
     */
    protected function transformNull($value)
    {
        if (is_null($value)) {
            return 'null';
        }
    }

    /**
     * @param  string $value
     * @return string
     */
    protected function escape(string $value): string
    {
        return str_replace(["\\", "'"], ["\\\\", "\'"], $value);
    }


}
