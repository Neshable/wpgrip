<?php

namespace App\Ai\Tools;

use App\Models\Site;
use App\Services\SiteMdService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetSiteInfo implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Get the full site information snapshot including WordPress version, server details, plugins, themes, and configuration.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        $content = app(SiteMdService::class)->read($this->site);

        return $content ?: 'No site snapshot available. The user should trigger a site sync first.';
    }
}
