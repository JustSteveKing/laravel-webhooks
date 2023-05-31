<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Builder;

use Closure;
use Exception;
use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JsonException;
use JustSteveKing\HttpHelpers\Enums\Method;
use JustSteveKing\Webhooks\Contracts\Builder\PendingWebhookContract;
use JustSteveKing\Webhooks\Contracts\Signing\SigningContract;
use JustSteveKing\Webhooks\Jobs\DispatchWebhookRequest;

final class PendingWebhook implements PendingWebhookContract
{
    /**
     * @param SigningContract $signer
     * @param null|string $url
     * @param array $payload
     * @param bool $signed
     * @param string|null $signature
     * @param PendingRequest|null $request
     */
    public function __construct(
        public readonly SigningContract $signer,
        public readonly null|string $url = null,
        public array $payload = [],
        public bool $signed = true,
        public null|string $signature = null,
        public null|PendingRequest $request = null,
    ) {
    }

    /**
     * Enforce the webhook to be signed.
     *
     * @return PendingWebhook
     * @throws JsonException
     */
    public function signed(): PendingWebhook
    {
        $this->signed = true;

        return $this;
    }

    /**
     * Do not sign this webhook.
     *
     * @return PendingWebhook
     */
    public function notSigned(): PendingWebhook
    {
        $this->signed = false;

        return $this;
    }


    /**
     * Set the payload for the Webhook.
     *
     * @param array $payload
     * @return PendingWebhook
     */
    public function with(array $payload): PendingWebhook
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Intercept the Http Request to override options.
     *
     * @param Closure $callback
     * @return PendingWebhook
     */
    public function intercept(Closure $callback): PendingWebhook
    {
        $userAgent = strval(config('webhooks.user_agent.name'));

        if ($this->request) {
            $this->request->withUserAgent(
                userAgent: $userAgent,
            );

            $this->request = $callback($this->request);

            return $this;
        }

        $this->request = Http::withUserAgent(
            userAgent: $userAgent,
        );

        $this->request = $callback($this->request);

        return $this;
    }

    /**
     * Dispatch the webhook to be sent on a Queue.
     *
     * @param string|null $queue
     * @return PendingDispatch|PendingClosureDispatch
     * @throws JsonException
     */
    public function queue(null|string $queue = null): PendingDispatch|PendingClosureDispatch
    {
        return dispatch(new DispatchWebhookRequest(
            webhook: $this,
        ))->onQueue(
            queue: $queue,
        );
    }

    /**
     * Send the webhook.
     *
     * @param Method $method
     * @return Response
     * @throws JsonException
     * @throws Exception
     */
    public function send(Method $method = Method::POST): Response
    {
        if (null === $this->request) {
            $this->intercept(
                fn (PendingRequest $request) => $request
                    ->timeout(intval(config('webhooks.request.timeout'))),
            );
        }

        if ($this->signed) {
            /** @phpstan-ignore-next-line  */
            $this->request->withHeaders(
                headers: [
                    strval(config('webhooks.signing.header')) => $this->signature ?: $this->signed()->signature,
                ],
            );
        }

        /** @phpstan-ignore-next-line  */
        return $this->request->send(
            method: $method->value,
            url: $this->url,
        )->throw();
    }
}
