<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FaceMatchService
{
    /**
     * Verify that the live face in the selfie matches the ID face.
     *
     * @param  string  $selfiePath  Relative path on 'public' disk (e.g. "selfies/xxx.jpg")
     * @return array{success: bool, match?: bool, distance?: float, confidence?: float, error?: string}
     */
    public function verify(string $selfiePath): array
    {
        $script = base_path('scripts/verify_face.py');

        if (! file_exists($script)) {
            Log::error('FaceMatch: script not found', ['path' => $script]);
            return ['success' => false, 'error' => 'verify_face.py not found'];
        }

        // Resolve absolute path on 'public' disk
        $absolutePath = storage_path("app/public/{$selfiePath}");

        if (! file_exists($absolutePath)) {
            Log::error('FaceMatch: selfie not found', ['path' => $absolutePath]);
            return ['success' => false, 'error' => 'Selfie file not found'];
        }

        // Python path (default: 'python' from PATH, override via .env PYTHON_PATH)
        $python = env('PYTHON_PATH', 'python');

        $cmd = sprintf(
            '%s %s %s 2>&1',
            $python,
            escapeshellarg($script),
            escapeshellarg($absolutePath)
        );

        Log::info('FaceMatch: running', ['cmd' => $cmd]);

        $output = shell_exec($cmd);

        if (empty($output)) {
            return ['success' => false, 'error' => 'No output from Python script'];
        }

        // Python may print warnings before JSON — find last valid JSON line
        $lines = array_values(array_filter(array_map('trim', explode("\n", $output))));
        $result = null;

        for ($i = count($lines) - 1; $i >= 0; $i--) {
            $decoded = json_decode($lines[$i], true);
            if (is_array($decoded) && isset($decoded['success'])) {
                $result = $decoded;
                break;
            }
        }

        if ($result === null) {
            Log::error('FaceMatch: invalid JSON', ['output' => $output]);
            return ['success' => false, 'error' => 'Invalid response from Python'];
        }

        return $result;
    }
}