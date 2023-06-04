
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# A Blade directive to export variables to JavaScript

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-blade-javascript.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-blade-javascript)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-blade-javascript/run-tests?label=tests)
![Check & fix styling](https://github.com/spatie/laravel-blade-javascript/workflows/Check%20&%20fix%20styling/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-blade-javascript.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-blade-javascript)

This package contains a Blade directive to export values to JavaScript.

Here's an example of how it can be used:

```php
@javascript('key', 'value')
```

The rendered view will output:
```html
<script>window['key'] = 'value';</script>
```

So in your browser you now have access to a key variable:
```js
console.log(key); //outputs "value"
```

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-blade-javascript.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-blade-javascript)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

``` bash
composer require spatie/laravel-blade-javascript
```

The package will automatically register itself.

Optionally the config file can be published with

```bash
php artisan vendor:publish --provider="Spatie\BladeJavaScript\BladeJavaScriptServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [

    /**
     * All passed values will be present in this JavaScript namespace. Set this to an empty string
     * to use the window object.
     */
    'namespace' => '',
];
```

If you want to customize the generated `<script>` tag you can publish and override the used view.

```bash
php artisan vendor:publish --provider="Spatie\BladeJavaScript\BladeJavaScriptServiceProvider" --tag="views"
```

After this you can edit the published view `resources/views/vendor/bladeJavaScript/index.blade.php`. This is usefull to add the `type` or a CSP `nonce`.

## Usage

With the package installed you can make use of a `@javascript` Blade directive.

```php
@javascript('key', 'value')
```

The rendered view will output:
```html
<script>key = 'value';</script>
```

You can also use a single argument:
```php
@javascript(['key' => 'value'])
```

This will also output:
```html
<script>key = 'value';</script>
```

When setting the namespace to eg `js` in the config file this will be the output:

```html
<script>window['js'] = window['js'] || {};js['key'] = 'value';</script>
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Sebastian De Deyne](https://github.com/seb)
- [All Contributors](../../contributors)

This repository contains some code from the [laracasts/PHP-Vars-To-Js-Transformer](https://github.com/laracasts/PHP-Vars-To-Js-Transformer) package written by [Jeffrey Way](https://github.com/JeffreyWay).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
