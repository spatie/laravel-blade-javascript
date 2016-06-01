<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class Boolean implements Transformer
{
    public function canTransform($value): string
    {
        return is_bool($value);
    }

    public function transform($value)
    {
        return $value ? 'true' : 'false';
    }
}
