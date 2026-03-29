<?php

declare(strict_types=1);

namespace He4rt\Streamer\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use He4rt\Identity\ExternalIdentity\Actions\DisconnectIdentity;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;

class IntegrationsPage extends Page
{
    protected string $view = 'panel-streamer::filament.pages.integrations';

    protected static ?string $slug = 'integrations';

    protected static ?string $title = 'Integrations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?int $navigationSort = 2;

    /**
     * @return array<int, array{provider: IdentityProvider, identity: ExternalIdentity|null, connected: bool}>
     */
    public function getProviderCards(): array
    {
        $identities = ExternalIdentity::query()
            ->where('team_id', filament()->getTenant()->getKey())
            ->get()
            ->keyBy(fn (ExternalIdentity $i) => $i->provider->value);

        return collect(IdentityProvider::cases())
            ->map(fn (IdentityProvider $provider): array => [
                'provider' => $provider,
                'identity' => $identities->get($provider->value),
                'connected' => ($identity = $identities->get($provider->value)) !== null && $identity->isConnected(),
            ])
            ->all();
    }

    public function connectAction(): Action
    {
        return Action::make('connect')
            ->label('Connect')
            ->color('success')
            ->icon('heroicon-o-link')
            ->action(function (array $arguments): void {
                $provider = IdentityProvider::from($arguments['provider']);

                session()->put('identity_oauth_return', [
                    'panel' => 'streamer',
                    'tenant' => filament()->getTenant()->getKey(),
                ]);

                $this->redirect(route('identity.oauth.connect', $provider));
            });
    }

    public function disconnectAction(): Action
    {
        return Action::make('disconnect')
            ->requiresConfirmation()
            ->modalHeading('Disconnect Integration')
            ->modalDescription('Are you sure you want to disconnect this integration?')
            ->action(function (array $arguments): void {
                $identity = ExternalIdentity::query()->findOrFail($arguments['identity_id']);

                resolve(DisconnectIdentity::class)->handle($identity);

                Notification::make()
                    ->title('Integration disconnected')
                    ->success()
                    ->send();
            });
    }
}
