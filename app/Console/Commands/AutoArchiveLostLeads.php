<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class AutoArchiveLostLeads extends Command
{
    protected $signature = 'app:auto-archive-leads';

    protected $description = 'Archive NEW leads older than 30 days as LOST.';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        // Idempotent: only NEW leads are transitioned; already LOST leads untouched.
        $count = Lead::where('status', 'NEW')
            ->where('created_at', '<', $cutoff)
            ->update(['status' => 'LOST']);

        $this->info("Auto-archived {$count} leads to LOST.");

        return 0;
    }
}
