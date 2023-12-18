<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendRequestToGetPeople implements ShouldQueue
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
     * Create a new job instance on a 'fetch-people-queue'
     */
    public function __construct() 
    {
        $this->onQueue('fetch-people-queue');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try 
        {
            // Send Request
            $response = Http::get('https://tech.primeinsights.net/api/people');

            if ($response->successful()) 
            {
                // no need to store this data more than a minute
                cache()->put("People-Data", $response->json(), now()->addMinute());
            } 
            else 
            {
                // Handle error
                throw new \Exception($response->reason());
            }
        } 
        catch (\Throwable $e) 
        {
            Log::error("An exception occurred while sending request to fetch People: " . $e->getMessage());
            // Include the stack trace
            Log::error($e->getTraceAsString());

            // Mark the job as failed
            $this->fail($e);
        }
    }
}
