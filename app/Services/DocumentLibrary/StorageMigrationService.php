<?php

namespace App\Services\DocumentLibrary;

use App\Models\DocumentLibrary\MasterFile;
use App\Models\DocumentLibrary\StorageLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * StorageMigrationService - Handles migration of documents between storage locations
 *
 * This service allows administrators to move files from one storage location to another
 * while keeping the document records and paths transparent to end users.
 */
class StorageMigrationService
{
    /**
     * Migrate a single document to a new storage location
     */
    public function migrateDocument(MasterFile $document, StorageLocation $destination): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'bytes_transferred' => 0,
        ];

        // Validate destination
        if ($destination->is_readonly) {
            $result['message'] = 'Destination storage is read-only';

            return $result;
        }

        if (! $destination->is_active) {
            $result['message'] = 'Destination storage is not active';

            return $result;
        }

        // Check if already in destination
        if ($document->storage_location_id === $destination->id) {
            $result['message'] = 'Document is already in this storage location';
            $result['success'] = true;

            return $result;
        }

        // Check space
        if (! $destination->hasSpaceFor($document->file_size)) {
            $result['message'] = 'Not enough space in destination storage';

            return $result;
        }

        try {
            DB::beginTransaction();

            // Get source disk (use legacy public disk if no storage location)
            $sourceDisk = $document->getStorageDisk();
            $sourceLocation = $document->storageLocation;

            // Register and get destination disk
            $destDisk = $destination->disk();

            // Check source file exists
            if (! $sourceDisk->exists($document->file_path)) {
                $result['message'] = 'Source file not found: '.$document->file_path;
                DB::rollBack();

                return $result;
            }

            // Get file contents
            $contents = $sourceDisk->get($document->file_path);

            // Determine new path (keep same relative path structure)
            $newPath = $destination->getStoragePath('master-files/'.date('Y/m')).'/'.$document->original_filename;

            // Ensure unique filename
            $counter = 1;
            $basePath = pathinfo($newPath, PATHINFO_DIRNAME);
            $filename = pathinfo($newPath, PATHINFO_FILENAME);
            $extension = pathinfo($newPath, PATHINFO_EXTENSION);

            while ($destDisk->exists($newPath)) {
                $newPath = "{$basePath}/{$filename}_{$counter}.{$extension}";
                $counter++;
            }

            // Store file in destination
            $stored = $destDisk->put($newPath, $contents);

            if (! $stored) {
                $result['message'] = 'Failed to store file in destination';
                DB::rollBack();

                return $result;
            }

            // Update document record
            $oldPath = $document->file_path;
            $document->update([
                'file_path' => $newPath,
                'storage_location_id' => $destination->id,
            ]);

            // Update storage usage counters
            $destination->addUsedSpace($document->file_size);
            if ($sourceLocation) {
                $sourceLocation->removeUsedSpace($document->file_size);
            }

            // Delete source file (after successful migration)
            $sourceDisk->delete($oldPath);

            DB::commit();

            $result['success'] = true;
            $result['message'] = 'Document migrated successfully';
            $result['bytes_transferred'] = $document->file_size;

            Log::info('Document migrated', [
                'document_id' => $document->id,
                'from_location' => $sourceLocation?->name ?? 'Public (Legacy)',
                'to_location' => $destination->name,
                'file_size' => $document->file_size,
            ]);

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document migration failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            $result['message'] = 'Migration failed: '.$e->getMessage();

            return $result;
        }
    }

    /**
     * Migrate multiple documents to a new storage location
     */
    public function migrateDocuments(array $documentIds, StorageLocation $destination): array
    {
        $results = [
            'total' => count($documentIds),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'bytes_transferred' => 0,
            'errors' => [],
        ];

        foreach ($documentIds as $id) {
            $document = MasterFile::find($id);
            if (! $document) {
                $results['skipped']++;
                $results['errors'][] = "Document {$id} not found";

                continue;
            }

            $migrationResult = $this->migrateDocument($document, $destination);

            if ($migrationResult['success']) {
                $results['success']++;
                $results['bytes_transferred'] += $migrationResult['bytes_transferred'];
            } else {
                $results['failed']++;
                $results['errors'][] = "Document {$id}: ".$migrationResult['message'];
            }
        }

        return $results;
    }

    /**
     * Migrate all documents from one location to another
     */
    public function migrateAllFromLocation(StorageLocation $source, StorageLocation $destination): array
    {
        $documentIds = $source->documents()->pluck('id')->toArray();

        return $this->migrateDocuments($documentIds, $destination);
    }

    /**
     * Migrate legacy documents (without storage_location_id) to a storage location
     */
    public function migrateLegacyDocuments(StorageLocation $destination, ?int $limit = null): array
    {
        $query = MasterFile::whereNull('storage_location_id');

        if ($limit) {
            $query->limit($limit);
        }

        $documentIds = $query->pluck('id')->toArray();

        return $this->migrateDocuments($documentIds, $destination);
    }

    /**
     * Get migration statistics
     */
    public function getMigrationStats(): array
    {
        $legacyCount = MasterFile::whereNull('storage_location_id')->count();
        $legacySize = MasterFile::whereNull('storage_location_id')->sum('file_size');

        $locations = StorageLocation::withCount('documents')
            ->get()
            ->map(function ($loc) {
                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'documents_count' => $loc->documents_count,
                    'used_size' => $loc->formatted_used_size,
                    'max_size' => $loc->formatted_max_size,
                    'usage_percentage' => $loc->usage_percentage,
                    'is_default' => $loc->is_default,
                    'is_active' => $loc->is_active,
                    'is_readonly' => $loc->is_readonly,
                ];
            });

        return [
            'legacy_documents' => $legacyCount,
            'legacy_size' => $this->formatBytes($legacySize),
            'storage_locations' => $locations,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 4) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
