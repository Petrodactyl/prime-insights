<?php

namespace App\Console\Commands;

use App\Jobs\SavePeopleToDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendRequestToGetPeople;

class FetchPeopleData extends Command
{
    /**
     * Maximum number of jobs in a 'fetch-people-queue' queue
     *
     * @var int
     */
    const MAX_JOB_IN_QUEUE = 5;

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
        // prevent dispatching a new job chain if there are already allowed number of jobs in progress
        if (Queue::size('fetch-people-queue') < self::MAX_JOB_IN_QUEUE) 
        {
            Bus::chain([
                new SendRequestToGetPeople,
                new SavePeopleToDatabase
            ])->dispatch();
        }
    }
}
