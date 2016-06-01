<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class PhpNull implements Transformer
{
    public function canTransform($value): string
    {
        return is_null($value);
    }

    public function transform($value)
    {
        return 'null';
    }
}
