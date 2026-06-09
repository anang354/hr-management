<?php
// app/Services/BiometricSyncService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiometricSyncService
{
    protected $nodeApiUrl;

    public function __construct()
    {
        $this->nodeApiUrl = config('services.node_sync.url', 'http://localhost:3001');
    }

    /**
     * Sinkronisasi single user ke semua mesin
     */
    public function syncUser(string $userId, array $targetMachines = ['all']): array
    {
        try {
            $payload = [
                'userId' => $userId,
                'targetMachines' => $targetMachines
            ];

            $response = Http::timeout(120)
                ->retry(2, 1000)
                ->post($this->nodeApiUrl . '/api/sync/user', $payload);

            if ($response->successful()) {
                $result = $response->json();

                // Log success
                Log::info("Biometric sync successful for user: {$userId}", [
                    'uploaded' => $result['data']['summary']['templatesUploaded'] ?? 0,
                    'skipped' => $result['data']['summary']['templatesSkipped'] ?? 0
                ]);

                return [
                    'success' => true,
                    'message' => $result['message'] ?? 'User synced successfully',
                    'data' => $result['data'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => 'Sync server returned error: ' . $response->status(),
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error("Biometric sync failed for user: {$userId}", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to connect to sync server: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Cek status user di semua mesin
     */
    public function getUserSyncStatus(string $userId): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->nodeApiUrl . "/api/sync/user/{$userId}/status");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Failed to get user status'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
