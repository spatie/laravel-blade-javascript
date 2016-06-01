<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class Boolean implements Transformer
{
    public function canTransform($value): bool
    {
        return is_bool($value);
    }

    /**
     * @param bool $value
     * @return string
     */
    public function transform($value)
    {
        return $value ? 'true' : 'false';
    }
}
