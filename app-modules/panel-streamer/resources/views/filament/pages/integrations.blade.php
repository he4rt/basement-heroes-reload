<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($this->getProviderCards() as $card)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $card['provider']->getLabel() }}
                    </h3>
                    <x-filament::badge :color="$card['connected'] ? 'success' : 'gray'">
                        {{ $card['connected'] ? 'Connected' : 'Not Connected' }}
                    </x-filament::badge>
                </div>

                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ $card['provider']->getType()->getLabel() }}
                </p>

                @if ($card['connected'] && $card['identity'])
                    <div class="mb-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        @if ($card['identity']->external_account_id)
                            <p><span class="font-medium">Account:</span> {{ $card['identity']->external_account_id }}</p>
                        @endif
                        <p><span class="font-medium">Connected:</span> {{ $card['identity']->connected_at->format('M d, Y') }}</p>
                    </div>
                    {{
                        ($this->disconnectAction)([
                            'identity_id' => $card['identity']->id,
                        ])
                    }}
                @else
                    {{
                        ($this->connectAction)([
                            'provider' => $card['provider']->value,
                        ])
                    }}
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
