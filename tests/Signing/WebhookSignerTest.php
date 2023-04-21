<?php

declare(strict_types=1);

use JustSteveKing\Webhooks\Signing\WebhookSigner;

test('it can create a new webhook signer')
    ->with('keys')
    ->expect(fn (string $key) => new WebhookSigner(
        key: $key,
    ))->toBeInstanceOf(WebhookSigner::class);

test('it can sign a payload')
    ->with('keys')
    ->expect(fn (string $key) => (new WebhookSigner(
        key: $key,
    ))->sign(
        payload: ['foo' => 'bar'],
    ))->toBeString();
