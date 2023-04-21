<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Facades;

use Closure;
use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;
use JustSteveKing\HttpHelpers\Enums\Method;
use JustSteveKing\Webhooks\Builder\PendingWebhook;

/**
 * @method static PendingWebhook sign() Create the webhook signature.
 * @method static PendingWebhook payload(array $payload) Set the payload for the Webhook.
 * @method static PendingWebhook intercept(Closure $callback) Intercept the Http Request to override options.
 * @method static PendingDispatch|PendingClosureDispatch queue(null|string $queue = null) Dispatch the webhook to be sent on a Queue.
 * @method static Response send(Method $method = Method::POST) Send the webhook.
 * @see PendingWebhook
 */
final class Webhook extends Facade
{
    /**
     * @return class-string
     */
    protected static function getFacadeAccessor(): string
    {
        return PendingWebhook::class;
    }
}
