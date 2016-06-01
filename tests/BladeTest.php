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
            '<script type="text/javascript">{"key":"value"}</script>',
            $this->renderView('keyValue')
        );
    }

    /** @test */
    public function it_can_render_an_array()
    {
        $parameter = ['key' => 'value'];

        $this->assertEquals(
            '<script type="text/javascript">{"key":"value"}</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_jsonable_objects()
    {
        $parameter = new class implements Jsonable
 {
     /**
             * Convert the object to its JSON representation.
             *
             * @param int $options
             *
             * @return string
             */
            public function toJson($options = 0)
            {
                return json_encode(['jsonableKey' => 'jsonableValue']);
            }
 };

        $this->assertEquals(
            '<script type="text/javascript">{"jsonableKey":"jsonableValue"}</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_arrayable_objects()
    {
        $parameter = new class implements Arrayable
 {
     /**
             * Get the instance as an array.
             *
             * @return array
             */
            public function toArray()
            {
                return ['arrayableKey' => 'arrayableValue'];
            }
 };

        $this->assertEquals(
            '<script type="text/javascript">{"arrayableKey":"arrayableValue"}</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_use_a_namespace_to_render_data()
    {
        $this->app['config']->set('laravel-blade-javascript.namespace', 'spatie');

        $this->assertEquals(
            '<script type="text/javascript">window.spatie = window.spatie || {};{"key":"value"}</script>',
            $this->renderView('keyValue')
        );
    }
}
