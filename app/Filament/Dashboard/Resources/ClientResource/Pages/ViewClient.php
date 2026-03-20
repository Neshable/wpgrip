<?php

namespace App\Filament\Dashboard\Resources\ClientResource\Pages;

use App\Filament\Dashboard\Resources\ClientResource;
use App\Models\Client;
use App\Models\ClientActivity;
use App\Models\ClientContact;
use App\Models\ClientNote;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.dashboard.resources.client-resource.pages.view-client';

    // Livewire state for notes
    public string $newNote = '';

    // Livewire state for contacts
    public string $contactName  = '';
    public string $contactEmail = '';
    public string $contactPhone = '';
    public string $contactRole  = 'other';

    public function getTitle(): string|Htmlable
    {
        return $this->record->display_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    // -------------------------------------------------------------------------
    // Notes
    // -------------------------------------------------------------------------

    public function addNote(): void
    {
        $body = trim($this->newNote);
        if (empty($body)) {
            return;
        }

        ClientNote::create([
            'client_id' => $this->record->id,
            'user_id'   => auth()->id(),
            'body'      => $body,
        ]);

        $this->record->logActivity('note_added', 'Note added');

        $this->newNote = '';
        $this->record->refresh();

        Notification::make()->title('Note added')->success()->send();
    }

    public function togglePin(int $noteId): void
    {
        $note = ClientNote::where('client_id', $this->record->id)->findOrFail($noteId);
        $note->update(['is_pinned' => !$note->is_pinned]);
        $this->record->refresh();
    }

    public function deleteNote(int $noteId): void
    {
        ClientNote::where('client_id', $this->record->id)->findOrFail($noteId)->delete();
        $this->record->logActivity('note_deleted', 'Note deleted');
        $this->record->refresh();

        Notification::make()->title('Note deleted')->success()->send();
    }

    // -------------------------------------------------------------------------
    // Contacts
    // -------------------------------------------------------------------------

    public function addContact(): void
    {
        $name = trim($this->contactName);
        if (empty($name)) {
            Notification::make()->title('Name is required')->danger()->send();
            return;
        }

        $contact = ClientContact::create([
            'client_id' => $this->record->id,
            'name'      => $name,
            'email'     => trim($this->contactEmail) ?: null,
            'phone'     => trim($this->contactPhone) ?: null,
            'role'      => $this->contactRole,
            'is_primary' => $this->record->contacts()->count() === 0,
        ]);

        $this->record->logActivity('contact_added', "Contact added: {$name}", [
            'contact_id' => $contact->id,
            'role'       => $this->contactRole,
        ]);

        $this->contactName  = '';
        $this->contactEmail = '';
        $this->contactPhone = '';
        $this->contactRole  = 'other';
        $this->record->refresh();

        Notification::make()->title('Contact added')->success()->send();
    }

    public function setPrimaryContact(int $contactId): void
    {
        $this->record->contacts()->update(['is_primary' => false]);
        ClientContact::where('client_id', $this->record->id)->findOrFail($contactId)
            ->update(['is_primary' => true]);
        $this->record->refresh();

        Notification::make()->title('Primary contact updated')->success()->send();
    }

    public function deleteContact(int $contactId): void
    {
        $contact = ClientContact::where('client_id', $this->record->id)->findOrFail($contactId);
        $name = $contact->name;
        $contact->delete();

        $this->record->logActivity('contact_deleted', "Contact removed: {$name}");
        $this->record->refresh();

        Notification::make()->title('Contact removed')->success()->send();
    }
}
