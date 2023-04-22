# Laravel Webhooks

<!-- BADGES_START -->
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![Run Tests](https://github.com/JustSteveKing/laravel-webhooks/actions/workflows/tests.yml/badge.svg)](https://github.com/JustSteveKing/sdk-tools/actions/workflows/tests.yml)
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

[badge-release]: https://img.shields.io/packagist/v/juststeveking/laravel-webhooks.svg?style=flat-square&label=release
[badge-license]: https://img.shields.io/packagist/l/juststeveking/laravel-webhooks.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/juststeveking/laravel-webhooks.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/juststeveking/laravel-webhooks.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/juststeveking/laravel-webhooks
[license]: https://github.com/juststeveking/laravel-webhooks/blob/main/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/juststeveking/laravel-webhooks
<!-- BADGES_END -->

A simple webhook implementation for Laravel.

## Installation

```bash
composer require juststeveking/laravel-webhooks
```

## Publishing config

To publish the configuration file for this package, please run the following artisan command:

```bash
php artisan vendor:publish --provider="JustSteveKing\Webhooks\Providers\PackageServiceProvider" --tag="webhooks"
```

The config looks for a few ENV variables that you can set:

- `WEBHOOK_HEADER` - The header key to send the signature as, this defaults to `Signature`
- `WEBHOOK_SIGNING_KEY` - Your webhook signing key.
- `WEBHOOK_USER_AGENT` - The user agent you want to set on your request, defaults to `Laravel_Webhooks`
- `WEBHOOK_TIMEOUT` - The request timeout you want to set for sending the webhook, defaults to 15 seconds.

## Usage

Using the webhook facade all you need to pass through is the URL you want to send the webhook to.

```php
use JustSteveKing\Webhooks\Facades\Webhook;

$webhook = Webhook::to(
    url: 'https://your-url.com/',
)
```

This will return a `PendingWebhook` for you to use. This will load the signing key in from your configuration.

If you need/want to set the signing key per-webhook you will need to instantiate the `PendingWebhook` yourself:

```php
use JustSteveKing\Webhooks\Builder\PendingWebhook;
use JustSteveKing\Webhooks\Signing\WebhookSigner;

$webhook = new PendingWebhook(
    url: 'https://your-url.com/',
    signer: new WebhookSigner(
        key: 'your-signing-key',
    ),
);
```

The Pending Webhook has the following properties in the constructor:

- `url` - The URL you want to send the webhook to.
- `signer` - An instance of the webhook signer you want to use. This must implement the `SigningContract`.
- `payload` - You can pre-pass in the payload that you want to send in your webhook. This should be an `array`.
- `signed` - You can pre-pass in whether you want this webhook to be signed or not, the default is `true`
.- `signature` - You can pre-pass in the signature that you want to use to sign your Webhooks with.
- `request` - You can pre-pass in a `PendingRequest` that you want to use to send your webhooks, this is useful when you need to attach an API token to your Webhooks.

## A simple example

In the below example, we are sending a webhook to `https://your-url.com/` and sending it the first `Post` model that we find.

```php
use JustSteveKing\Webhooks\Facades\Webhook;

Webhook::to('https://your-url.com/')
    ->with(Post::query()->first()->toArray())
    ->send();
```

## A more complex example

In this example we want to send a webhook to `https://your-url.com/`, again passing it the first `Post` model.
However, this time we want to intercept the creation of the Request to attach the Bearer token for authentication.
We then want to dispatch the sending of this webhook to the queue.

```php
use Illuminate\Http\Client\PendingRequest;
use JustSteveKing\Webhooks\Facades\Webhook;

Webhook::to('https://your-url.com/')
    ->with(Post::query()->first()->toArray())
    ->intercept(fn (PendingRequest $request) => $request
        ->withToken('YOUR-BEARER-TOKEN'),
    )->queue('my-queue-name');
```

## Not signing the webhook

If you don't need to sign the webhook.

```php
use JustSteveKing\Webhooks\Facades\Webhook;

Webhook::to('https://your-url.com/')->with(
    Post::query()->first()->toArray()
)->notSigned()->send();
```

## Testing

To run the test:

```bash
composer run test
```

## Credits

- [Steve McDougall](https://github.com/JustSteveKing)
- [All Contributors](../../contributors)

## LICENSE

The MIT License (MIT). Please see [License File](./LICENSE) for more information.
