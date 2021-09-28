<?php

namespace App\Console\Commands;

use App\Models\ApiUser;
use Illuminate\Console\Command;

class ListApiUsers extends Command {

    protected $signature = 'api_user:list';
    protected $description = 'List api users';

    public function handle(): void {
        ApiUser::all()->each(
                fn($user) => $this->info($user->name)
        );
    }
}
