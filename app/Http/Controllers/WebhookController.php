<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Jobs\Git\SshAndGitPull;

class WebhookController extends Controller 
{

	public function allowedIPs() 
    {
		return array(
        // Bitbucket
		'13.52.5.96/28',
        '13.236.8.224/28',
        '18.136.214.96/28',
        '18.184.99.224/28',
        '18.234.32.224/28',
        '18.246.31.224/28',
        '52.215.192.224/28',
        '104.192.137.240/28',
        '104.192.138.240/28',
        '104.192.140.240/28',
        '104.192.142.240/28',
        '104.192.143.240/28',
        '185.166.143.240/28',
        '185.166.142.240/28',
        // IPv6 Ranges
        '2401:1d80:3000:100::/61',
        '2401:1d80:3000:200::/61',
        '2401:1d80:3000:300::/61',
        '2401:1d80:3000:400::/61',
        '2401:1d80:3000:500::/61',
        '2401:1d80:3000:600::/61',
        '2401:1d80:3000:700::/61',
        '2406:da18:809:e04::/63',
        '2406:da18:809:e06::/64',
        '2406:da1c:1e0:a204::/63',
        '2406:da1c:1e0:a206::/64',
        '2600:1f14:824:304::/63',
        '2600:1f14:824:306::/64',
        '2600:1f18:2146:e304::/63',
        '2600:1f18:2146:e306::/64',
        '2600:1f1c:cc5:2304::/63',
        '2a05:d014:f99:dd04::/63',
        '2a05:d014:f99:dd06::/64',
        '2a05:d018:34d:5804::/63',
        '2a05:d018:34d:5806::/64',
        // GITHUB
        "192.30.252.0/22",
        "185.199.108.0/22",
        "140.82.112.0/20",
        "143.55.64.0/20",
        "2a0a:a440::/29",
        "2606:50c0::/32"
		);
	}

    /**
     * Determine if an IP address falls within a given CIDR range.
     * Supports both IPv4 and IPv6.
     *
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    public function ipInRange($ip, $cidr)
    {
        list($subnet, $bits) = explode('/', $cidr);
        $bits = (int) $bits;
        
        $ipBin = inet_pton($ip);
        $subnetBin = inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false) {
            return false;
        }

        // Different address families can't match
        if (strlen($ipBin) !== strlen($subnetBin)) {
            return false;
        }

        // Build a binary mask of the correct byte length
        $totalBits = strlen($ipBin) * 8; // 32 for IPv4, 128 for IPv6
        $mask = str_repeat("\xff", (int)floor($bits / 8));
        if ($bits % 8) {
            $mask .= chr(0xff << (8 - ($bits % 8)) & 0xff);
        }
        $mask = str_pad($mask, strlen($ipBin), "\x00");

        return ($ipBin & $mask) === ($subnetBin & $mask);
    }

	public function handleWebhook( Request $request, $unique_token ) 
    {
		// Get the IP address of the incoming request
		$request_ip = $request->ip();
        // Check if the request IP falls within any allowed CIDR ranges
        $is_allowed = false;
        foreach ($this->allowedIPs() as $cidr) {
            if ($this->ipInRange($request_ip, $cidr)) {
                $is_allowed = true;
                break;
            }
        }

        if (!$is_allowed) {
            Log::warning("Unauthorized IP address: {$request_ip}");
            return response()->json(['message' => 'Unauthorized IP address'], 403);
        }

		// Find the repository by its unique token
		$repository = Repository::where( 'webhook', $unique_token )->first();

		if ( ! $repository ) {
			Log::warning( "Webhook for unknown repository token: " . substr($unique_token, 0, 12) . '...' );
			return response()->json(['message' => 'Webhook processed'], 200);
		}

        $provider = $repository->provider;

        try {
            $payload = $request->json()->all();
        } catch (\Exception $e) {
            Log::warning("Invalid JSON payload in webhook for repository ID {$repository->id}");
            return response()->json(['message' => 'Webhook processed'], 200);
        }

        $branch_name = null;

        if ($provider === 'bitbucket') {
            // Bitbucket sends push events with changes array
            $changes = $payload['push']['changes'] ?? [];
            if (!empty($changes)) {
                $branch_name = $changes[0]['new']['name'] ?? null;
            }
        } elseif ($provider === 'github') {
            // GitHub sends ref as "refs/heads/branch-name"
            $ref = $payload['ref'] ?? null;
            if ($ref && str_starts_with($ref, 'refs/heads/')) {
                $branch_name = substr($ref, strlen('refs/heads/'));
            }
        }

        if (!$branch_name) {
            Log::info("Could not determine branch from webhook for repository ID {$repository->id}", [
                'provider' => $provider,
                'payload_keys' => array_keys($payload),
            ]);
            return response()->json(['message' => 'Could not determine branch from webhook.'], 400);
        }

        Log::info("Webhook received for repository {$repository->name} (ID: {$repository->id}), branch: {$branch_name}, provider: {$provider}");

		// Handle the webhook payload (Bitbucket or GitHub)
        $deployedCount = 0;
		if ( $repository->sites ) 
        {
			foreach ( $repository->sites as $single_site ) 
            {
                $site_branch = $single_site->pivot->branch;

                if( $single_site->pivot->auto_deploy && $site_branch === $branch_name)
                {
                    // Skip if already deploying
                    if ($single_site->pivot->status === \App\Enums\RepoStatus::WORKING->value) {
                        Log::info("Skipping deploy for site {$single_site->id} — already in progress");
                        continue;
                    }

                    // Update status to working before dispatch
                    $repository->sites()->updateExistingPivot($single_site->id, [
                        'status' => \App\Enums\RepoStatus::WORKING->value,
                    ]);

				    SshAndGitPull::dispatch( $repository, $single_site, null, 'webhook' );
                    $deployedCount++;

                    Log::info("Auto-deploy triggered for site {$single_site->name} (ID: {$single_site->id}) on branch {$branch_name}");
                }
			}
		}

		return response()->json(['message' => 'Webhook processed'], 200);
	}
}
