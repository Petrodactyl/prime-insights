<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Person;

class SavePeopleToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 1;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Create a new job instance on a 'save-people-queue'
     */
    public function __construct()
    {
        $this->onQueue('save-people-queue');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // get data received from API endpoint
            $data = cache()->get("People-Data");

            if (empty($data)) 
            {
                Log::warning("Data returned from API are empty!");

                return;
            }

            // collect IDs for sync purpose
            $fetchedPeople = collect($data);
            $fetchedIds = $fetchedPeople->pluck('id');

            // perform all database-related operations within a transaction
            DB::beginTransaction();

            // Delete records not present in the fetched data
            Person::whereNotIn('id', $fetchedIds)->delete();
            
            // Insert new or update existing records
            Person::upsert($data, ['id']);
        
            DB::commit();
        } catch (\Throwable $e) {
            // rollback transaction and fail the job
            DB::rollBack();

            Log::error("An exception occurred while saving People data to a storage:", [$e->getMessage()]);
            
            $this->fail();
        }

        // save last successfull sync date and time for debug purposes
        cache()->put("Last Successful Sync", now()->toDateTimeString(), now()->addMinutes(5));
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        // use WithoutOverlapping middleware because there should be one job at a time accessing SQLite database
        return [(new WithoutOverlapping())->dontRelease()];
    }
    
}
