<?php

declare(strict_types=1);

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use JustSteveKing\Webhooks\Builder\PendingWebhook;
use JustSteveKing\Webhooks\Signing\WebhookSigner;

beforeEach(fn () => $this->signer = new WebhookSigner(key: 'test'));

test('it can build a new Pending Webhook')
    ->with('urls')
    ->expect(fn (string $url) => new PendingWebhook(
        url: $url,
        signer: $this->signer,
    ))->toBeInstanceOf(PendingWebhook::class);

test('it can create a signature')
    ->with('urls')
    ->expect(fn (string $url) => (new PendingWebhook(
        url: $url,
        signer: $this->signer,
        payload: ['foo' => 'bar'],
    ))->sign()->signature)->toBeString();

it('can set the payload', function (string $url): void {
    $pendingWebhook = new PendingWebhook(
        url: $url,
        signer: $this->signer
    );

    expect(
        $pendingWebhook->payload
    )->toBeArray()->toBeEmpty()->and(
        $pendingWebhook->payload(['foo' => 'bar'])->payload,
    )->toBeArray()->toEqual(['foo' => 'bar']);
})->with('urls');

it('can intercept the http request', function (string $url): void {
    $pendingWebhook = new PendingWebhook(
        url: $url,
        signer: $this->signer
    );

    expect(
        $pendingWebhook->request
    )->toBeNull()->and(
        $pendingWebhook->intercept(fn (PendingRequest $request) => $request->timeout(15))->request,
    )->toBeInstanceOf(PendingRequest::class);
})->with('urls');

it('can intercept the http request if set on instantiation', function (string $url): void {
    $pendingWebhook = new PendingWebhook(
        url: $url,
        signer: $this->signer,
        request: Http::timeout(15),
    );

    expect(
        $pendingWebhook->request
    )->toBeInstanceOf(PendingRequest::class)->and(
        $pendingWebhook->intercept(fn (PendingRequest $request) => $request->timeout(20))->request
    )->toBeInstanceOf(PendingRequest::class);
})->with('urls');

it('can send the webhook', function (string $url): void {
    Http::fake();

    $pendingWebhook = new PendingWebhook(
        url: $url,
        signer: $this->signer,
    );

    $pendingWebhook->send();

    Http::assertSentCount(1);
})->with('urls');
