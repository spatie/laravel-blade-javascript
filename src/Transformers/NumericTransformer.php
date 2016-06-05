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
        return is_numeric($value);
    }

    /**
     * @param int $value
     *
     * @return int
     */
    public function transform($value): int
    {
        return $value;
    }
}
