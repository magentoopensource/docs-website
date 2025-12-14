<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class WebhookController extends Controller
{
    /**
     * Handle GitHub webhook to sync documentation.
     */
    public function syncDocs(Request $request)
    {
        // Verify webhook secret
        $secret = config('services.github.webhook_secret');

        if (!$secret) {
            Log::error('Webhook secret not configured');
            return response()->json(['error' => 'Webhook not configured'], 500);
        }

        // Verify GitHub signature
        $signature = $request->header('X-Hub-Signature-256');
        if (!$signature) {
            Log::warning('Webhook request missing signature');
            return response()->json(['error' => 'Missing signature'], 401);
        }

        $payload = $request->getContent();
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Webhook signature mismatch');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Only process push events to main branch
        $event = $request->header('X-GitHub-Event');
        if ($event !== 'push') {
            return response()->json(['message' => 'Event ignored', 'event' => $event]);
        }

        $payload = json_decode($request->getContent(), true);
        $ref = $payload['ref'] ?? '';

        if ($ref !== 'refs/heads/main') {
            return response()->json(['message' => 'Branch ignored', 'ref' => $ref]);
        }

        Log::info('Starting docs sync from webhook');

        try {
            // Pull latest docs
            $docsPath = resource_path('docs/main');

            // Force fetch to handle rewritten history
            $fetchProcess = new Process(['git', 'fetch', '--force', 'origin', 'main'], $docsPath);
            $fetchProcess->run();

            if (!$fetchProcess->isSuccessful()) {
                throw new \RuntimeException('Git fetch failed: ' . $fetchProcess->getErrorOutput());
            }

            // Clean any local changes and reset to remote
            $cleanProcess = new Process(['git', 'clean', '-fd'], $docsPath);
            $cleanProcess->run();

            $resetProcess = new Process(['git', 'reset', '--hard', 'origin/main'], $docsPath);
            $resetProcess->run();

            if (!$resetProcess->isSuccessful()) {
                throw new \RuntimeException('Git reset failed: ' . $resetProcess->getErrorOutput());
            }

            // Get current commit for logging
            $commitProcess = new Process(['git', 'rev-parse', '--short', 'HEAD'], $docsPath);
            $commitProcess->run();
            $currentCommit = trim($commitProcess->getOutput());

            // Clear Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');

            // Rebuild caches
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            Log::info('Docs sync completed successfully', ['commit' => $currentCommit]);

            return response()->json([
                'success' => true,
                'message' => 'Documentation synced successfully',
                'commit' => $currentCommit,
            ]);

        } catch (\Exception $e) {
            Log::error('Docs sync failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Sync failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
