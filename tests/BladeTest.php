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
            ['key' => 'value'],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>',
        ],
        'a boolean with value of `true`' => [
            ['boolean' => true],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'boolean\'] = true;</script>',
        ],
        'a boolean with value of `false`' => [
            ['boolean' => false],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'boolean\'] = false;</script>',
        ],
        'an integer' => [
            ['number' => 5],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'number\'] = 5;</script>',
        ],
        'an float' => [
            ['number' => 5.5],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'number\'] = 5.5;</script>',
        ],
        'null' => [
            ['nothing' => null],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'nothing\'] = null;</script>',
        ],
        'a string with line breaks' => [
            ['string' => "This is\r\n a test"],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'string\'] = \'This is\\r\\n a test\';</script>',
        ],
        'a numeric string as a string' => [
            ['socialSecurity' => '123456789'],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'socialSecurity\'] = \'123456789\';</script>',
        ],
        'escapes tags in a string' => [
            ['string' => "This is a <tag>"],
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'string\'] = \'This is a \<tag\>\';</script>',
        ],
        'arrayable objects' => [
            new class () implements Arrayable {
                public function toArray()
                {
                    return ['arrayableKey' => 'arrayableValue'];
                }
            },
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'arrayableKey\'] = \'arrayableValue\';</script>',
        ],
        'JSON serializable objects' => [
            new class () implements JsonSerializable {
                public function jsonSerialize()
                {
                    return ['jsonKey' => 'jsonValue'];
                }
            },
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = {"jsonKey":"jsonValue"};</script>',
        ],
        'an object that implements `toJson`' => [
            new class () {
                public function toJson()
                {
                    return json_encode(['jsonKey' => 'jsonValue']);
                }
            },
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = {"jsonKey":"jsonValue"};</script>',
        ],
        'an object that implements `toString`' => [
            new class () {
                public function __toString()
                {
                    return 'string';
                }
            },
            '<script>window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'0\'] = \'string\';</script>',
        ],

    ]);

it('can render data without a namespace', function () {
    config()->set('blade-javascript.namespace', '');
    expect(renderView('keyValue'))
        ->toEqual('<script>window[\'key\'] = \'value\';</script>');
});

it('cannot translate resources to Javascript')
    ->tap(fn () => renderView('variable', ['parameter' => fopen(__FILE__, 'r')]))
    ->throws(ErrorException::class);

it('can render a customized view', function () {
    view()->replaceNamespace('bladeJavaScript', [__DIR__ . '/resources/views/override']);
    expect(renderView('keyValue'))
        ->toEqual('<script type="application/javascript">window[\'js\'] = window[\'js\'] || {};window[\'js\'][\'key\'] = \'value\';</script>');
});
