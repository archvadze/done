<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use getID3;
use Exception;

class FileUploadService
{
    private ?ImageManager $imageManager = null;

    private function getImageManager(): ImageManager
    {
        if ($this->imageManager === null) {
            $this->imageManager = new ImageManager(new Driver());
        }
        return $this->imageManager;
    }

    /**
     * Handle artwork file upload with comprehensive processing
     */
    public function uploadArtwork(UploadedFile $file, User $user, array $metadata = []): Artwork
    {
        // Validate file
        $this->validateFile($file);

        // Generate unique file path
        $filePath = $this->generateFilePath($file->getClientOriginalName());

        // Determine media type
        $mediaType = $this->determineMediaType($file->getMimeType());

        // Store the original file
        $storedPath = $file->storeAs('public/' . dirname($filePath), basename($filePath));
        $fileUrl = Storage::url($storedPath);

        // Generate file hash for integrity
        $fileHash = hash_file('sha256', $file->getPathname());

        // Extract file metadata
        $fileMetadata = $this->extractFileMetadata($file, $mediaType);

        // Generate thumbnail if needed
        $thumbnailPath = null;
        if ($this->shouldGenerateThumbnail($mediaType)) {
            $thumbnailPath = $this->generateThumbnail($file, $filePath);
        }

        // Create artwork record
        $artwork = Artwork::create([
            'user_id' => $user->id,
            'title' => $metadata['title'] ?? ['en' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)],
            'description' => $metadata['description'] ?? null,
            'media_type' => $mediaType,
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'thumbnail_path' => $thumbnailPath,
            'original_filename' => $file->getClientOriginalName(),
            'file_hash' => $fileHash,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'file_metadata' => $fileMetadata,
            'license_type' => $metadata['license_type'] ?? 'all_rights_reserved',
            'copyright_notice' => $metadata['copyright_notice'] ?? null,
            'watermark_enabled' => $metadata['watermark_enabled'] ?? true,
            'tags' => $metadata['tags'] ?? [],
            'category' => $metadata['category'] ?? null,
            'subcategory' => $metadata['subcategory'] ?? null,
            'is_ai_generated' => $metadata['is_ai_generated'] ?? false,
            'ai_tools_used' => $metadata['ai_tools_used'] ?? null,
            'visibility' => $metadata['visibility'] ?? 'public',
            'comments_enabled' => $metadata['comments_enabled'] ?? true,
            'downloads_enabled' => $metadata['downloads_enabled'] ?? false,
            'status' => 'draft', // Start as draft, can be published later
        ]);

