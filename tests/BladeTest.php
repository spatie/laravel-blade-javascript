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
    public function it_can_render_a_string_with_line_breaks()
    {
        $parameter = ['string' => "This is\r\n a test"];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'string\'] = \'This is\\r\\n a test\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_a_numeric_string_as_a_string()
    {
        $parameter = ['socialSecurity' => '123456789'];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'socialSecurity\'] = \'123456789\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_escapes_tags_in_a_string()
    {
        $parameter = ['string' => "This is a <tag>"];

        $this->assertEquals(
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'string\'] = \'This is a \<tag\>\';</script>',
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
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'arrayableKey\'] = \'arrayableValue\';</script>',
            $this->renderView('variable', compact('parameter'))
        );
    }

    /** @test */
    public function it_can_render_json_serializable_objects()
    {
        $parameter = new class implements JsonSerializable {
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
        $parameter = new class {
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
        $parameter = new class {
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
        $this->app['config']->set('blade-javascript.namespace', '');

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

    /** @test */
    public function it_can_render_a_customized_view()
    {
        $this->app['view']->replaceNamespace('bladeJavaScript', [__DIR__.'/resources/views/override']);

        $this->assertEquals(
            '<script type="application/javascript">window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>',
            $this->renderView('keyValue')
        );
    }
}
