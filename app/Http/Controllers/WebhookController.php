<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Jobs\Git\SshAndGitPull;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request, $unique_token)
    {
        // Find the repository by its unique token
        $repository = Repository::where('webhook', $unique_token)->first();

        if (!$repository) {
            Log::warning("Webhook for unknown repository token: {$unique_token}");
            return response()->json(['message' => 'Repository not found'], 404);
        }

        // dd($request->all());

        // Retrieve the secret token from the repository
        $secret = $repository->secret; // Assuming 'secret' is the field where the token is stored

        // Bitbucket Cloud will use your secret token to create a HMAC signature, which will be sent to you as the value of the X-Hub-Signature header with each payload. The signature is calculated based on the payload contents, your secret token, and a hashing algorithm (sha256).
        // The X-Hub-Signature header sent by Bitbucket
        $bitbucketSignature = $request->header('X-Hub-Signature');

        // Create the HMAC SHA256 hash
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        // Use hash_equals to prevent timing attacks
        if (!hash_equals($expectedSignature, $bitbucketSignature)) {
            // Log this discrepancy, halt execution, or take other security measures
            Log::warning("Webhook signature does not match for repository ID {$repository->id}. Expected: {$expectedSignature}, Given: {$bitbucketSignature}");
            abort(403, 'Invalid signature');
        }

        // @todo see which branch this is.
        // Need a query to the pivot table where all site is assosiated with
        if ( $repository->sites() ) {
            foreach ( $repository->sites() as $single_site ) {
                // If the signature is valid, process the webhook payload
                SshAndGitPull::dispatch( $repository, $single_site );
            }
        }

        return response()->json(['message' => 'Webhook received and processed'], 200);
    }
}

