<?php
// app/Services/SyncService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncService
{
    protected $nodeApiUrl;

    public function __construct()
    {
        $this->nodeApiUrl = config('services.node_sync.url', 'http://localhost:3001');
    }

    /**
     * Sinkronisasi penuh ke semua mesin
     */
    public function syncAll(string $direction = 'both', bool $dryRun = false, bool $force = false): array
    {
        try {
            $response = Http::timeout(600) // 10 menit timeout
                ->retry(3, 1000)
                ->post("{$this->nodeApiUrl}/api/sync/manual", [
                    'direction' => $direction,
                    'options' => [
                        'dryRun' => $dryRun,
                        'force' => $force
                    ]
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Sync server returned error: ' . $response->status(),
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('Sync failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to connect to sync server: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Sinkronisasi single user
     */
    public function syncSingleUser(string $userId): array
    {
        // Ambil data user dan template dari database
        $templates = \App\Models\BiometricBackup::where('biometric_id', $userId)
            ->whereNotNull('template')
            ->where('template', '!=', '')
            ->get();

        if ($templates->isEmpty()) {
            return [
                'success' => false,
                'message' => "User {$userId} has no fingerprint templates"
            ];
        }

        $userData = [
            'userId' => $userId,
            'templates' => $templates->map(function ($tmpl) {
                return [
                    'fingerId' => $tmpl->finger_index,
                    'template' => $tmpl->template
                ];
            })->toArray()
        ];

        try {
            $response = Http::timeout(120)
                ->post("{$this->nodeApiUrl}/api/sync/user", $userData);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Sync failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sinkronisasi multiple users
     */
    public function syncMultipleUsers(array $userIds): array
    {
        $results = [];
        $successCount = 0;

        foreach ($userIds as $userId) {
            $result = $this->syncSingleUser($userId);
            $results[$userId] = $result;
            if ($result['success']) {
                $successCount++;
            }
        }

        return [
            'total' => count($userIds),
            'synced' => $successCount,
            'failed' => count($userIds) - $successCount,
            'results' => $results
        ];
    }

    /**
     * Get sync status
     */
    public function getSyncStatus(): array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->nodeApiUrl}/api/sync/status");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Failed to get sync status'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Sync server is offline'
            ];
        }
    }
}
