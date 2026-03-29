<?php

declare(strict_types=1);

namespace He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages;

use Filament\Resources\Pages\CreateRecord;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\PlayerProfileResource;
use Illuminate\Support\Str;

class CreatePlayerProfile extends CreateRecord
{
    protected static string $resource = PlayerProfileResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['browser_source_token'] = Str::random(32);

        return $data;
    }
}
