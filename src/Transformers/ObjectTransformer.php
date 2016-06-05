<?php

namespace Spatie\BladeJavaScript\Transformers;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use StdClass;

class ObjectTransformer implements Transformer
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function canTransform($value): bool
    {
        return is_object($value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws \Exception
     */
    public function transform($value): string
    {
        if (method_exists($value, 'toJson')) {
            return $value->toJson();
        }

        if ($value instanceof JsonSerializable || $value instanceof StdClass) {
            return json_encode($value);
        }

        if (!method_exists($value, '__toString')) {
            throw new Exception('Cannot transform this object to JavaScript.');
        }

        return "'{$value}'";
    }
}
