<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class PurgeDeletedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-deleted-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge users soft-deleted for more than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ✅ FIX ISSUE #42: Permanently delete users soft-deleted for 30+ days
        $count = User::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(30))
            ->forceDelete();

        $this->info("Successfully purged {$count} deleted users older than 30 days.");
    }
}
