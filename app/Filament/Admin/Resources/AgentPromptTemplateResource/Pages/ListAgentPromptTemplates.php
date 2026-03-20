<?php

namespace App\Filament\Admin\Resources\AgentPromptTemplateResource\Pages;

use App\Filament\Admin\Resources\AgentPromptTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentPromptTemplates extends ListRecords
{
    protected static string $resource = AgentPromptTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
