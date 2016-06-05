<?php

namespace Spatie\BladeJavaScript\Transformers;

class NullTransformer implements Transformer
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function canTransform($value): bool
    {
        return is_null($value);
    }

    /**
     * @param null $value
     *
     * @return string
     */
    public function transform($value): string
    {
        return 'null';
    }
}
