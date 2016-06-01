<?php

namespace Spatie\Permission\Test;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Spatie\BladeJavaScript\Test\TestCase;

class BladeTest extends TestCase
{
    /** @test */
    public function it_can_render_a_key_value_pair()
    {
        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.key = \'value\';</script>',
            $this->renderView('keyValue')
        );
    }

    /** @test */
    public function it_can_render_an_array()
    {
        $parameter = ['key' => 'value'];

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.key = \'value\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_arrayable_objects()
    {
        $parameter = new class implements Arrayable {
            public function toArray()
            {
                return ['arrayableKey' => 'arrayableValue'];
            }
        };

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.arrayableKey = \'arrayableValue\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_use_a_namespace_to_render_data()
    {
        $this->app['config']->set('laravel-blade-javascript.namespace', 'spatie');

        $this->assertEquals(
            '<script type="text/javascript">window.spatie = window.spatie || {};spatie.key = \'value\';</script>',
            $this->renderView('keyValue')
        );
    }
}
