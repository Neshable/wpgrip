<?php

namespace App\Jobs\Git;

use App\Models\Repository;
use App\Models\Deployment;
use App\Models\Site;
use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Filament\Notifications\Notification;

use App\Enums\RepoStatus;

class SshAndGitRevert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $pivot;
    public $repository;
    public $commit_hash;
    public $status;
    public $status_text;

    public function __construct(Repository $repository, Site $site, string $commit_hash)
    {
        $this->repository  = $repository;
        $this->site        = $site;
        $this->commit_hash = $commit_hash;
        $this->status      = RepoStatus::WORKING->value;
    }

    public function get_latest_git_commit()
    {
        return 'git log -n 1 --pretty=format:\'{%n  "commit": "%H",%n  "abbreviated_commit": "%h",%n  "tree": "%T",%n  "abbreviated_tree": "%t",%n  "parent": "%P",%n  "abbreviated_parent": "%p",%n  "refs": "%D",%n  "encoding": "%e",%n  "subject": "%s",%n  "sanitized_subject_line": "%f",%n  "body": "%b",%n  "commit_notes": "%N",%n  "verification_flag": "%G?",%n  "signer": "%GS",%n  "signer_key": "%GK",%n  "author": {%n    "name": "%aN",%n    "email": "%aE",%n    "date": "%aD"%n  },%n  "commiter": {%n    "name": "%cN",%n    "email": "%cE",%n    "date": "%cD"%n  }%n},\' | sed "$ s/,$//" | sed \':a;N;$!ba;s/\r\n\([^{]\)/\\n\1/g\'| awk \'BEGIN { print("[") } { print($0) } END { print("]") }\'';
    }

    public function handle()
    {
        $temp_site = $this->repository->sites->firstWhere('id', $this->site->id);

        if ($temp_site) {
            $this->pivot = $temp_site->pivot;
        } else {
            return false;
        }

        if (!$this->site->server) {
            $this->status_text = 'No server assigned.';
            GripNotifications::getCustomFailure($this->status_text);
            return false;
        }

        $this->repository->update(['status' => RepoStatus::WORKING->value]);

        $connection = new SSHSiteConnect($this->site);

        if (!$connection->active) {
            $this->status_text = 'SSH connection failed.';
            $this->status = RepoStatus::ERROR->value;
            $this->saveToDb();
            return false;
        }

        $output = $this->executeGitCommand($connection);
        $success = $connection->getExitStatusBool();

        $this->create_commit_log($connection);

        $connection->close();

        if ($success) {
            $this->status      = RepoStatus::SUCCESS->value;
            $this->status_text = 'Reverted to commit ' . substr($this->commit_hash, 0, 8) . '.';
            $this->saveToDb();

            Notification::make()
                ->title('Revert successful.')
                ->success()
                ->body($this->status_text)
                ->sendToDatabase(auth()->user());
        } else {
            $this->status      = RepoStatus::ERROR->value;
            $this->status_text = 'Revert failed for commit ' . substr($this->commit_hash, 0, 8) . '.';
            $this->saveToDb();
        }

        return true;
    }

    public function getAbsolutePathToDeploy()
    {
        return rtrim($this->site->dir_path, '/') . '/' . $this->pivot->path;
    }

    public function executeGitCommand($connection)
    {
        $gitCommand =
            'cd ' . $this->getAbsolutePathToDeploy() . ' && ' .
            'git fetch origin && ' .
            'git reset --hard ' . escapeshellarg($this->commit_hash);

        return $connection->exec($gitCommand);
    }

    public function saveToDb()
    {
        if ($this->pivot) {
            $this->repository->sites()->updateExistingPivot($this->site->id, [
                'status'      => $this->status,
                'status_text' => $this->status_text,
                'last_pull'   => Carbon::now(),
            ]);
        }
    }

    public function create_commit_log(SSHSiteConnect $connection)
    {
        $last_commits = 'cd ' . $this->getAbsolutePathToDeploy() . ' && ' . $this->get_latest_git_commit();

        $commit_response = json_decode((string) $connection->exec($last_commits));

        if ($commit_response && is_array($commit_response)) {
            Deployment::firstOrCreate(
                ['commit' => $commit_response[0]->commit, 'pivot_id' => $this->pivot->id],
                [
                    'committer'     => $commit_response[0]->author->name,
                    'branch'        => $this->pivot->branch,
                    'message'       => $commit_response[0]->subject,
                    'site_id'       => $this->pivot->site_id,
                    'repository_id' => $this->repository->id,
                    'success'       => 1,
                    'type'          => 'revert',
                ]
            );
        }
    }
}
