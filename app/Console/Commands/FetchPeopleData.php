<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Person;

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
        $response = Http::get('https://tech.primeinsights.net/api/people');
        
        if ($response->successful()) {
            $data = $response->json();

            foreach ($data as $personData) {
                // Assuming 'id' is a unique identifier
                Person::upsert($personData, ['id']);
            }

            $this->info('Data fetched and updated successfully.');
        } else {
            // Handle error
            $this->error('Failed to fetch data from the API.');
        }
    }
}
