<?php

namespace App\Console\Commands;

use App\Models\ApiUser;
use Illuminate\Console\Command;
use Str;

class CreateApiUser extends Command {

    protected $signature = 'api_user:create {name}';
    protected $description = 'Create user to get auth token';

    public function handle(): void {
        $token = Str::random(60);

        $apiUser = new ApiUser();
        $apiUser->name = $this->argument('name');
        $apiUser->token = hash('sha256', $token);
        $apiUser->save();

        $this->info("Token: $token");
    }
}
