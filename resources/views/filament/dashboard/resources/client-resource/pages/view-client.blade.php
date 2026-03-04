<x-filament-panels::page>

@php
    $client     = $this->getRecord();
    $sites      = $client->sites;
    $contacts   = $client->contacts()->orderByDesc('is_primary')->orderBy('name')->get();
    $notes      = $client->notes()->with('user')->get();
    $pinnedNotes = $notes->where('is_pinned', true);
    $regularNotes = $notes->where('is_pinned', false);
    $activities = $client->activities()->with('user')->latest()->limit(30)->get();
    $tenant     = Filament\Facades\Filament::getTenant();
@endphp

{{-- HEADER CARD --}}
<div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
    <div class="flex flex-col sm:flex-row sm:items-start gap-6">
        <div class="flex-shrink-0 h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                {{ strtoupper(substr($client->name, 0, 1)) }}
            </span>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-xl font-semibold text-gray-950 dark:text-white truncate">{{ $client->name }}</h2>
                @if($client->status)
                    <x-filament::badge :color="$client->status->getColor()" :icon="$client->status->getIcon()">
                        {{ $client->status->getLabel() }}
                    </x-filament::badge>
                @endif
            </div>

            @if($client->company)
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $client->company }}</p>
            @endif

            <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                @if($client->email)
                    <span class="inline-flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-envelope" class="w-4 h-4" />
                        <a href="mailto:{{ $client->email }}" class="hover:text-primary-500">{{ $client->email }}</a>
                    </span>
                @endif
                @if($client->phone)
                    <span class="inline-flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-phone" class="w-4 h-4" />
                        <a href="tel:{{ $client->phone }}" class="hover:text-primary-500">{{ $client->phone }}</a>
                    </span>
                @endif
                @if($client->website)
                    <span class="inline-flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-globe-alt" class="w-4 h-4" />
                        <a href="{{ $client->website }}" target="_blank" class="hover:text-primary-500">{{ $client->website }}</a>
                    </span>
                @endif
                @if($client->country)
                    <span class="inline-flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-map-pin" class="w-4 h-4" />
                        {{ $client->city ? $client->city . ', ' : '' }}{{ $client->country }}
                    </span>
                @endif
                @if($client->timezone)
                    <span class="inline-flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-clock" class="w-4 h-4" />
                        {{ str_replace(['/', '_'], [' / ', ' '], $client->timezone) }}
                    </span>
                @endif
            </div>

            @if($client->tags && count($client->tags))
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($client->tags as $tag)
                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex-shrink-0 text-right space-y-1">
            @if($client->monthly_value)
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Monthly</span>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        {{ $client->currency ?? 'USD' }} {{ number_format($client->monthly_value, 2) }}
                    </p>
                </div>
            @endif
            @if($client->contract_end)
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Contract ends</span>
                    <p class="text-sm font-medium {{ $client->contract_end->isPast() ? 'text-red-500' : ($client->is_contract_expiring ? 'text-amber-500' : 'text-gray-600 dark:text-gray-300') }}">
                        {{ $client->contract_end->format('M d, Y') }}
                        <span class="text-xs">({{ $client->contract_end->diffForHumans() }})</span>
                    </p>
                </div>
            @endif
            @if($client->source)
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Source</span>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $client->source->getLabel() }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- STATS STRIP --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 text-center">
        <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $sites->count() }}</p>
        <p class="text-xs text-gray-400 uppercase tracking-wide">Sites</p>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 text-center">
        <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $contacts->count() }}</p>
        <p class="text-xs text-gray-400 uppercase tracking-wide">Contacts</p>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 text-center">
        <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $notes->count() }}</p>
        <p class="text-xs text-gray-400 uppercase tracking-wide">Notes</p>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 text-center">
        <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $client->created_at->diffInDays(now()) }}</p>
        <p class="text-xs text-gray-400 uppercase tracking-wide">Days as client</p>
    </div>
</div>

