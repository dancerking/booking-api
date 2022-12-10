<?php

namespace App\Controllers;

use App\Models\LogModel;
use CodeIgniter\RESTful\ResourceController;
use DateTime;

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
        'booking' => 1200,
        'host' => 1300,
        'register' => 1400,
        'password_recovery' => 1500,
        'stripe' => 1600,
        'property_calendar' => 1700,
        'property_type' => 1800,
        'guest' => 1900,
        'filters_mapping' => 2000,
        'property' => 2100,
    ];

    protected $errorCodes = [
        'unknown' => 0,
        'notFound' => 1,
        'invalid_data' => 2,
        'invalid_request' => 3,
        'failed_create' => 4,
        'failed_update' => 5,
        'failed_delete' => 6,
        'duplicate' => 7,
        'overflow' => 8,
        'notAllowedAccess' => 9,
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
        return self::respond($response, 400);
    }

    protected function respond(
        $data = null,
        ?int $status = null,
        string $message = ''
    ) {
        $log_model = new LogModel();
        $log_model->insert([
            'log_host_id' => self::get_host_id(),
            'log_time' => time(),
            'log_request' => json_encode(
                $this->request->getJSON()
            ),
            'log_response' => json_encode($data),
            'log_error' => $status == 400,
        ]);
        return parent::respond($data, $status, $message);
    }

    public function get_host_id()
    {
        /* Getting header_id from JWT token */
        $config = config('Config\App');
        $response = $config->JWTresponse;
        $host_id =
            $response == null ? 0 : $response['host_id'];
        return $host_id;
    }

    public function get_userlevel()
    {
        /* Getting header_id from JWT token */
        $config = config('Config\App');
        $response = $config->JWTresponse;
        $user_level = $response['user_level'];
        return $user_level;
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
