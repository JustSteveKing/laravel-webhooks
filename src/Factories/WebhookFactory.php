<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Factories;

use JustSteveKing\Webhooks\Builder\PendingWebhook;
use JustSteveKing\Webhooks\Contracts\Factories\FactoryContract;
use JustSteveKing\Webhooks\Contracts\Signing\SigningContract;

final readonly class WebhookFactory implements FactoryContract
{
    /**
     * @param SigningContract $signer
     */
    public function __construct(
        private SigningContract $signer,
    ) {}

    /**
     * Set the URL you want to send this payload to.
     *
     * @param string $url
     * @return PendingWebhook
     */
    public function to(string $url): PendingWebhook
    {
        return new PendingWebhook(
            url: $url,
            signer: $this->signer,
        );
    }
}
