<?php

namespace App\Console\Commands;

use App\Models\Household;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LinkOrphanCitizens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'households:link-orphans {--dry-run : Show what would be linked without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link citizen users without household_id to existing households by national ID (head_national_id)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $orphans = User::query()
            ->where('is_staff', false)
            ->whereNull('household_id')
            ->get();

        $linked = 0;
        $skippedNoMatch = 0;
        $skippedAlreadyLinked = 0;

        foreach ($orphans as $user) {
            $household = Household::where('head_national_id', $user->national_id)->first();

            if (!$household) {
                $skippedNoMatch++;
                continue;
            }

            $alreadyLinked = User::where('household_id', $household->id)->exists();
            if ($alreadyLinked) {
                $skippedAlreadyLinked++;
                continue;
            }

            $this->line(sprintf(
                '%s -> household #%d (%s)',
                $user->national_id,
                $household->id,
                $household->head_name
            ));

            if ($dryRun) {
                $linked++;
                continue;
            }

            DB::transaction(function () use ($user, $household) {
                // Keep household head data in sync with the user account
                $household->update([
                    'head_name' => $user->full_name,
                    'head_national_id' => $user->national_id,
                    'head_birth_date' => $user->birth_date ?? $household->head_birth_date,
                ]);

                $user->household_id = $household->id;
                $user->save();
            });

            $linked++;
        }

        $this->info("Linked: {$linked}");
        $this->info("Skipped (no household found): {$skippedNoMatch}");
        $this->info("Skipped (household already linked to another user): {$skippedAlreadyLinked}");

        return Command::SUCCESS;
    }
}
