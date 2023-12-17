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

    public $data;

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
