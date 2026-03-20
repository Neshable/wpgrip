<?php

namespace App\Filament\Admin\Resources\AgentPromptTemplateResource\Pages;

use App\Filament\Admin\Resources\AgentPromptTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgentPromptTemplate extends EditRecord
{
    protected static string $resource = AgentPromptTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