        return $artwork;
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file): void
    {
        $maxSize = 100 * 1024 * 1024; // 100MB
        $allowedMimes = array_merge(
            ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'],
            ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
            ['video/mp4', 'video/webm', 'video/quicktime', 'video/avi'],
            ['application/pdf'],
            ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed']
        );

        if ($file->getSize() > $maxSize) {
            throw new Exception('File size too large. Maximum allowed size is 100MB.');
        }

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new Exception('File type not supported: ' . $file->getMimeType());
        }

        // Additional security check for executables
        $dangerousExtensions = ['php', 'exe', 'bat', 'sh', 'js', 'html', 'htm'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, $dangerousExtensions)) {
            throw new Exception('File type not allowed for security reasons.');
        }
    }

    /**
     * Generate unique file path
     */
    private function generateFilePath(string $originalFilename): string
    {
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $hash = Str::random(40);
        $date = date('Y/m/d');

        return "artworks/{$date}/{$hash}.{$extension}";
    }

    /**
     * Determine media type from MIME type
     */
    private function determineMediaType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        return 'other';
    }

    /**
     * Extract comprehensive file metadata
     */
    private function extractFileMetadata(UploadedFile $file, string $mediaType): array
    {
        $metadata = [];

        try {
            switch ($mediaType) {
                case 'image':
                    $metadata = $this->extractImageMetadata($file);
                    break;

                case 'audio':
                case 'video':
                    $metadata = $this->extractMediaMetadata($file);
                    break;

                case 'pdf':
                    $metadata = $this->extractPdfMetadata($file);
                    break;
            }
        } catch (Exception $e) {
            // Log error but don't fail upload
            logger('Metadata extraction failed: ' . $e->getMessage());
        }

        return $metadata;
    }

    /**
     * Extract image metadata
     */
    private function extractImageMetadata(UploadedFile $file): array
    {
        $image = $this->getImageManager()->read($file->getPathname());

        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'orientation' => $image->width() > $image->height() ? 'landscape' : 'portrait',
            'aspect_ratio' => round($image->width() / $image->height(), 2),
            'color_profile' => $this->extractColorProfile($file),
        ];
    }

    /**
     * Extract audio/video metadata using getID3
     */
    private function extractMediaMetadata(UploadedFile $file): array
    {
        if (!class_exists('getID3')) {
            return [];
        }

        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($file->getPathname());

        $metadata = [];

        if (isset($fileInfo['playtime_seconds'])) {
            $metadata['duration'] = (float) $fileInfo['playtime_seconds'];
            $metadata['duration_formatted'] = gmdate('H:i:s', (int) $fileInfo['playtime_seconds']);
        }

        if (isset($fileInfo['video'])) {
            $metadata['width'] = $fileInfo['video']['resolution_x'] ?? null;
            $metadata['height'] = $fileInfo['video']['resolution_y'] ?? null;
            $metadata['framerate'] = $fileInfo['video']['frame_rate'] ?? null;
            $metadata['codec'] = $fileInfo['video']['codec'] ?? null;
        }

        if (isset($fileInfo['audio'])) {
            $metadata['bitrate'] = $fileInfo['audio']['bitrate'] ?? null;
            $metadata['sample_rate'] = $fileInfo['audio']['sample_rate'] ?? null;
            $metadata['channels'] = $fileInfo['audio']['channels'] ?? null;
            $metadata['codec'] = $fileInfo['audio']['codec'] ?? null;
        }

        if (isset($fileInfo['comments'])) {
            $metadata['title'] = $fileInfo['comments']['title'][0] ?? null;
            $metadata['artist'] = $fileInfo['comments']['artist'][0] ?? null;
            $metadata['album'] = $fileInfo['comments']['album'][0] ?? null;
        }

        return $metadata;
    }

    /**
     * Extract PDF metadata
     */
    private function extractPdfMetadata(UploadedFile $file): array
    {
        // Basic PDF info - can be extended with PDF parsing library
        return [
            'type' => 'document',
            'format' => 'pdf',
        ];
    }

    /**
     * Extract color profile information
     */
    private function extractColorProfile(UploadedFile $file): ?string
    {
        // Simplified color profile detection
        try {
            $image = $this->getImageManager()->read($file->getPathname());
            return 'RGB'; // Default for web images
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Check if thumbnail should be generated
     */
    private function shouldGenerateThumbnail(string $mediaType): bool
    {
        return in_array($mediaType, ['image', 'video', 'pdf']);
    }

    /**
     * Generate thumbnail for various media types
     */
    private function generateThumbnail(UploadedFile $file, string $originalPath): ?string
    {
        try {
            $thumbnailPath = str_replace(
                '.' . pathinfo($originalPath, PATHINFO_EXTENSION),
                '_thumb.jpg',
                $originalPath
            );

            $fullThumbnailPath = storage_path('app/public/' . $thumbnailPath);

            // Create directory if it doesn't exist
            $directory = dirname($fullThumbnailPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate thumbnail based on media type
            $mediaType = $this->determineMediaType($file->getMimeType());

            switch ($mediaType) {
                case 'image':
                    $this->generateImageThumbnail($file, $fullThumbnailPath);
                    break;

                case 'video':
                    $this->generateVideoThumbnail($file, $fullThumbnailPath);
                    break;

                case 'pdf':
                    $this->generatePdfThumbnail($file, $fullThumbnailPath);
                    break;
            }

            return $thumbnailPath;
        } catch (Exception $e) {
            logger('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate image thumbnail
     */
    private function generateImageThumbnail(UploadedFile $file, string $outputPath): void
    {
        $image = $this->getImageManager()->read($file->getPathname());

        // Resize to max 300x300 while maintaining aspect ratio
        $image->scale(width: 300, height: 300);

        // Save as JPEG with good quality
        $image->toJpeg(quality: 85)->save($outputPath);
    }

    /**
     * Generate video thumbnail (requires FFmpeg)
     */
    private function generateVideoThumbnail(UploadedFile $file, string $outputPath): void
    {
        // This would require FFmpeg integration
        // For now, create a placeholder
        $this->createPlaceholderThumbnail($outputPath, 'video');
    }

    /**
     * Generate PDF thumbnail (requires Imagick or similar)
     */
    private function generatePdfThumbnail(UploadedFile $file, string $outputPath): void
    {
        // This would require Imagick or similar
        // For now, create a placeholder
        $this->createPlaceholderThumbnail($outputPath, 'pdf');
    }

    /**
     * Create placeholder thumbnail
     */
    private function createPlaceholderThumbnail(string $outputPath, string $type): void
    {
        // Create a simple colored rectangle as placeholder
        $image = $this->getImageManager()->create(300, 300);

        $color = match ($type) {
            'video' => '#2563eb', // Blue
            'pdf' => '#dc2626',   // Red
            'audio' => '#16a34a', // Green
            default => '#6b7280'  // Gray
        };

        $image->fill($color);
        $image->toJpeg(quality: 85)->save($outputPath);
    }

    /**
     * Delete artwork files
     */
    public function deleteArtworkFiles(Artwork $artwork): bool
    {
        try {
            // Delete main file
            if ($artwork->file_path && Storage::disk('public')->exists($artwork->file_path)) {
                Storage::disk('public')->delete($artwork->file_path);
            }

            // Delete thumbnail
            if ($artwork->thumbnail_path && Storage::disk('public')->exists($artwork->thumbnail_path)) {
                Storage::disk('public')->delete($artwork->thumbnail_path);
            }

            return true;
        } catch (Exception $e) {
            logger('File deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get supported file types for frontend validation
     */
    public static function getSupportedFileTypes(): array
    {
        return [
            'image' => [
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
                'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'],
                'max_size' => '50MB'
            ],
            'audio' => [
                'extensions' => ['mp3', 'wav', 'ogg', 'm4a'],
                'mimes' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
                'max_size' => '100MB'
            ],
            'video' => [
                'extensions' => ['mp4', 'webm', 'mov', 'avi'],
                'mimes' => ['video/mp4', 'video/webm', 'video/quicktime', 'video/avi'],
                'max_size' => '100MB'
            ],
            'document' => [
                'extensions' => ['pdf'],
                'mimes' => ['application/pdf'],
                'max_size' => '25MB'
            ]
        ];
    }
}
