<?php

namespace Spatie\Permission\Test;

use Spatie\BladeJavaScript\Test\TestCase;

class BladeTest extends TestCase
{
    /** @test */
    public function it_renders() {

        $this->assertEquals('this one', $this->renderView('index'));
    }
}