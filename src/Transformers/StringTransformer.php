<?php

namespace Spatie\BladeJavaScript\Transformers;

class StringTransformer  implements Transformer
{
    public function canTransform($value): bool
    {
        return is_string($value);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function transform($value): string
    {
        return "'{$this->escape($value)}'";
    }

    protected function escape(string $value): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\'"], $value);
    }
}
