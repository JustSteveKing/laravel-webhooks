<?php

declare(strict_types=1);

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use JustSteveKing\Webhooks\Builder\PendingWebhook;
use JustSteveKing\Webhooks\Jobs\DispatchWebhookRequest;
use JustSteveKing\Webhooks\Signing\WebhookSigner;

beforeEach(fn () => $this->signer = new WebhookSigner(key: 'test'));

test('it can build a new Pending Webhook')
    ->with('urls')
    ->expect(fn (string $url) => new PendingWebhook(
        url: $url,
        signer: $this->signer,
    ))->toBeInstanceOf(PendingWebhook::class);

test('it can set the webhook to be signed')
    ->with('urls')
    ->expect(fn (string $url) => (new PendingWebhook(
        url: $url,
        signer: $this->signer,
        payload: ['foo' => 'bar'],
    ))->signed()->signed)->toBeTrue();

test('it can set the webhook to not be signed')
    ->with('urls')
    ->expect(fn (string $url) => (new PendingWebhook(
        url: $url,
        signer: $this->signer,
        payload: ['foo' => 'bar'],
    ))->notSigned()->signed)->toBeFalse();

it('can set the payload', function (string $url): void {
    $pendingWebhook = new PendingWebhook(
        url: $url,
        signer: $this->signer
    );

    expect(
        $pendingWebhook->payload
    )->toBeArray()->toBeEmpty()->and(
        $pendingWebhook->with(['foo' => 'bar'])->payload,
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

it('dispatches a job', function (string $url): void {
    Bus::fake();

    $pendingWebhook = new PendingWebhook(
        url: $url,
        signer: $this->signer,
    );

    $pendingWebhook->queue(
        queue: 'test',
    );

    Bus::assertDispatched(
        command: DispatchWebhookRequest::class,
        callback: fn (DispatchWebhookRequest $job) => 'test' === $job->queue,
    );
})->with('urls');
