<?php

namespace Spatie\BladeJavaScript;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Renderer
{
    /**
     * @param array ...$arguments
     *
     * @return string
     */
    public static function render(...$arguments): string
    {
        $data = $arguments[0];

        if (count($arguments) == 2) {
            $data = [$arguments[0] => $arguments[1]];
        }

        $output = '<script type="text/javascript">';

        $namespace = config('laravel-blade-javascript.namespace');

        if ($namespace) {
            $output .= "window.{$namespace} = window.{$namespace} || {};";
        }

        $output .= static::formatData($data);

        $output .= '</script>';

        return $output;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected static function formatData($data): string
    {
        if ($data instanceof Jsonable) {
            return $data->toJson();
        }

        if ($data instanceof Arrayable) {
            return json_encode($data->toArray());
        }

        return json_encode($data);
    }
}
