@php
    $tenant = Filament\Facades\Filament::getTenant();
    $record = $this->getRecord();
    
    // Type icon logic
    $typeIcon = match ($record->type) {
        'plugin' => 'icon-plugins',
        'theme' => 'icon-wordpress', // or a specific theme icon if you have one
        default => 'icon-wordpress',
    };
    
    $providerLabel = ucfirst($record->provider);
@endphp

<div class="flex flex-col bg-white border-gray-200 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-6 gap-6">
        
        <!-- Left Side: Repo Info -->
        <div class="flex items-start gap-4">
            <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-100 dark:border-white/5">
                <x-filament::icon
                    :icon="$typeIcon"
                    class="h-8 w-8 text-gray-500 dark:text-gray-400"
                />
            </div>
            
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ $record->name }}
                    </h2>
                    <x-filament::badge color="gray" size="sm">
                        {{ $providerLabel }}
                    </x-filament::badge>
                </div>
                
                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div 
                        class="flex items-center gap-1.5 font-mono group cursor-pointer transition-colors hover:text-gray-700 dark:hover:text-gray-300" 
                        title="Click to copy Remote Origin"
                        x-on:click="
                            window.navigator.clipboard.writeText('{{ $record->remote }}');
                            $tooltip('Copied to clipboard', { timeout: 1500 });
                        "
                    >
                        <x-filament::icon icon="heroicon-m-command-line" class="h-4 w-4 text-gray-400 group-hover:text-primary-500 transition-colors" />
                        <span class="truncate max-w-md">{{ $record->remote }}</span>
                        <x-filament::icon icon="heroicon-m-clipboard" class="h-3 w-3 opacity-0 group-hover:opacity-100 transition-opacity text-gray-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Actions -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <x-filament::button
                :href="match($record->provider) {
                    'github' => str_replace(['git@github.com:', '.git'], ['https://github.com/', ''], $record->remote),
                    'bitbucket' => str_replace(['git@bitbucket.org:', '.git'], ['https://bitbucket.org/', ''], $record->remote),
                    default => '#',
                }"
                tag="a"
                color="gray"
                icon="heroicon-m-arrow-top-right-on-square"
                target="_blank"
                outlined
            >
                View Source
            </x-filament::button>

            <x-filament::button
                :href="route('filament.dashboard.resources.repositories.edit', ['record' => $record->id, 'tenant' => $tenant->uuid])"
                tag="a"
                color="primary"
                icon="heroicon-m-cog-6-tooth"
            >
                Settings
            </x-filament::button>
        </div>
    </div>
</div>