{{-- MAIN CONTENT: 2 COLUMNS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT COLUMN (2/3) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- SITES --}}
        <x-filament::section icon="heroicon-m-globe-alt" icon-color="primary">
            <x-slot name="heading">Connected Sites</x-slot>
            @if($sites->count())
                <div class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($sites as $site)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img class="w-5 h-5 rounded" src="https://s2.googleusercontent.com/s2/favicons?domain={{ $site->url }}" alt="">
                                <div class="min-w-0">
                                    <a href="{{ route('filament.dashboard.resources.sites.view', ['record' => $site->id, 'tenant' => $tenant->uuid]) }}"
                                       class="text-sm font-medium text-gray-900 dark:text-white hover:text-primary-500 truncate block">
                                        {{ $site->name }}
                                    </a>
                                    <p class="text-xs text-gray-400 truncate">{{ $site->url }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($site->ssh_connection)
                                    <span class="inline-flex items-center rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-0.5 text-xs text-green-600 dark:text-green-400">Connected</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-900/20 px-2 py-0.5 text-xs text-red-500">Disconnected</span>
                                @endif
                                @if($site->wp_ver)
                                    <span class="text-xs text-gray-400">WP {{ $site->wp_ver }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 py-4 text-center">No sites linked to this client.</p>
            @endif
        </x-filament::section>

        {{-- NOTES --}}
        <x-filament::section icon="heroicon-m-chat-bubble-left-right" icon-color="warning">
            <x-slot name="heading">Notes</x-slot>

            <div class="mb-4">
                <div class="flex gap-2">
                    <textarea
                        wire:model="newNote"
                        rows="2"
                        placeholder="Add a note..."
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500 resize-none"
                    ></textarea>
                    <button
                        wire:click="addNote"
                        wire:loading.attr="disabled"
                        class="self-end px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 disabled:opacity-50 transition"
                    >
                        <span wire:loading.remove wire:target="addNote">Add</span>
                        <span wire:loading wire:target="addNote">...</span>
                    </button>
                </div>
            </div>

            @foreach($pinnedNotes as $note)
                <div class="rounded-lg border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/10 p-3 mb-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <x-filament::icon icon="heroicon-s-star" class="w-3.5 h-3.5 text-amber-500" />
                                <span class="text-xs font-medium text-gray-500">{{ $note->user?->name ?? 'System' }}</span>
                                <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $note->body }}</p>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            <button wire:click="togglePin({{ $note->id }})" class="p-1 text-amber-500 hover:text-amber-600" title="Unpin">
                                <x-filament::icon icon="heroicon-s-star" class="w-4 h-4" />
                            </button>
                            <button wire:click="deleteNote({{ $note->id }})" wire:confirm="Delete this note?" class="p-1 text-gray-400 hover:text-red-500" title="Delete">
                                <x-filament::icon icon="heroicon-m-trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            @forelse($regularNotes as $note)
                <div class="py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-white/5' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-500">{{ $note->user?->name ?? 'System' }}</span>
                                <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $note->body }}</p>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            <button wire:click="togglePin({{ $note->id }})" class="p-1 text-gray-300 hover:text-amber-500" title="Pin">
                                <x-filament::icon icon="heroicon-o-star" class="w-4 h-4" />
                            </button>
                            <button wire:click="deleteNote({{ $note->id }})" wire:confirm="Delete this note?" class="p-1 text-gray-400 hover:text-red-500" title="Delete">
                                <x-filament::icon icon="heroicon-m-trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                @if($pinnedNotes->isEmpty())
                    <p class="text-sm text-gray-400 py-2 text-center">No notes yet.</p>
                @endif
            @endforelse
        </x-filament::section>
    </div>

    {{-- RIGHT COLUMN (1/3) --}}
    <div class="space-y-6">

        {{-- CONTACTS --}}
        <x-filament::section icon="heroicon-m-users" icon-color="info">
            <x-slot name="heading">Contacts</x-slot>

            @foreach($contacts as $contact)
                <div class="py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-white/5' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $contact->name }}</span>
                                @if($contact->is_primary)
                                    <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-900/20 px-1.5 py-0.5 text-[10px] font-medium text-primary-600 dark:text-primary-400">Primary</span>
                                @endif
                                @if($contact->role)
                                    <span class="text-xs text-gray-400 capitalize">{{ $contact->role }}</span>
                                @endif
                            </div>
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="text-xs text-gray-500 hover:text-primary-500 block mt-0.5">{{ $contact->email }}</a>
                            @endif
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="text-xs text-gray-500 hover:text-primary-500 block">{{ $contact->phone }}</a>
                            @endif
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            @if(!$contact->is_primary)
                                <button wire:click="setPrimaryContact({{ $contact->id }})" class="p-1 text-gray-300 hover:text-primary-500" title="Set as primary">
                                    <x-filament::icon icon="heroicon-o-star" class="w-4 h-4" />
                                </button>
                            @endif
                            <button wire:click="deleteContact({{ $contact->id }})" wire:confirm="Remove this contact?" class="p-1 text-gray-400 hover:text-red-500" title="Remove">
                                <x-filament::icon icon="heroicon-m-trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                <p class="text-xs font-medium text-gray-500 mb-2">Add contact</p>
                <div class="space-y-2">
                    <input wire:model="contactName" type="text" placeholder="Name *"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm px-3 py-1.5" />
                    <input wire:model="contactEmail" type="email" placeholder="Email"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm px-3 py-1.5" />
                    <input wire:model="contactPhone" type="text" placeholder="Phone"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm px-3 py-1.5" />
                    <select wire:model="contactRole"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm px-3 py-1.5">
                        <option value="owner">Owner</option>
                        <option value="developer">Developer</option>
                        <option value="billing">Billing</option>
                        <option value="marketing">Marketing</option>
                        <option value="other">Other</option>
                    </select>
                    <button wire:click="addContact" wire:loading.attr="disabled"
                        class="w-full px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        + Add Contact
                    </button>
                </div>
            </div>
        </x-filament::section>

        {{-- ACTIVITY TIMELINE --}}
        <x-filament::section icon="heroicon-m-clock" icon-color="gray">
            <x-slot name="heading">Activity</x-slot>

            @forelse($activities as $activity)
                <div class="flex gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-50 dark:border-white/5' : '' }}">
                    <div class="flex-shrink-0 mt-1">
                        @php
                            $iconMap = [
                                'note_added' => 'heroicon-m-chat-bubble-left',
                                'note_deleted' => 'heroicon-m-trash',
                                'contact_added' => 'heroicon-m-user-plus',
                                'contact_deleted' => 'heroicon-m-user-minus',
                                'status_changed' => 'heroicon-m-arrow-path',
                                'site_synced' => 'heroicon-m-arrow-path-rounded-square',
                                'backup_created' => 'heroicon-m-archive-box',
                                'manual' => 'heroicon-m-pencil-square',
                            ];
                            $icon = $iconMap[$activity->type] ?? 'heroicon-m-bolt';
                        @endphp
                        <x-filament::icon :icon="$icon" class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ $activity->description }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">
                            {{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-2 text-center">No activity yet.</p>
            @endforelse
        </x-filament::section>

        {{-- BILLING INFO --}}
        @if($client->billing_email || $client->address)
        <x-filament::section icon="heroicon-m-document-text" icon-color="success">
            <x-slot name="heading">Billing</x-slot>
            <dl class="space-y-2 text-sm">
                @if($client->billing_email)
                    <div>
                        <dt class="text-xs text-gray-400">Billing email</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ $client->billing_email }}</dd>
                    </div>
                @endif
                @if($client->address)
                    <div>
                        <dt class="text-xs text-gray-400">Address</dt>
                        <dd class="text-gray-700 dark:text-gray-300">
                            {{ $client->address }}<br>
                            {{ $client->city ? $client->city . ', ' : '' }}{{ $client->postal_code ?? '' }}<br>
                            {{ $client->country }}
                        </dd>
                    </div>
                @endif
                @if($client->currency && $client->currency !== 'USD')
                    <div>
                        <dt class="text-xs text-gray-400">Currency</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ $client->currency }}</dd>
                    </div>
                @endif
                @if($client->contract_start)
                    <div>
                        <dt class="text-xs text-gray-400">Contract period</dt>
                        <dd class="text-gray-700 dark:text-gray-300">
                            {{ $client->contract_start->format('M d, Y') }}
                            @if($client->contract_end)
                                — {{ $client->contract_end->format('M d, Y') }}
                            @else
                                — Ongoing
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>
        </x-filament::section>
        @endif
    </div>
</div>

</x-filament-panels::page>
