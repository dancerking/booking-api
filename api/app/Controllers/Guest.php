<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\GuestModel;
use App\Models\HostBookingModel;
use App\Models\HostModel;
use CodeIgniter\API\ResponseTrait;

class Guest extends APIBaseController
{
    /**
     * Return an array of Gueset
     * GET/guests
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        /* Import config variable */
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();
        /* Load Model */
        $guest_model = new GuestModel();
        $host_booking_model = new HostBookingModel();
        $host_model = new HostModel();

        /* validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'guest'
            );
        }
        /* Getting request data */
        $name_part = $this->request->getVar('name');
        $surname_part = $this->request->getVar('surname');
        $mail_part = $this->request->getVar('mail');
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'guest'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        /* Search guest data */
        $host_booking_users = $host_booking_model
            ->select(
                'host_booking_referral_email, host_booking_id, host_booking_status'
            )
            ->where('host_booking_host_id', $host_id)
            ->findAll();
        $guest_data = [];
        if (
            $name_part != null ||
            $surname_part != null ||
            $mail_part != null
        ) {
            foreach ($host_booking_users as $user) {
                $data = $guest_model
                    ->select(
                        'guest_id as id, guest_referral_name as name, guest_referral_surname as surname, guest_referral_email as e-mail, guest_mobile_phone as tel, ' .
                            $user['host_booking_id'] .
                            ' as booking_id, ' .
                            $user['host_booking_status'] .
                            ' as booking_status'
                    )
                    ->where(
                        'guest_referral_email',
                        $user['host_booking_referral_email']
                    )
                    ->groupStart()
                    ->like(
                        'guest_referral_name',
                        '%' .
                            ($name_part == null
                                ? ''
                                : $name_part) .
                            '%'
                    )
                    ->like(
                        'guest_referral_surname',
                        '%' .
                            ($surname_part == null
                                ? ''
                                : $surname_part) .
                            '%'
                    )
                    ->like(
                        'guest_referral_email',
                        '%' .
                            ($mail_part == null
                                ? ''
                                : $mail_part) .
                            '%'
                    )
                    ->groupEnd()
                    ->first();
                if ($data != null) {
                    array_push($guest_data, $data);
                }
            }
        }
        if (
            $name_part == null &&
            $surname_part == null &&
            $mail_part == null
        ) {
            foreach ($host_booking_users as $user) {
                $data = $guest_model
                    ->select(
                        'guest_id as id, guest_referral_name as name, guest_referral_surname as surname, guest_referral_email as e-mail, guest_mobile_phone as tel, ' .
                            $user['host_booking_id'] .
                            ' as booking_id, ' .
                            $user['host_booking_status'] .
                            ' as booking_status'
                    )
                    ->where(
                        'guest_referral_email',
                        $user['host_booking_referral_email']
                    )
                    ->first();
                array_push($guest_data, $data);
            }
        }

        return parent::respond(
            [
                'guest_data' => $guest_data,
            ],
            200
        );
    }
}
