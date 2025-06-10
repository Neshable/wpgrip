<?php

namespace App\Services;

use App\Models\Site;
use App\Models\User;

use Carbon\Carbon;
use Filament\Notifications\Actions\Action;

use Filament\Notifications\Notification;

class GripNotifications {


    public static function getUnauthorizedNotificaiton()
    {
        return Notification::make()
            ->title( 'Login failed: Unauthorized.' )
            ->body( 'Make sure your public SSH key is added to the server\'s authorized keys.' )
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function getLiveToStagingError()
    {
        return Notification::make()
            ->title( 'Login failed: Unauthorized.' )
            ->body( 'Make sure your public SSH key from the live site is added to the staging server\'s authorized keys.' )
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function getInvalidDirectoryNotificaiton()
    {
        return Notification::make()
            ->title( 'Invalid directory' )
            ->body( 'The directory doesn\'t exist or it doesn\'t have .git folder inside' )
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function getCustomFailure( string $message )
    {
        return Notification::make()
            ->title( $message )
            ->danger()
            ->duration(5000)
            ->send();
    }

    /**
     * Cusotm general warning for all cases
     *
     * @param string $message
     * @param string $description
     * @return void
     */
    public static function getCustomWarning( string $message, string $description = '' )
    {
        return Notification::make()
            ->title( $message )
            ->body( $description )
            ->warning()
            ->duration(5000)
            ->send();
    }

    /**
     * Cusotm general warning for all cases
     *
     * @param string $message
     * @param string $description
     * @return void
     */
    public static function getCustomWarningToDB( string $message, string $description = '', $user_id = null )
    {
        if ( !$user_id )
        {
            return;
        }
        
        // Find the user id.   
        $user = User::find( $user_id );
        if ( $user )
        {
            $user->notify(
                Notification::make()
                    ->title($message)
                    ->warning()
                    ->body( $description ) 
                    ->toDatabase(),
            );
        }

    }
    /**
     * Trigger db notification when the screenshot VRT is different.
     *
     * @param string $message
     * @param string $description
     * @param Site $site
     * @return void
     */
    public static function getScreenshotMissmatchWarningToDB( string $message, string $description = '', Site $site )
    {
        if ( !isset( $site->user_id ) )
        {
            return;
        }
        
        // Find the user id.   
        $user = User::find( $site->user_id );
        
        if ( $user )
        {
            $user->notify(
                Notification::make()
                    ->title($message)
                    ->warning()
                    ->body( $description ) 
                    ->actions([
                        Action::make('view')
                            ->button()
                            ->url(route('filament.dashboard.resources.sites.tests', [ 
                                'record' => $site->id, 
                                'tenant' => $site->team->slug ] ), shouldOpenInNewTab: false)
       
                        // Action::make('undo')
                        //     ->color('gray'),
                    ])
                    ->toDatabase(),
            );
        }

    }


    public static function getCustomSuccess( string $message )
    {
        return Notification::make()
            ->title( $message )
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function getDatabaseSyncedNotification()
    {
        return Notification::make()
            ->title('Database successfully synced from live to staging.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function getSiteSyncedNotification()
    {
        return Notification::make()
            ->title('The site is successfully synced.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function getStagingSyncDispatched()
    {
        return Notification::make()
            ->title('Staging is now syncing from the live site.')
            ->info()
            ->duration(5000)
            ->send();
    }

    public static function notAllowedToRunTest()
    {
        return Notification::make()
            ->title('Your manual test limit is 1 per hour. Please try again later.')
            ->info()
            ->duration(5000)
            ->send();
    }

    public static function getStagingSyncComplete( $user_id = false )
    {
        if ( !$user_id )
        {
            return;
        }
        // Find the user id.
        
        $user = User::find( $user_id );
        if ( $user )
        {
            $user->notify(
                Notification::make()
                    ->title('Staging sync complete')
                    ->success()
                    ->body( 'Staging is now fully synced from the live site.' ) 
                    ->toDatabase(),
            );
        }
    }

    public static function getBackupFail( $user_id = false )
    {
        if ( !$user_id )
        {
            return;
        }
        // Find the user id.
        
        $user = User::find( $user_id );
        if ( $user )
        {
            $user->notify(
                Notification::make()
                    ->title('Backup failed.')
                    ->danger()
                    ->body( 'One of your backup jobs failed. Please check the logs.' ) 
                    ->toDatabase(),
            );
        }
    }

    public static function getProcessFail( $user_id = false, string $text )
    {
        if ( !$user_id )
        {
            return;
        }
        // Find the user id.
        
        $user = User::find( $user_id );
        if ( $user )
        {
            $user->notify(
                Notification::make()
                    ->title('There was an issue with some commands.')
                    ->danger()
                    ->body( $text ) 
                    ->toDatabase(),
            );
        }
    }

    public static function getGitPulledSuccess()
    {
        return Notification::make()
            ->title('Repository fetched and merged successfully.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function pluginUpdatedSuccess()
    {
        return Notification::make()
            ->title('The plugin has been updated.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function commandSuccess()
    {
        return Notification::make()
            ->title('The command has been executed successfully.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function fieldUpdated()
    {
        return Notification::make()
            ->title('The field has been updated.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function commandFail()
    {
        return Notification::make()
            ->title('The command failed to execute.')
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function pluginUpdatedFailed()
    {
        return Notification::make()
            ->title('The plugin has not been updated.')
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function getGitUpToDate()
    {
        return Notification::make()
            ->title('Repository already up to date. No need of deploy.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function getGitPulledFailed()
    {
        return Notification::make()
            ->title('An error occured. The repository was not pulled successfully.')
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function gitNoPublicKey()
    {
        return Notification::make()
            ->title('Permission Denied')
            ->title('Please check the permissions. The server public key should be added to your repository access keys.')
            ->danger()
            ->duration(5000)
            ->send();
    }

    public static function getVRTSuccessNotification()
    {
        return Notification::make()
            ->title('Screenshot captured.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function getNoDefaultScreenshotNotification()
    {
        return Notification::make()
            ->title('Missing default home screenshot.')
            ->warning()
            ->duration(5000)
            ->body('There is no default home screenshot for reference. Would you like to create a new one?')
            // ->actions([
            //     Action::make('create')
            //         ->button()
            //         ->dispatch('undoEditingPost', [$post->id])
            // ])
            ->send();
    }

    public static function getStatusCode( $code )
    {
        return Notification::make()
            ->title('Request resulted in status code: ' . $code )
            ->info()
            ->duration(5000)
            ->send();
    }

    
}