<?php

declare(strict_types=1);

namespace He4rt\Streamer\Filament\Resources\PlayerProfiles;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages\CreatePlayerProfile;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages\EditPlayerProfile;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages\ListPlayerProfiles;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Schemas\PlayerProfileForm;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Tables\PlayerProfilesTable;
use He4rt\WidgetPlayer\Models\PlayerProfile;
use Illuminate\Database\Eloquent\Builder;

class PlayerProfileResource extends Resource
{
    protected static ?string $model = PlayerProfile::class;

    protected static ?string $slug = 'player-profiles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static ?int $navigationSort = 1;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    /**
     * @return Builder<PlayerProfile>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($tenant = Filament::getTenant()) {
            $query->whereBelongsTo($tenant, 'team');
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return PlayerProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlayerProfilesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayerProfiles::route('/'),
            'create' => CreatePlayerProfile::route('/create'),
            'edit' => EditPlayerProfile::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Player Profile';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Player Profiles';
    }
}
