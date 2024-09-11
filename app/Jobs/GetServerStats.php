<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\User;

use App\Services\SSHService;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Filament\Notifications\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetServerStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Server instance.
     *
     * @var \App\Models\Server
     */
    public $server;

    
     /**
     * The User instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The output array that we expect.
     * 
     * @var array
     */
    public $server_output = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Server $server )
    {
        $this->server = $server;
        $this->user = User::find( $this->server->user_id );
        $this->server_output = array(
            'cores' => 0,
            'memory' =>array(
                'total' => 0,
                'used' => 0,
                'available' => 0
            ),
            'connections' => 0,
            'disk' => array(
                'total' => 0,
                'used' => 0,
                'free' => 0,
                'used_perc' => 0
            ),
            'load' => 0 );
    }

    /**
     * Execute the job.
     * @author Kudos to https://github.com/jamesbachini/Server-Check-PHP/blob/master/servercheck.php
     *
     * @return void
     */
    public function handle()
    {
        // if ( !$this->server->ssh_user ) {
        //     $this->server->ssh_user = 'humadmin';
        // }

        if ( $this->server->ip ) {

            
            
            if ( !$this->user->ssh_private ) 
            {
                Notification::make()
                    ->title('Personal SSH Key not generated')
                    ->danger()
                    ->duration(5000)
                    ->send();

                return false;
            }

            // Init a new connection to the server. @todo change root
            $connection = new SSHService( 'root', $this->server->ip, 22, $this->user->ssh_private );
            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                Notification::make()
                    ->title('Login failed: Unauthorized.')
                    ->danger()
                    ->duration(5000)
                    ->send();
        
                return false;
            }

           
            
    
            $this->server_output['cores'] = (int) $connection->ssh->exec('nproc');

            $this->server_output['memory'] = $this->handle_memory_output( $connection->ssh->exec('free') );
            $this->server_output['connections'] = (int) $connection->ssh->exec('netstat -ntu | grep -E \':80 |443 \' | grep -v LISTEN | awk \'{print $5}\' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l');
            $this->server_output['disk'] = $this->handle_disk_output( $connection->ssh->exec('df -h /home') );
            $this->server_output['load'] = $this->handle_cpu_load( $connection->ssh->exec('uptime | cut -d \',\' -f 4-6 | sed -e \'s/^[ \t]*//\'') );

            // Close the connection if we don't need it.
            $connection->close();

            
            
            $this->save_to_db();
            // Send DB notification.
            $this->send_notification();
    
        }

       
    }

    /**
     * Get a readable array format from uptime command
     *
     * @return float
     */
    public function handle_cpu_load( string $free_output ) : ?float
    {
        $free_output = (string)trim($free_output);
        $free_arr = explode(",", $free_output); 

        if ( isset( $free_arr[1] ) ) {
            $average = (float) trim($free_arr[1], " ");
        } elseif ( isset( $free_arr[0] ) ) {
            $average = (float) trim($free_arr[0], "load average: ");
        }   

        return $average;

    }

    
    /**
     * Get a readable array format from the free command
     *
     * @return void
     */
    public function handle_memory_output( $free_output )
    {
        $free_output = (string)trim($free_output);
        $free_arr = explode("\n", $free_output);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
        $mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
        $memtotal = round($mem[1] / 1000000,2);
        $memused = round($mem[2] / 1000000,2);
        $memfree = round($mem[3] / 1000000,2);
        $memshared = round($mem[4] / 1000000,2);
        $memcached = round($mem[5] / 1000000,2);
        $memavailable = round($mem[6] / 1000000,2);

        return array(
            'total' => $memtotal,
            'used' =>  $memused,
            'available' => $memavailable
        );

    }

    /**
     * Get a readable array format from the DF command
     *
     * @return void
     */
    public function handle_disk_output( $free_output )
    {
        $free_output = (string)trim($free_output);
        $free_arr = explode("\n", $free_output);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
        $mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
        
        $total = (int)trim($mem[1], "G");
        $used = (int)trim($mem[2], "G");
        $free = (int)trim($mem[3], "G");
        
        // $used_perc = round($mem[4] / 1000000,2);
        // $free_percentage = ($total - ( $total - $free ) ) / ($total*100);
        $this->calculate_free_space( $total, $free );
       

        return array(
            'total' => $total,
            'used' =>  $used,
            'free' => $free,
            'used_perc' => round($used/$total*100)
        );

    }   

    /**
     * Check and return free disk space in percentage
     *
     * @param float $total
     * @param float $free
     * 
     * @return float Remaining space in percentage
     */
    public function calculate_free_space( float $total = 90, float $free = 22 ) : float
    {
        $threshold = 10; // Set to db option.
        $total_percentage = (($total - ( $total - $free ) ) / $total) *100;
       
        // Place some monitoring here or notificaiton.
        if ( $total_percentage <= $threshold ) {
            $this->user->notify(
                Notification::make()
                    ->title('Low disk space')
                    ->warning()
                    ->body( 'The remaining disk space on the server ' . $this->server->name . ' is below 10%. Please check.' ) 
                    ->toDatabase(),
            );
        }

        return $total_percentage;
    }

    /**
     * Send notification to UX user.
     *
     * @return void
     */
    public function send_notification()
    {

        $this->user->notify(
            Notification::make()
                ->title('Server stats gathered successfully')
                ->success()
                ->body( $this->server->cpu_load ) 
                ->toDatabase(),
        );
    }


    
    /**
     * Save the result in the DB
     *
     * @return void
     */
    public function save_to_db()
    {    
        
        $this->server->cpu_cores = $this->server_output['cores'];
        $this->server->cpu_load = $this->server_output['load'];

        $this->server->ram_free = $this->server_output['memory']['available'];
        $this->server->ram_total = $this->server_output['memory']['total'];

        $this->server->hdd_free = $this->server_output['disk']['free'];
        $this->server->hdd_total = $this->server_output['disk']['total'];

        $this->server->save();

    }

}
