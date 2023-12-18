# Prime Insights Test Demo

## Prerequisites

-   Docker
-   Composer
-   Laravel Sail

## Installation

Git clone and then run a composer.

```
composer install
```

Then create SQLite file `database\primeinsights.sqlite`.
Afterwards run migrations with Artisan. Don't forget to set proper permissions for `storage` folder.

## Configuration

### Environment

Here are the most important .env settings:

```
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/primeinsights.sqlite
DB_FOREIGN_KEYS=true

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Docker Config

Look at `docker-compose.yml` for general Docker config. We need only two containers: one for the app and the second one for the redis storage.

Then check the `docker\8.2\` folder. There you can find a config for Laravel Scheduler and Horizon along with the Dockerfile.

## How to start

Start Laravel Sail:

```
# sail up -d
```

## Structure

The business logic is distributed across three files: `app\Console\Commands\FetchPeopleData.php`, `app/Jobs/SendRequestToGetPeople.php` and `app/Jobs/SavePeopleToDatabase.php`.

`FetchPeopleData.php` dispatches a Job Chain that consists of two jobs: the first one `SendRequestToGetPeople` runs in a `fetch-people-queue` and the second one in a `save-people-queue`.

`FetchPeopleData.php` is scheduled to run every second. All workers are controlled by Laravel Horizon which starts right after docker container is up. Also you can take a look at Supervisor config `docker\8.2\supervisord.conf` to see how Horizon is set up.
