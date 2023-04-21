<?php

declare(strict_types=1);

use JustSteveKing\Webhooks\Builder\PendingWebhook;
use JustSteveKing\Webhooks\Facades\Webhook;
use JustSteveKing\Webhooks\Factories\WebhookFactory;
use JustSteveKing\Webhooks\Signing\WebhookSigner;

test('it can create a new pending webhook')
    ->with('urls')
    ->expect(fn (string $url) => (new WebhookFactory(
        signer: new WebhookSigner(
            key: '1234',
        ),
    ))->to(
        url: $url
    ))->toBeInstanceOf(PendingWebhook::class);

test('it can create a new pending webhook with the facade')
    ->with('urls')
    ->expect(fn (string $url) => Webhook::to($url))
    ->toBeInstanceOf(PendingWebhook::class);
