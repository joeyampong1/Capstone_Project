<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrService
{
    public function extractIdDetails(string $imagePath): array
    {
        $absolutePath = storage_path("app/public/{$imagePath}");

        if (! file_exists($absolutePath)) {
            Log::error('OCR: file not found', ['path' => $absolutePath]);
            return ['success' => false, 'error' => 'Image file not found on storage'];
        }

        $apiKey = config('services.ocr_space.key');

        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'OCR API key not configured'];
        }

        $attempts = [
            ['rotation' => 0,   'engine' => '2', 'detect' => 'false'],
            ['rotation' => 0,   'engine' => '1', 'detect' => 'true'],
            ['rotation' => 90,  'engine' => '2', 'detect' => 'false'],
            ['rotation' => 270, 'engine' => '2', 'detect' => 'false'],
        ];

        $bestResult = null;
        $bestScore = -1;

        foreach ($attempts as $attempt) {
            $tempPath = $absolutePath;

            if ($attempt['rotation'] !== 0) {
                $tempPath = $this->rotateImage($absolutePath, $attempt['rotation']);
                if (! $tempPath) continue;
            }

            $result = $this->callOcrSpace($tempPath, $apiKey, $attempt['engine'], $attempt['detect']);

            if ($attempt['rotation'] !== 0 && file_exists($tempPath)) {
                @unlink($tempPath);
            }

            $score = 0;
            if (! empty($result['name']))      $score += 2;
            if (! empty($result['id_number'])) $score += 3;
            if (! empty($result['dob']))       $score += 1;
            if (! empty($result['raw_text']))  $score += strlen($result['raw_text']) > 100 ? 2 : 0;

            Log::info('OCR attempt', [
                'rotation' => $attempt['rotation'],
                'engine'   => $attempt['engine'],
                'score'    => $score,
                'success'  => $result['success'] ?? false,
            ]);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestResult = $result;
            }

            if ($score >= 8) break;
        }

        return $bestResult ?? ['success' => false, 'error' => 'All attempts failed'];
    }

    private function rotateImage(string $sourcePath, int $degrees): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $image = @imagecreatefromstring(file_get_contents($sourcePath));
        if ($image === false) return null;

        $rotated = imagerotate($image, -$degrees, 0);
        imagedestroy($image);
        if ($rotated === false) return null;

        $tempPath = storage_path('app/temp/rotated_' . uniqid() . '.jpg');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $saved = imagejpeg($rotated, $tempPath, 92);
        imagedestroy($rotated);
        return $saved ? $tempPath : null;
    }

    private function resizeForOcr(string $sourcePath): string
    {
        $fileSize = filesize($sourcePath);
        $maxSize = 1024 * 1024;
        $maxDim = 1600;

        $info = @getimagesize($sourcePath);
        if ($info === false) return $sourcePath;

        [$width, $height] = $info;

        if ($fileSize <= $maxSize && $width <= $maxDim && $height <= $maxDim) {
            return $sourcePath;
        }

        $image = @imagecreatefromstring(file_get_contents($sourcePath));
        if ($image === false) return $sourcePath;

        $scale = min($maxDim / $width, $maxDim / $height, 1);
        $newWidth  = (int) ($width  * $scale);
        $newHeight = (int) ($height * $scale);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        $tempPath = storage_path('app/temp/resized_' . uniqid() . '.jpg');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        imagejpeg($resized, $tempPath, 85);
        imagedestroy($resized);

        Log::info('OCR: resized', [
            'orig' => "{$width}x{$height} ({$fileSize}b)",
            'new'  => "{$newWidth}x{$newHeight}",
        ]);

        return $tempPath;
    }

    private function callOcrSpace(string $path, string $apiKey, string $engine, string $detect): array
    {
        $processedPath = $this->resizeForOcr($path);

        try {
            $response = Http::asMultipart()
                ->attach('file', file_get_contents($processedPath), 'id.jpg')
                ->post('https://api.ocr.space/parse/image', [
                    'apikey'            => $apiKey,
                    'language'          => 'eng',
                    'isOverlayRequired' => 'false',
                    'OCREngine'         => $engine,
                    'scale'             => 'true',
                    'isTable'           => 'false',
                    'detectOrientation' => $detect,
                ]);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Request failed'];
        } finally {
            if ($processedPath !== $path && file_exists($processedPath)) {
                @unlink($processedPath);
            }
        }

        if (! $response->ok()) {
            return ['success' => false, 'error' => 'API status ' . $response->status()];
        }

        $data = $response->json();

        if (! empty($data['IsErroredOnProcessing'])) {
            $err = $data['ErrorMessage'][0] ?? 'OCR error';
            return ['success' => false, 'error' => is_array($err) ? implode(' ', $err) : $err];
        }

        $text = $data['ParsedResults'][0]['ParsedText'] ?? '';

        if (trim($text) === '') {
            return ['success' => false, 'error' => 'No text extracted'];
        }

        return [
            'success'   => true,
            'raw_text'  => $text,
            'name'      => $this->extractName($text),
            'id_number' => $this->extractIdNumber($text),
            'dob'       => $this->extractDob($text),
        ];
    }

    private function extractName(string $text): ?string
    {
        if (preg_match('/\b([A-Z]{2,},\s+[A-Z][A-Z\s]+?)(?=\n|Nationality|Date)/i', $text, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/(?:name|pangalan)[:\s]+([A-Z][A-Z\s,\.]+)/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function extractIdNumber(string $text): ?string
    {
        if (preg_match('/\b([A-Z]\d{2}-\d{2}-\d{6})\b/', $text, $m)) return $m[1];
        if (preg_match('/\b([A-Z]{1,4}[-]\d{2,4}[-]\d{4,12})\b/', $text, $m)) return $m[1];
        if (preg_match('/\b([A-Z]{2,4}[-\s]?\d{6,12})\b/', $text, $m)) return $m[1];
        return null;
    }

    /**
     * Extract Date of Birth — LTO format.
     *
     * Prioritize:
     * 1. "Date of Birth" label (most accurate)
     * 2. Year range 1900-2010 (birth year filter)
     * 3. Generic fallback
     */
    private function extractDob(string $text): ?string
    {
        // ────────────────────────────────────────────────────────────
        // PATTERN 1: Pangita "Date of Birth" label
        // ────────────────────────────────────────────────────────────
        // LTO format: "Date of Birth\n...\n1998/12/30"
        if (preg_match('/Date\s+of\s+Birth[^\d]{0,50}(\d{4}\/\d{2}\/\d{2})/i', $text, $m)) {
            return $m[1];
        }

        // ────────────────────────────────────────────────────────────
        // PATTERN 2: Filter birth years (1900-2010)
        // ────────────────────────────────────────────────────────────
        if (preg_match_all('/\b(\d{4}\/\d{2}\/\d{2})\b/', $text, $m)) {
            foreach ($m[1] as $date) {
                $year = (int) substr($date, 0, 4);
                // Birth year range
                if ($year >= 1900 && $year <= 2010) {
                    return $date;
                }
            }
        }

        // ────────────────────────────────────────────────────────────
        // PATTERN 3: Generic fallback (MM/DD/YYYY)
        // ────────────────────────────────────────────────────────────
        if (preg_match('/\b(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4})\b/', $text, $m)) {
            return $m[1];
        }

        return null;
    }
}
