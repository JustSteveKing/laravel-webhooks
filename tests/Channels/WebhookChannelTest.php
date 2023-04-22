<?php

declare(strict_types=1);

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JustSteveKing\Webhooks\Channels\WebhookChannel;
use JustSteveKing\Webhooks\Tests\Stubs\TestNotifiable;
use JustSteveKing\Webhooks\Tests\Stubs\TestNotification;

it('can send a notification', function (): void {
    Http::fake();

    $channel = new WebhookChannel();

    $channel->send(new TestNotifiable(), new TestNotification());

    Http::assertSentCount(1);
});
