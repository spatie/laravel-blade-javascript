<?php

namespace Spatie\BladeJavaScript\Transformers;

class NumericTransformer implements Transformer
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function canTransform($value): bool
    {
        return is_int($value) || is_float($value);
    }

    /**
     * @param float|int $value
     *
     * @return float|int
     */
    public function transform($value)
    {
        return $value;
    }
}
