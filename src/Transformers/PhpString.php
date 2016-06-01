<?php

namespace Spatie\BladeJavaScript\Transformers;

use Spatie\BladeJavaScript\Transformer;

class PhpString implements Transformer
{
    public function canTransform($value): string
    {
        return is_string($value);
    }

    public function transform($value)
    {
        return "'{$this->escape($value)}'";
    }

    protected function escape(string $value): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\'"], $value);
    }
}
