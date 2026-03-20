<?php

namespace App\Ai\Tools;

use App\Models\Site;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetPerformanceData implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Get the latest performance test results (Lighthouse/PageSpeed scores) for the site.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()->min(1)->max(20)->default(5),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $limit = $request['limit'] ?? 5;

        $data = DB::table('performance_data')
            ->where('site_id', $this->site->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        if ($data->isEmpty()) {
            return 'No performance data available for this site. Performance tests may not have been run yet.';
        }

        $lines = [
            "## Performance History (last {$limit} tests)",
            '| Date | Performance | Accessibility | Best Practices | SEO |',
            '|---|---|---|---|---|',
        ];

        foreach ($data as $row) {
            $lines[] = sprintf(
                '| %s | %s | %s | %s | %s |',
                $row->created_at ?? 'n/a',
                $row->performance ?? 'n/a',
                $row->accessibility ?? 'n/a',
                $row->best_practices ?? 'n/a',
                $row->seo ?? 'n/a',
            );
        }

        return implode("\n", $lines);
    }
}
