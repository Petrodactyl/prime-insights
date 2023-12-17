<?php

namespace App\Console\Commands;

use App\Jobs\SavePeopleToDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Models\Person;
use App\Jobs\FetchPeopleJob;

class FetchPeopleData extends Command
{
    const MAX_JOB_IN_QUEUE = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-people-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // app('redis')->set('last job started', now());

        // Log::info("Зараз в default черзі: ", [Queue::size('default')]);
        
        // Log::info("Tecтанемо кеш: ");
        cache()->put("Test-Cache", 'data', now()->addMinutes(5));

        // cache()->put('key', 'value', 60);

        if (Queue::size('fetch-people-queue') < self::MAX_JOB_IN_QUEUE) 
        {
            // Log::info("Зараз в fetch-people-queue черзі: ", [Queue::size('fetch-people-queue')]);
            // Log::info("FetchPeopleData команда працює!");

            Bus::chain([
                new FetchPeopleJob(), //TODO: rename job
                new SavePeopleToDatabase()
            ])->dispatch();
        }
    }
}
