<?php

namespace Spatie\BladeJavaScript\Transformers;

interface Transformer
{
    public function canTransform($value): bool;

    public function transform($value);
}
