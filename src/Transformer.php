<?php

namespace Spatie\BladeJavaScript;

interface Transformer
{
    public function canTransform($value): bool;

    public function transform($value);
}
