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
     *
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    public function ipInRange($ip, $cidr)
    {
        list($subnet, $bits) = explode('/', $cidr);
        $ip = inet_pton($ip);
        $subnet = inet_pton($subnet);

        if ($ip === false || $subnet === false) {
            return false; // Invalid IP format
        }

        $mask = ~((1 << (128 - $bits)) - 1);
        $mask = pack('J', $mask);

        return ($ip & $mask) === ($subnet & $mask);
    }

	public function handleWebhook( Request $request, $unique_token ) 
    {
		// Get the IP address of the incoming request
		$requestIp = $request->ip();
        // Check if the request IP falls within any allowed CIDR ranges
        $isAllowed = false;
        foreach ($allowedIps as $cidr) {
            if ($this->ipInRange($requestIp, $cidr)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            Log::warning("Unauthorized IP address: {$requestIp}");
            return response()->json(['message' => 'Unauthorized IP address'], 403);
        }

		// Find the repository by its unique token
		$repository = Repository::where( 'webhook', $unique_token )->first();

		if ( ! $repository ) {
			Log::warning( "Webhook for unknown repository token: {$unique_token}" );
			return response()->json( array( 'message' => 'Repository not found' ), 404 );
		}

		// Retrieve the secret token from the repository
		$secret = $repository->secret;

		// Get the provider
		$provider = $repository->provider;

		// Check the provider and validate the webhook signature if a secret is set
		if ( $secret ) 
        {
			if ( $provider === 'bitbucket' ) 
            {
				// Bitbucket-specific signature verification
				$bitbucketSignature = $request->header( 'X-Hub-Signature' );
				$expectedSignature  = 'sha256=' . hash_hmac( 'sha256', $request->getContent(), $secret );

				if ( ! hash_equals( $expectedSignature, $bitbucketSignature ) ) {
					Log::warning( "Bitbucket webhook signature mismatch for repository ID {$repository->id}. Expected: {$expectedSignature}, Given: {$bitbucketSignature}" );
					abort( 403, 'Invalid Bitbucket signature' );
				}
			} 
            elseif ( $provider === 'github' ) 
            {
				// GitHub-specific signature verification
				$githubSignature   = $request->header( 'X-Hub-Signature-256' );
				$expectedSignature = 'sha256=' . hash_hmac( 'sha256', $request->getContent(), $secret );

				if ( ! hash_equals( $expectedSignature, $githubSignature ) ) 
                {
					Log::warning( "GitHub webhook signature mismatch for repository ID {$repository->id}. Expected: {$expectedSignature}, Given: {$githubSignature}" );
					abort( 403, 'Invalid GitHub signature' );
				}
			}
		} else {
			Log::info( "No secret set for repository ID {$repository->id}, skipping signature verification." );
		}

		// Handle the webhook payload (Bitbucket or GitHub)
		if ( $repository->sites() ) 
        {
			foreach ( $repository->sites() as $single_site ) 
            {
				// If the signature is valid, process the webhook payload
				SshAndGitPull::dispatch( $repository, $single_site );
			}
		}

		return response()->json( array( 'message' => 'Webhook received and processed' ), 200 );
	}
}
