<?php

namespace Spatie\BladeJavaScript\Transformers;

class ArrayTransformer implements Transformer
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function canTransform($value): bool
    {
        return is_array($value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function transform($value): string
    {
        return json_encode($value);
    }
}
