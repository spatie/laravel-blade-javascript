<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class PhpArray implements Transformer
{
    public function canTransform($value): string
    {
        return is_array($value);
    }

    public function transform($value)
    {
        return json_encode($value);
    }
}
