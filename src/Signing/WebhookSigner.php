<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Signing;

use JustSteveKing\Webhooks\Contracts\Signing\SigningContract;

final readonly class WebhookSigner implements SigningContract
{
    public function __construct(
        private string $key,
    ) {
    }

    public function sign(array $payload): string
    {
        return hash_hmac(
            algo: 'sha256',
            data: json_encode(
                value: $payload,
                flags: JSON_THROW_ON_ERROR,
            ),
            key: $this->key,
        );
    }
}
