<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    public function upload(UploadedFile $file, string $folder = 'general', array $options = []): array
    {
        try {
            $baseFolder = config('cloudinary.folder', 'petnanny');
            $fullFolder = "{$baseFolder}/{$folder}";

            $defaults = [
                'folder'        => $fullFolder,
                'resource_type' => 'auto',
                'quality'       => 'auto',
                'fetch_format'  => 'auto',
            ];

            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                array_merge($defaults, $options)
            );

            return [
                'success'   => true,
                'url'       => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format'    => $result['format'] ?? null,
                'bytes'     => $result['bytes'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'folder' => $folder,
                'error'  => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete(?string $publicId): bool
    {
        if (empty($publicId)) return false;

        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed', [
                'public_id' => $publicId,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function uploadMultiple(array $files, string $folder = 'general'): array
    {
        $results = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $results[] = $this->upload($file, $folder);
            }
        }
        return $results;
    }
}
