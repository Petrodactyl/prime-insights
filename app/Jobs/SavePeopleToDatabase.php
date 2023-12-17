<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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
     * Create a new job instance.
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
        Log::info("SavePeopleToDatabase!!!!");
        $data = cache()->get("FetchPeopleJob-response");
        Log::info("Дані, щойно з Редісу!!!!!!:", [count($data)]);

        try {
            DB::beginTransaction();
            
            // Person::truncate();
            // Person::insert(json_decode($data, true));
            Person::upsert($data, ['id']);
        
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("SavePeopleToDatabase зафейлилась:", [$e->getMessage()]);
            
            $this->fail();
        }

        Log::info("Дані (наче) успішно збереглися!");
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping())->dontRelease()];
    }
    
}
