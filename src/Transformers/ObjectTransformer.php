<?php

namespace Spatie\BladeJavaScript\Transformers;

use JsonSerializable;
use Spatie\BladeJavaScript\Exceptions\Untransformable;
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
     * @throws \Spatie\BladeJavaScript\Exceptions\Untransformable
     *
     * @return string
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
            throw Untransformable::cannotTransformObject($value);
        }

        return "'{$value}'";
    }
}
