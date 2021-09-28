<?php

namespace App\Console\Commands;

use App\Models\ApiUser;
use Illuminate\Console\Command;

class DeleteApiUser extends Command {

    protected $signature = 'api_user:delete {name}';
    protected $description = 'Delete api user';

    public function handle(): void {
        $deleted = ApiUser::query()
            ->where('name', $this->argument('name'))
            ->delete();

        $this->info($deleted ? 'Deleted' : 'No such user');
    }
}
