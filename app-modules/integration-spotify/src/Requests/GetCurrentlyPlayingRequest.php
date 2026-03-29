<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetCurrentlyPlayingRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/me/player/currently-playing';
    }

    protected function defaultQuery(): array
    {
        return [
            'additional_types' => 'track',
        ];
    }
}
