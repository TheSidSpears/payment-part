<?php


namespace App\Services\Sber;


use App\Models\ApiUser;
use App\Services\Log;
use Illuminate\Http\Request;

class SberCallbackValidation {

    private Log $log;

    public function __construct(Log $log) {
        $this->log = $log;
    }

    public function __invoke(Request $request): ApiUser {
        if (!$this->checksumCorrect($request)){
            $message = 'Sber callback checksum not valid';
            $this->log->warning($message);
            throw new \RuntimeException($message);
        }

        $sberUser = new ApiUser();
        $sberUser->name = 'sber';
        return $sberUser;
    }

    protected function checksumCorrect(Request $request): bool {
        // CENSORED
        return true;
    }
}
