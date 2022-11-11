<?php

uses(Spatie\BladeJavaScript\Tests\TestCase::class)->in('.');

function renderView(string $viewName, array $withParameters = []): string
{
    return view($viewName)->with($withParameters)->render();
}
