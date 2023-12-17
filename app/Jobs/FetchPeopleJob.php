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

class FetchPeopleJob implements ShouldQueue
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

    public function __construct() 
    {
        $this->onQueue('fetch-people-queue');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("FetchPeopleJob джоба стартанула!");

        // app('redis')->set('last job started', now());

        $response = Http::get('https://tech.primeinsights.net/api/people');

        // Log::info("FetchPeopleJob витягнула дані:", [$response->body()]);
        

        if ($response->successful()) {
            Log::info("FetchPeopleJob успішно витягнула дані:", [substr($response->body(), 0, 100)]);
            // $this->data = '$response->json()';
            cache()->put("FetchPeopleJob-response", $response->json(), now()->addMinute());
        } else {
            // Handle error
            Log::error("FetchPeopleJob зафейлилась:", [$response->reason()]);
            $this->fail($response->reason());
        }
    }
}
