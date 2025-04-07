<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class ExportService
{
    /**
     * Export mentions to CSV
     */
    public function exportMentionsToCsv(User $user, array $filters = []): string
    {
        // Get mentions
        $mentions = $user->mentions()
            ->when(isset($filters['start_date']), function ($query) use ($filters) {
                return $query->where('created_at', '>=', $filters['start_date']);
            })
            ->when(isset($filters['end_date']), function ($query) use ($filters) {
                return $query->where('created_at', '<=', $filters['end_date']);
            })
            ->when(isset($filters['sentiment']), function ($query) use ($filters) {
                return $query->where('sentiment', $filters['sentiment']);
            })
            ->get();
        
        // Create CSV file
        $filename = 'mentions_' . now()->format('Y-m-d_His') . '.csv';
        $path = storage_path('app/exports/' . $filename);
        
        // Create directory if it doesn't exist
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }
        
        // Open file
        $file = fopen($path, 'w');
        
        // Write headers
        fputcsv($file, [
            'ID',
            'Author',
            'Text',
            'Post URL',
            'Post Indexed At',
            'Sentiment',
            'Created At',
        ]);
        
        // Write data
        foreach ($mentions as $mention) {
            fputcsv($file, [
                $mention->id,
                $mention->author_handle,
                $mention->text,
                $mention->post_url,
                $mention->post_indexed_at,
                $mention->sentiment,
                $mention->created_at,
            ]);
        }
        
        // Close file
        fclose($file);
        
        return $path;
    }

    /**
     * Generate a mention report
     */
    public function generateMentionReport(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $mentions = $this->getMentionsForExport($user, $startDate, $endDate);
        
        $stats = [
            'total_mentions' => $mentions->count(),
            'unique_mentioners' => $mentions->unique('author_handle')->count(),
            'keywords' => $mentions->groupBy(function ($mention) {
                return $mention->keyword->keyword ?? 'Unknown';
            })->map->count()->toArray(),
            'daily_breakdown' => $mentions->groupBy(function ($mention) {
                return $mention->post_indexed_at->format('Y-m-d');
            })->map->count()->toArray(),
        ];
        
        return [
            'period' => [
                'start' => $startDate?->format('Y-m-d') ?? 'All time',
                'end' => $endDate?->format('Y-m-d') ?? 'Present',
            ],
            'statistics' => $stats,
        ];
    }

    /**
     * Get mentions for export
     */
    private function getMentionsForExport(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = $user->mentions()
            ->with('keyword')
            ->orderBy('post_indexed_at', 'desc');
            
        if ($startDate) {
            $query->where('post_indexed_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('post_indexed_at', '<=', $endDate);
        }
        
        return $query->get();
    }
} 