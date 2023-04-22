<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Contracts\Builder;

use Closure;
use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Http\Client\Response;
use JustSteveKing\HttpHelpers\Enums\Method;

interface PendingWebhookContract
{
    /**
     * Create the webhook signature.
     * @return PendingWebhookContract
     */
    public function sign(): PendingWebhookContract;

    /**
     * Set the payload for the Webhook.
     *
     * @param array $payload
     * @return PendingWebhookContract
     */
    public function with(array $payload): PendingWebhookContract;

    /**
     * Intercept the Http Request to override options.
     *
     * @param Closure $callback
     * @return PendingWebhookContract
     */
    public function intercept(Closure $callback): PendingWebhookContract;

    /**
     * Dispatch the webhook to be sent on a Queue.
     *
     * @param string|null $queue
     * @return PendingDispatch|PendingClosureDispatch
     */
    public function queue(null|string $queue = null): PendingDispatch|PendingClosureDispatch;

    /**
     * Send the webhook.
     *
     * @param Method $method
     * @return Response
     */
    public function send(Method $method = Method::POST): Response;
}
