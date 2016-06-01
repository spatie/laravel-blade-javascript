<?php

namespace Spatie\Permission\Test;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
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
    public function it_can_render_a_numeric_value()
    {
        $parameter = ['number' => 1];

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.number = 1;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_a_boolean()
    {
        $parameter = ['boolean' => true];

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.boolean = true;</script>',
            $this->renderView('variable', compact('parameter'))
        );

        $parameter = ['boolean' => false];

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.boolean = false;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_null()
    {
        $parameter = ['nothing' => null];

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.nothing = null;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_arrayable_objects()
    {
        $parameter = new class implements Arrayable
 {
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
    public function it_can_render_json_serializable_objects()
    {
        $parameter = new class implements JsonSerializable
        {
            public function jsonSerialize()
            {
                return ['jsonKey' => 'jsonValue'];
            }
        };

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.0 = {"jsonKey":"jsonValue"};</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_an_object_that_implements_toJson()
    {
        $parameter = new class
        {
            public function toJson()
            {
                return json_encode(['jsonKey' => 'jsonValue']);
            }
        };

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.0 = {"jsonKey":"jsonValue"};</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_an_object_that_implements_to_string()
    {
        $parameter = new class
        {
            public function __toString()
            {
                return 'string';
            }
        };

        $this->assertEquals(
            '<script type="text/javascript">window.js = window.js || {};js.0 = \'string\';</script>',
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
