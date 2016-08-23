<?php

namespace Spatie\Permission\Test;

use ErrorException;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Spatie\BladeJavaScript\Test\TestCase;

class BladeTest extends TestCase
{
    /** @test */
    public function it_can_render_a_key_value_pair()
    {
        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>',
            $this->renderView('keyValue')
        );
    }

    /** @test */
    public function it_can_render_an_array()
    {
        $parameter = ['key' => 'value'];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_a_boolean()
    {
        $parameter = ['boolean' => true];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'boolean\'] = true;</script>',
            $this->renderView('variable', compact('parameter'))
        );

        $parameter = ['boolean' => false];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'boolean\'] = false;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_an_integer()
    {
        $parameter = ['number' => 5];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'number\'] = 5;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_a_float()
    {
        $parameter = ['number' => 5.5];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'number\'] = 5.5;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_null()
    {
        $parameter = ['nothing' => null];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'nothing\'] = null;</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_arrayable_objects()
    {
        $parameter = new class() implements Arrayable {
            public function toArray()
            {
                return ['arrayableKey' => 'arrayableValue'];
            }
        };

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'arrayableKey\'] = \'arrayableValue\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_json_serializable_objects()
    {
        $parameter = new class() implements JsonSerializable {
            public function jsonSerialize()
            {
                return ['jsonKey' => 'jsonValue'];
            }
        };

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = {"jsonKey":"jsonValue"};</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_an_object_that_implements_toJson()
    {
        $parameter = new class() {
            public function toJson()
            {
                return json_encode(['jsonKey' => 'jsonValue']);
            }
        };

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = {"jsonKey":"jsonValue"};</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_an_object_that_implements_to_string()
    {
        $parameter = new class() {
            public function __toString()
            {
                return 'string';
            }
        };

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = \'string\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_data_without_a_namespace()
    {
        $this->app['config']->set('laravel-blade-javascript.namespace', '');

        $this->assertEquals(
            '<script>window[\'key\'] = \'value\';</script>',
            $this->renderView('keyValue')
        );
    }

    /** @test */
    public function it_cannot_translate_resources_to_javascript()
    {
        $resource = fopen(__FILE__, 'r');

        $this->expectException(ErrorException::class);

        $this->renderView('variable', ['parameter' => $resource]);
    }
}
