<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class APIBaseController extends ResourceController
{
    protected $sectionCodes = [
        'unknown' => 0,
        'login' => 100,
        'code' => 200,
        'photo' => 300,
        'video' => 400,
        'availability' => 500,
        'rate' => 600,
        'rate_calendar' => 700,
        'filter' => 800,
        'promo' => 900,
        'service' => 1000,
        'service_calendar' => 1100,
    ];

    protected $errorCodes = [
        'unknown' => 0,
        'notFound' => 1,
        'invalid_data' => 2,
        'invalid_request' => 3,
        'failed_create' => 4,
        'failed_update' => 5,
        'failed_delete' => 6,
    ];

    protected function notifyError(
        ?string $message = null,
        ?string $error = 'unknown',
        ?string $section = 'unknown'
    ) {
        $safeSection = isset($this->sectionCodes[$section])
            ? $section
            : 'unknown';
        $safeError = isset($this->errorCodes[$error])
            ? $error
            : 'unknown';
        $response = [
            'error' => 1,
            'code' =>
                $this->sectionCodes[$safeSection] +
                $this->errorCodes[$safeError],
            'message' =>
                $message == null
                    ? lang('APIErrors.' . $safeError)
                    : $message,
        ];
        return $this->respond($response, 400);
    }

    public function get_host_id()
    {
        /* Getting header_id from JWT token */
        $config = config('Config\App');
        $response = $config->JWTresponse;
        $host_id = $response['host_id'];
        return $host_id;
    }
}
