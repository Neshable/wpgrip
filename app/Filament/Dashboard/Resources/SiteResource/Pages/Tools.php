<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Resources\Pages\ViewRecord;

use App\Services\GripNotifications;
use Illuminate\Support\Facades\Http;
use App\Enums\CachingSystem;
use App\Jobs\Site\CustomCLICommand;
use App\Services\WPCliService;

use Illuminate\Contracts\View\View;
use Filament\Actions\Action;
use Filament\Forms\Components;


class Tools extends ViewRecord
{

    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.tools';

    /**
     * Stores the output of the last executed CLI command.
     */
    public string $commandOutput = '';

    /**
     * The label for the last command run, shown above the output.
     */
    public string $lastCommandLabel = '';

    // -------------------------------------------------------------------------
    // Helper: run a command, capture output, update Livewire state
    // -------------------------------------------------------------------------

    private function runCommand( string $cmd, string $label ): void
    {
        $result = CustomCLICommand::dispatchSync( $this->record, $cmd );

        $this->lastCommandLabel = $label;

        if ( $result !== false ) {
            $this->commandOutput = (string) $result;
        } else {
            $this->commandOutput = '(Command failed or returned no output.)';
        }
    }

    // -------------------------------------------------------------------------
    // Cache clearing
    // -------------------------------------------------------------------------

    public function clearOtherCache(): Action
    {
        return Action::make('clearOtherCache')
            ->requiresConfirmation()
            ->color('info')
            ->label('Clear cache')
            ->modalHeading('Clear plugin cache')
            ->modalSubmitActionLabel('Clear cache')
            ->modalIcon('heroicon-o-arrow-path')
            ->modalDescription('Select the caching plugin installed on this site.')
            ->form([
                Components\Select::make('caching')
                    ->label('Caching system')
                    ->options(CachingSystem::class)
                    ->required(),
            ])
            ->action(function (array $data): void {
                $cmd = WPCliService::clearCache($data['caching']);
                if ( empty($cmd) ) {
                    GripNotifications::getCustomFailure(
                        'No WP-CLI command available for that caching plugin.'
                    );
                    return;
                }
                $this->runCommand($cmd, 'Clear cache: ' . $data['caching']);
            });
    }

    // -------------------------------------------------------------------------
    // Predefined WP-CLI commands
    // -------------------------------------------------------------------------

    public function runVerifyChecksums(): void
    {
        $this->runCommand(
            WPCliService::verifyCoreChecksums(),
            'wp core verify-checksums'
        );
    }

    public function runDbCheck(): void
    {
        $this->runCommand(
            WPCliService::checkDatabase(),
            'wp db check'
        );
    }

    public function runUserList(): void
    {
        $this->runCommand(
            WPCliService::listUsers(),
            'wp user list'
        );
    }

    public function runRewriteFlush(): void
    {
        $this->runCommand(
            WPCliService::flushRewrites(),
            'wp rewrite flush --hard'
        );
    }

    // -------------------------------------------------------------------------
    // Custom WP-CLI command
    // -------------------------------------------------------------------------

    private static function validateCustomCommand( string $cmd ): ?string
    {
        $cmd = trim($cmd);

        if ( strlen($cmd) > 300 ) {
            return 'Command is too long (max 300 characters).';
        }

        if ( !str_starts_with($cmd, 'wp ') ) {
            return 'Command must start with "wp ".';
        }

        // Block dangerous subcommands that allow arbitrary code execution
        $blockedSubcommands = ['eval', 'shell', 'eval-file'];
        foreach ($blockedSubcommands as $sub) {
            if ( str_contains($cmd, 'wp ' . $sub) ) {
                return 'The "' . $sub . '" subcommand is not permitted.';
            }
        }

        // Block shell injection operators
        $blocked = [';', '&&', '||', '|', '`', '$(', '${', '>', '<'];
        foreach ($blocked as $token) {
            if ( str_contains($cmd, $token) ) {
                return 'Command contains a disallowed character or operator: ' . htmlspecialchars($token);
            }
        }

        return null;
    }

    public function runCustomCommand(): Action
    {
        return Action::make('runCustomCommand')
            ->requiresConfirmation()
            ->color('warning')
            ->label('Run custom WP-CLI command')
            ->modalHeading('Run a custom WP-CLI command')
            ->modalSubmitActionLabel('Run command')
            ->modalIcon('heroicon-o-command-line')
            ->modalDescription(
                'Only WP-CLI commands starting with "wp " are allowed. ' .
                'Shell operators (;, &&, |, >, <, backticks) are blocked.'
            )
            ->form([
                Components\TextInput::make('custom_command')
                    ->label('WP-CLI command')
                    ->placeholder('wp option get siteurl')
                    ->required()
                    ->maxLength(300)
                    ->rules([
                        'required',
                        'string',
                        'max:300',
                        function (string $attribute, mixed $value, \Closure $fail) {
                            $error = self::validateCustomCommand($value);
                            if ($error !== null) {
                                $fail($error);
                            }
                        },
                    ]),
            ])
            ->action(function (array $data): void {
                $cmd = trim($data['custom_command']);

                // Defense in depth: validate again in the action handler
                $error = self::validateCustomCommand($cmd);
                if ($error !== null) {
                    GripNotifications::getCustomFailure($error);
                    return;
                }

                $this->runCommand($cmd, $cmd);
            });
    }

    // -------------------------------------------------------------------------
    // Status check (kept from original)
    // -------------------------------------------------------------------------

    public function checkStatusCode(): void
    {
        $response = Http::get( $this->record->url );

        GripNotifications::getStatusCode( $response->status() );
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

}
