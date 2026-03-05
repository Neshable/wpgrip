@extends('repo/single/pagetemplate')

@section('content')

    @php
        $record = $this->getRecord();
        $tenant = Filament\Facades\Filament::getTenant();
        $webhookUrl = url('/webhook/git/' . $record->webhook);
        $publicKey = $tenant->getPublicKey();
        $providerName = ucfirst($record->provider ?? 'provider');
        
        // Build provider-specific URLs
        $repoUrl = match($record->provider) {
            'github' => str_replace(['git@github.com:', '.git'], ['https://github.com/', ''], $record->remote),
            'bitbucket' => str_replace(['git@bitbucket.org:', '.git'], ['https://bitbucket.org/', ''], $record->remote),
            default => '#',
        };
        $accessKeysUrl = match($record->provider) {
            'bitbucket' => $repoUrl . '/admin/access-keys/',
            'github' => $repoUrl . '/settings/keys',
            default => '#',
        };
        $webhooksSettingsUrl = match($record->provider) {
            'bitbucket' => $repoUrl . '/admin/webhooks',
            'github' => $repoUrl . '/settings/hooks',
            default => '#',
        };
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- SSH Key Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-key" class="h-5 w-5 text-amber-500" />
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">SSH Public Key</h3>
                </div>
                @if($accessKeysUrl !== '#')
                <x-filament::button
                    :href="$accessKeysUrl"
                    tag="a"
                    target="_blank"
                    color="gray"
                    size="xs"
                    icon="heroicon-m-arrow-top-right-on-square"
                >
                    {{ $providerName }} Access Keys
                </x-filament::button>
                @endif
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Add this public key to your <strong>{{ $providerName }}</strong> repository's <strong>Access Keys</strong> to allow the server to pull code.
            </p>
            @if($publicKey)
            <div class="relative" x-data="{ copied: false }">
                <pre class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 pr-10 text-xs font-mono text-gray-600 dark:text-gray-300 overflow-x-auto whitespace-pre-wrap break-all max-h-24 overflow-y-auto">{{ $publicKey }}</pre>
                <button 
                    class="absolute top-2 right-2 p-1.5 rounded-md bg-white dark:bg-gray-700 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    x-on:click="navigator.clipboard.writeText(@js($publicKey)); copied = true; setTimeout(() => copied = false, 2000)"
                >
                    <template x-if="!copied">
                        <x-filament::icon icon="heroicon-o-clipboard" class="h-4 w-4 text-gray-400" />
                    </template>
                    <template x-if="copied">
                        <x-filament::icon icon="heroicon-o-check" class="h-4 w-4 text-green-500" />
                    </template>
                </button>
            </div>
            @else
            <div class="bg-amber-50 dark:bg-amber-950/30 rounded-lg p-3">
                <p class="text-xs text-amber-700 dark:text-amber-400">No SSH key found for this tenant. Go to Workspace Settings to generate one.</p>
            </div>
            @endif
        </div>

        {{-- Webhook Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-bolt" class="h-5 w-5 text-blue-500" />
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Webhook (Push to Deploy)</h3>
                </div>
                @if($webhooksSettingsUrl !== '#')
                <x-filament::button
                    :href="$webhooksSettingsUrl"
                    tag="a"
                    target="_blank"
                    color="gray"
                    size="xs"
                    icon="heroicon-m-arrow-top-right-on-square"
                >
                    {{ $providerName }} Webhooks
                </x-filament::button>
                @endif
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Add this URL as a webhook in your <strong>{{ $providerName }}</strong> repository settings to enable automatic deployments on push.
            </p>
            <div class="relative" x-data="{ copied: false }">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 pr-10 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-link" class="h-4 w-4 text-gray-400 flex-shrink-0" />
                    <code class="text-xs font-mono text-gray-600 dark:text-gray-300 break-all">{{ $webhookUrl }}</code>
                </div>
                <button 
                    class="absolute top-2 right-2 p-1.5 rounded-md bg-white dark:bg-gray-700 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    x-on:click="navigator.clipboard.writeText(@js($webhookUrl)); copied = true; setTimeout(() => copied = false, 2000)"
                >
                    <template x-if="!copied">
                        <x-filament::icon icon="heroicon-o-clipboard" class="h-4 w-4 text-gray-400" />
                    </template>
                    <template x-if="copied">
                        <x-filament::icon icon="heroicon-o-check" class="h-4 w-4 text-green-500" />
                    </template>
                </button>
            </div>
            <div class="mt-3 bg-blue-50 dark:bg-blue-950/30 rounded-lg p-3">
                <p class="text-xs text-blue-700 dark:text-blue-400">
                    <strong>Setup:</strong>
                    @if($record->provider === 'bitbucket')
                        In Bitbucket, go to <em>Repository Settings → Webhooks → Add webhook</em>. Paste the URL above, select <strong>"Repository push"</strong> trigger.
                    @elseif($record->provider === 'github')
                        In GitHub, go to <em>Settings → Webhooks → Add webhook</em>. Paste the URL above, set Content type to <strong>application/json</strong>, select <strong>"Just the push event"</strong>.
                    @else
                        Add the URL above as a webhook in your repository provider settings, triggered on push events.
                    @endif
                    Then enable <strong>"Push to Deploy"</strong> per connected site below.
                </p>
            </div>
        </div>

    </div>
    
    @livewire('list-repo-sites', ['repo_model' => $record ] )

    @livewire('list-deployments', ['repository_id' => $record->id ])
    
@endsection

