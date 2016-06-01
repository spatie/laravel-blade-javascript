<?php

namespace Spatie\BladeJavaScript\Transformers;

use Exception;
use Spatie\BladeJavaScript\Transformer;

class Object implements Transformer
{
    public function canTransform($value): string
    {
        return is_object($value);
    }

    public function transform($value)
    {
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
}
