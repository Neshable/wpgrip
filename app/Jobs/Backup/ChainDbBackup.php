<?php

namespace App\Jobs\Backup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SSHSiteConnect;
use Exception;

use Illuminate\Support\Facades\Bus;

use App\Models\Snapshot;
use App\Models\Site;
use App\Models\Backup;

use App\Jobs\Backup\Database\CreateRemoteDatabaseArchive;
use App\Jobs\Backup\Files\UploadRemoteArchive;
use App\Jobs\Backup\Files\DeleteRemoteArchive;

class ChainDbBackup implements ShouldQueue {

	use Dispatchable;
	use InteractsWithQueue;
	use Queueable;
	use SerializesModels;

	private $backup;

	public $type;

	/**
	 * ChainAll constructor.
	 */
	public function __construct( Backup $backup, string $type = 'scheduled' ) {
		$this->backup = $backup;
		$this->type   = $type;
	}

	/**
	 * Execute the job.
	 *
	 * @throws Exception
	 */
	public function handle() 
    {

		try 
        {
			$site = Site::find( $this->backup->site_id );
		} 
        catch ( \Throwable $e ) 
        {
			$this->error( 'Site not found: ' . $e->getMessage() );
			return;
		}

		try 
        {
			$snapshot = Snapshot::create(
				array(
					'enabled'   => true,
					'status'    => 'pending',
					'type'      => $this->type,
					'backup_id' => $this->backup->id,
					'tenant_id' => $this->backup->tenant_id,
				)
			);
		} catch ( \Throwable $e ) {
			$this->error( 'Error creating snapshot id: ' . $e->getMessage() );
			return;
		}

		Bus::chain(
			array(
				new CreateRemoteDatabaseArchive( $site, $snapshot->id ),
				new UploadRemoteArchive( $site, $snapshot->id ),
				new DeleteRemoteArchive( $site, $snapshot->id ),
			)
		)->catch(
			function ( Throwable $e ) {
				// A job within the chain has failed...
			}
		)->onQueue( 'longrunning' )->dispatch();
		// })->dispatch();
	}
}
