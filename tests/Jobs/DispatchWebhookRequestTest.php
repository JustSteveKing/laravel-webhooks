<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use JustSteveKing\Webhooks\Facades\Webhook;
use JustSteveKing\Webhooks\Jobs\DispatchWebhookRequest;

it('can build a new dispatchable job', function (string $url): void {
    $job = new DispatchWebhookRequest(
        webhook: Webhook::to(
            url: $url,
        ),
    );

    expect(
        $job,
    )->toBeInstanceOf(DispatchWebhookRequest::class);
})->with('urls');

it('sends the webhook', function (string $url): void {
    Http::fake();

    $job = new DispatchWebhookRequest(
        webhook: Webhook::to(
            url: $url,
        ),
    );

    $job->handle();

    Http::assertSentCount(1);
})->with('urls');
