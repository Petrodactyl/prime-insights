<?php

namespace App\Console\Commands;

use App\Jobs\SavePeopleToDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Person;
use App\Jobs\FetchPeopleJob;

class FetchPeopleData extends Command
{
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
        Log::info("FetchPeopleData команда працює!");

        $fetchJob = new FetchPeopleJob();
        $saveJob = new SavePeopleToDatabase(); //TODO: rename job

        Bus::chain([
            $fetchJob,
            $saveJob
        ])->onQueue('default')->dispatch();
    }
}
