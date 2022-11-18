<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class APIBaseController extends ResourceController
{
    protected $sectionCodes = [
        'unknown'                   =>   0,
        'login'                     => 100,
        'code'                      => 200,
        'photo'                     => 300,
        'video'                     => 400,
    ];

    protected $errorCodes = [
        'unknown'                   =>   0,
        'notFound'                  =>   1,
        'invalid_data'              =>   2,
        'invalid_request'           =>   3,
        'failed_create'             =>   4,
        'failed_update'             =>   5,
        'failed_delete'             =>   6,
        'success_create'            =>   7,
        'success_update'            =>   8,
        'success_delete'            =>   9,
    ];

    protected function notifyError(?string $message = null, ?string $error = 'unknown', ?string $section = 'unknown') {
        $safeSection = isset($this->sectionCodes[$section]) ? $section : 'unknown';
        $safeError = isset($this->errorCodes[$error]) ? $error : 'unknown';
        $response = [
            'error'   => 1,
            'code'    => $this->sectionCodes[$safeSection] + $this->errorCodes[$safeError],
            'message' => $message == null ? lang('APIErrors.' . $safeError) : $message,
        ];
        return $this->respond($response);
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
