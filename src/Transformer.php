<?php

namespace Spatie\BladeJavaScript;

interface Transformer
{
    public function canTransform($value);

    public function transform($value);
}
