<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class Numeric implements Transformer
{
    public function canTransform($value): string
    {
        return is_numeric($value);
    }

    public function transform($value)
    {
        return $value;
    }
}
