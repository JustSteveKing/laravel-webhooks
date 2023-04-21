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
use JustSteveKing\Webhooks\Factories\WebhookFactory;

/**
 * @method static PendingWebhook to(string $url) Create a new pending webhook.
 * @see PendingWebhook
 */
final class Webhook extends Facade
{
    /**
     * @return class-string
     */
    protected static function getFacadeAccessor(): string
    {
        return WebhookFactory::class;
    }
}
