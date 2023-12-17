<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
        Log::info("SavePeopleToDatabase!");
        $data = cache()->get("FetchPeopleJob-response");
        Log::info("Дані, щойно з Редісу!!!!!!:", [count($data)]);   
        // foreach ($this->data as $personData) {
        //     // Assuming 'id' is a unique identifier
        //     Person::upsert($personData, ['id']);
        // }
    }
}
