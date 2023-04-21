<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Contracts\Signing;

use JsonException;

interface SigningContract
{
    /**
     * Create a signature for the request.
     *
     * @param array $payload
     * @return string
     * @throws JsonException
     */
    public function sign(array $payload): string;
}
