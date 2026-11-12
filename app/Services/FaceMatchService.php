<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FaceMatchService
{
    public function verify(string $idPath, string $selfiePath): array
    {
        $script = base_path('scripts/verify_face.py');

        if (! file_exists($script)) {
            Log::error('FaceMatch: script not found', ['path' => $script]);
            return ['success' => false, 'error' => 'verify_face.py not found'];
        }

        // Resolve absolute paths (Supports both Local Storage and Cloudinary URLs)
        $absoluteIdPath = $this->resolveImagePath($idPath, 'id');
        $absoluteSelfiePath = $this->resolveImagePath($selfiePath, 'selfie');

        if (!$absoluteIdPath) {
            Log::error('FaceMatch: ID image not found or failed to download', ['path' => $idPath]);
            return ['success' => false, 'error' => 'ID image could not be retrieved.'];
        }

        if (!$absoluteSelfiePath) {
            Log::error('FaceMatch: Selfie image not found or failed to download', ['path' => $selfiePath]);
            return ['success' => false, 'error' => 'Selfie image could not be retrieved.'];
        }

        // Python path (default: 'python' from PATH, override via .env PYTHON_PATH)
        $python = config('services.python.path', 'python');

        // Pass BOTH ID and Selfie arguments to the Python script
        $cmd = sprintf(
            '"%s" %s %s %s 2>&1',
            $python,
            escapeshellarg($script),
            escapeshellarg($absoluteIdPath),
            escapeshellarg($absoluteSelfiePath)
        );

        Log::info('FaceMatch: running', ['cmd' => $cmd]);

        try {
            $process = \Symfony\Component\Process\Process::fromShellCommandline($cmd);
            $process->setTimeout(60); // Abort if execution exceeds 1 minute
            $process->run();

            $output = $process->getOutput() ?: $process->getErrorOutput();
        } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
            \Log::error('FaceMatch: Python script timed out');
            $this->cleanupTempFiles($absoluteIdPath, $absoluteSelfiePath);
            return ['success' => false, 'error' => 'Face verification timed out. Please try again.'];
        } catch (\Exception $e) {
            \Log::error('FaceMatch: Execution error', ['error' => $e->getMessage()]);
            $this->cleanupTempFiles($absoluteIdPath, $absoluteSelfiePath);
            return ['success' => false, 'error' => 'System error during verification.'];
        }

        // Clean up temporary downloaded files (if any)
        $this->cleanupTempFiles($absoluteIdPath, $absoluteSelfiePath);

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

    /**
     * Resolve image path. If it's a remote URL (Cloudinary), download it temporarily.
     */
    private function resolveImagePath(string $path, string $type): ?string
    {
        // Check if it's a URL (Cloudinary)
        if (str_starts_with($path, 'http')) {
            $tempPath = storage_path('app/temp/' . uniqid() . '_' . $type . '.jpg');

            // Ensure temp directory exists
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            // Download the image
            try {
                $content = @file_get_contents($path);
                if ($content !== false) {
                    file_put_contents($tempPath, $content);
                    return $tempPath;
                }
            } catch (\Exception $e) {
                Log::error('FaceMatch: Failed to download image from URL', ['url' => $path, 'error' => $e->getMessage()]);
            }
            return null;
        }

        // Local storage
        $localPath = storage_path("app/public/{$path}");
        return file_exists($localPath) ? $localPath : null;
    }

    /**
     * Delete temporary downloaded files to save storage space.
     */
    private function cleanupTempFiles(string $idPath, string $selfiePath): void
    {
        if (str_contains($idPath, 'app/temp/') && file_exists($idPath)) {
            @unlink($idPath);
        }
        if (str_contains($selfiePath, 'app/temp/') && file_exists($selfiePath)) {
            @unlink($selfiePath);
        }
    }
}
