<?php

namespace Spatie\BladeJavaScript\Transformers;

class BooleanTransformer implements Transformer
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function canTransform($value): bool
    {
        return is_bool($value);
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    public function transform($value): string
    {
        return $value ? 'true' : 'false';
    }
}
