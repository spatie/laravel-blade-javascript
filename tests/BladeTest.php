<?php

use Illuminate\Contracts\Support\Arrayable;

it('can render a key-value pair')
    ->expect(fn () => renderView('keyValue'))
    ->toEqual('<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>');

it('can render', function (mixed $parameter, string $expected) {
    expect(
        renderView('variable', compact('parameter'))
    )->toEqual($expected);
})
    ->with([
        'an array' => [
            'parameter' => fn () => ['key' => 'value'],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>',
        ],
        'a boolean with value of `true`' => [
            'parameter' => fn () => ['boolean' => true],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'boolean\'] = true;</script>',
        ],
        'a boolean with value of `false`' => [
            'parameter' => fn () => ['boolean' => false],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'boolean\'] = false;</script>',
        ],
        'an integer' => [
            'parameter' => fn () => ['number' => 5],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'number\'] = 5;</script>',
        ],
        'an float' => [
            'parameter' => fn () => ['number' => 5.5],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'number\'] = 5.5;</script>',
        ],
        'null' => [
            'parameter' => fn () => ['nothing' => null],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'nothing\'] = null;</script>',
        ],
        'a string with line breaks' => [
            'parameter' => fn () => ['string' => "This is\r\n a test"],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'string\'] = \'This is\\r\\n a test\';</script>',
        ],
        'a numeric string as a string' => [
            'parameter' => fn () => ['socialSecurity' => '123456789'],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'socialSecurity\'] = \'123456789\';</script>',
        ],
        'escapes tags in a string' => [
            'parameter' => fn () => ['string' => "This is a <tag>"],
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'string\'] = \'This is a \<tag\>\';</script>',
        ],
        'arrayable objects' => [
            'parameter' => fn () => new class () implements Arrayable {
                public function toArray()
                {
                    return ['arrayableKey' => 'arrayableValue'];
                }
            },
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'arrayableKey\'] = \'arrayableValue\';</script>',
        ],
        'JSON serializable objects' => [
            'parameter' => fn () => new class () implements JsonSerializable {
                public function jsonSerialize()
                {
                    return ['jsonKey' => 'jsonValue'];
                }
            },
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = {"jsonKey":"jsonValue"};</script>',
        ],
        'an object that implements `toJson`' => [
            'parameter' => fn () => new class () {
                public function toJson()
                {
                    return json_encode(['jsonKey' => 'jsonValue']);
                }
            },
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = {"jsonKey":"jsonValue"};</script>',
        ],
        'an object that implements `toString`' => [
            'parameter' => fn () => new class () {
                public function __toString()
                {
                    return 'string';
                }
            },
            'expected' => '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = \'string\';</script>',
        ],

    ]);

it('can render data without a namespace')
    ->tap(fn () => config()->set('blade-javascript.namespace', ''))
    ->expect(fn () => renderView('keyValue'))
    ->toEqual('<script>window[\'key\'] = \'value\';</script>');

it('cannot translate resources to Javascript')
    ->tap(fn () => renderView('variable', ['parameter' => fopen(__FILE__, 'r')]))
    ->throws(ErrorException::class);

it('can render a customized view')
    ->tap(fn () => view()->replaceNamespace('bladeJavaScript', [__DIR__ . '/resources/views/override']))
    ->expect(fn () => renderView('keyValue'))
    ->toEqual('<script type="application/javascript">window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>');
