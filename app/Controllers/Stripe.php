<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\HostModel;
use App\Models\StripeModel;
use CodeIgniter\API\ResponseTrait;

class Stripe extends APIBaseController
{
    /**
     * Return an array of stripe public code
     * GET/stripe
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $stripe_model = new StripeModel();

        /* Validate */
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
                'stripe'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'stripe'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'stripe'
            );
        }
        /* Getting data from db*/
        $stripe_public_code = $stripe_model
            ->where('stripe_host_id', $host_id)
            ->first();
        return $this->respond([
            'stripe_public_code' =>
                $stripe_public_code == null
                    ? ''
                    : $this->replace_with_special_char(
                        $stripe_public_code[
                            'stripe_public'
                        ],
                        3
                    ),
        ]);
    }

    /**
     * Update and return id
     * UPDATE/stripe/update
     * @return mixed
     */
    use ResponseTrait;
    public function update($id = null)
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $stripe_model = new StripeModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'stripe_public' => 'required',
                'stripe_secret' => 'required',
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
                'stripe'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $stripe_public = $this->request->getVar(
            'stripe_public'
        );
        $stripe_secret = $this->request->getVar(
            'stripe_secret'
        );
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'stripe'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'stripe'
            );
        }
        /* Update or Insert */
        $new_id = '';
        $data = [
            'stripe_host_id' => $host_id,
            'stripe_public' => $stripe_public,
            'stripe_secret' => $stripe_secret,
        ];
        $stripe_data = $stripe_model
            ->where([
                'stripe_host_id' => $host_id,
            ])
            ->first();
        if ($stripe_data == null) {
            $new_id = $stripe_model->insert($data);
            if (!$new_id) {
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'stripe'
                );
            }
        } else {
            if (
                !$stripe_model->update(
                    $stripe_data['stripe_id'],
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'stripe'
                );
            }
        }
        return $this->respond([
            'id' =>
                $stripe_data == null
                    ? $new_id
                    : $stripe_data['stripe_id'],
            'message' =>
                'Successfully ' .
                ($stripe_data == null
                    ? 'created'
                    : 'updated'),
        ]);
    }

    public function replace_with_special_char(
        $main_string,
        $last_number
    ) {
        $string = $main_string;
        $length = strlen($main_string);
        for ($i = 0; $i < $length - $last_number; $i++) {
            $string[$i] = '*';
        }
        return $string;
    }
}
