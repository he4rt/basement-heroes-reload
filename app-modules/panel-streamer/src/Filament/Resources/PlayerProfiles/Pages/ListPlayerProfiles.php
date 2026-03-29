<?php

declare(strict_types=1);

namespace He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\PlayerProfileResource;

class ListPlayerProfiles extends ListRecords
{
    protected static string $resource = PlayerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
