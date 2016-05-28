<?php

namespace Spatie\BladeJavascript;

use Illuminate\Contracts\Support\Jsonable;

class Renderer
{
    public static function render($data): string
    {
        $output = "<script type=\"text/javascript\">";

        $namespace = config('laravel-blade-javascript.namespace');

        if ($namespace) {
            $output .= "window.{$namespace} = window.{$namespace} || {};";
        }

        $output .= static::formatData($data);

        $output .= "</script>";

        return $output;
    }

    protected function formatData($data): string
    {
        if ($data instanceof Jsonable) {
            return $data->toJson();
        }

        return json_encode($data);
    }
}
