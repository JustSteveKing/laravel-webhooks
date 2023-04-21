<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Contracts\Factories;

use JustSteveKing\Webhooks\Contracts\Builder\PendingWebhookContract;

interface FactoryContract
{
    public function to(string $url): PendingWebhookContract;
}
