<?php


namespace Tests\Feature;


trait ApiAuth {
    public function headers() {
        $merc = '//hash';
        $huyndai = '//hash';

        return [
            'Authorization' => "Bearer $merc",
            'Accept' => 'application/json',
        ];
    }
}
