<?php

namespace App\Services;

use App\Models\TechnicalLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

class TechnicalLogger
{
    /**
     * Store an audit-style technical log without interrupting the user flow.
     *
     * @param  array<string, mixed>  $context
     */
    public function record(string $action, string $message, ?int $userId = null, array $context = []): void
    {
        $logData = [
            'datetime' => now()->format('Y-m-d H:i:s'),
            'user_id' => $userId,
            'action' => $action,
            'message' => $message,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $this->writeToAppointmentTechnicalLog($logData);

        try {
            TechnicalLog::query()->create([
                'user_id' => $userId,
                'action' => $action,
                'message' => $message,
                'context' => $context,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Technische log kon niet worden opgeslagen.', [
                'action' => $action,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $logData
     */
    private function writeToAppointmentTechnicalLog(array $logData): void
    {
        try {
            $path = storage_path('logs/appoiments technich.log');
            $line = json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;

            File::append($path, $line);
        } catch (Throwable $exception) {
            Log::warning('Technische afspraak-log kon niet worden opgeslagen.', [
                'exception' => $exception,
            ]);
        }
    }
}
