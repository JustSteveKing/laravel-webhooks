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
     * @param string $url
     * @param SigningContract $signer
     * @param array $payload
     * @param string|null $signature
     * @param PendingRequest|null $request
     */
    public function __construct(
        public readonly string $url,
        public readonly SigningContract $signer,
        public array $payload = [],
        public null|string $signature = null,
        public null|PendingRequest $request = null,
    ) {
    }

    /**
     * Create the webhook signature.
     *
     * @return PendingWebhook
     * @throws JsonException
     */
    public function sign(): PendingWebhook
    {
        $this->signature = $this->signer->sign(
            payload: $this->payload,
        );

        return $this;
    }

    /**
     * Set the payload for the Webhook.
     *
     * @param array $payload
     * @return PendingWebhook
     */
    public function payload(array $payload): PendingWebhook
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
        $userAgent = 'Laravel_Webhooks';

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
            $this->intercept(fn (PendingRequest $request) => $request->timeout(15));
        }

        /** @phpstan-ignore-next-line  */
        $this->request->withHeaders(
            headers: [
                'Signature' => $this->signature ?: $this->sign()->signature,
            ],
        );

        /** @phpstan-ignore-next-line  */
        return $this->request->send(
            method: $method->value,
            url: $this->url,
        )->throw();
    }
}
