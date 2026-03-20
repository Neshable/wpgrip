<?php

namespace App\Livewire;

use Livewire\Component;
 
use App\Models\Site;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;


class ModifySiteBackupSettings extends Component implements HasForms
{
    use InteractsWithForms;
    
    public ?array $data = [];

    public Site $site;

    public function mount(Site $site): void
    {   
        $this->site = $site;
        $this->form->fill( $site->toArray() );
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
     
                Toggle::make('backup_enabled')
                    ->label('Enable backup')
                    ->required()
            ])
            ->statePath('data')
            ->model( $this->site );
            
    }
    
    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.modify-site-backup-settings');
    }
}
