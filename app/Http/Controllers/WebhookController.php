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

            // Generate developer docs if developer/ content was checked out.
            // Guard: skipped silently when developer/ is absent so that merchant-only
            // pushes are completely unaffected.
            // Fail-safe: a generation failure is logged but does NOT abort the merchant
            // sync or change the HTTP response to an error.
            $devDocsOutcome = 'skipped — developer/ content not present';

            $developerSourcePath = resource_path('docs/main/developer');

            if (is_dir($developerSourcePath)) {
                try {
                    $generateScript = base_path('bin/devdocs/generate.sh');

                    // Prefer the explicitly-configured server venv (DEVDOCS_VENV_PATH)
                    // for reproducible deps; fall back to whatever python3 is on PATH
                    // (works locally without any venv). Using config rather than
                    // getenv('HOME') keeps this deterministic under PHP-FPM, whose
                    // HOME may differ from the deploy user's.
                    $venvPath = config('services.devdocs.venv_path');
                    $venvBinDir = $venvPath ? rtrim($venvPath, '/') . '/bin' : '';
                    $processEnv = null;

                    if ($venvBinDir && is_dir($venvBinDir)) {
                        $processEnv = getenv();
                        $processEnv['PATH'] = $venvBinDir . ':' . ($processEnv['PATH'] ?? '/usr/local/bin:/usr/bin:/bin');
                    }

                    // generate.sh builds into a temp dir then atomically renames into
                    // public_path('developer'), so the live directory is never half-written.
                    $generateProcess = new Process(
                        ['bash', $generateScript, $developerSourcePath, public_path('developer')],
                        base_path(),
                        $processEnv,
                    );
                    $generateProcess->setTimeout(120);
                    $generateProcess->run();

                    if ($generateProcess->isSuccessful()) {
                        $devDocsOutcome = 'generated successfully';
                        Log::info('Developer docs generated successfully');
                    } else {
                        $devDocsOutcome = 'generation failed (see error log)';
                        Log::error('Developer docs generation failed — merchant sync unaffected', [
                            'stderr' => $generateProcess->getErrorOutput(),
                        ]);
                    }
                } catch (\Exception $devException) {
                    $devDocsOutcome = 'exception: ' . $devException->getMessage();
                    Log::error('Developer docs generation threw exception — merchant sync unaffected', [
                        'exception' => $devException->getMessage(),
                    ]);
                }
            }

            // Clear caches so the freshly synced docs are served. We deliberately do
            // NOT rebuild the compiled caches here (config:cache, route:cache,
            // view:cache): none are affected by a docs *content* sync, and running a
            // *:cache command inside a booted HTTP request throws at runtime
            // (route:cache -> TypeError on CompiledRouteCollection; view:cache ->
            // hash_file on a just-cleared compiled view). Laravel rebuilds each lazily
            // on the next request, and the deploy's deploy:optimize re-caches them fully.
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');

            Log::info('Docs sync completed successfully', ['commit' => $currentCommit]);

            return response()->json([
                'success' => true,
                'message' => 'Documentation synced successfully',
                'commit' => $currentCommit,
                'developer_docs' => $devDocsOutcome,
            ]);

        } catch (\Throwable $e) {
            Log::error('Docs sync failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Sync failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
