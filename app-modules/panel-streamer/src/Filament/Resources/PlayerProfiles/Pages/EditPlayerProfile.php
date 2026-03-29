<?php

declare(strict_types=1);

namespace He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\PlayerProfileResource;

class EditPlayerProfile extends EditRecord
{
    protected static string $resource = PlayerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('copyBrowserSourceUrl')
                ->label('Copy Browser Source URL')
                ->icon('heroicon-o-clipboard-document')
                ->color('info')
                ->action(function (): void {
                    $url = route('widget-player.show', $this->getRecord()->browser_source_token);
                    $this->js(sprintf("navigator.clipboard.writeText('%s')", $url));
                    Notification::make()
                        ->title('Browser Source URL copied!')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
