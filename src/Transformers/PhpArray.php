<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class PhpArray implements Transformer
{
    public function canTransform($value): bool
    {
        return is_array($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function transform($value): string
    {
        return json_encode($value);
    }
}
