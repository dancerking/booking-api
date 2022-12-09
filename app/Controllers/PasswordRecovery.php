<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\ApiAdminModel;
use App\Models\HostModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class PasswordRecovery extends APIBaseController
{
    /**
     * Send email with token and link
     * POST/password-recovery
     * @return mixed
     */
    use ResponseTrait;
    public function password_recovery()
    {
        $config = config('Config\App');

        /* Load Models */
        $host_model = new HostModel();
        $api_admin_model = new ApiAdminModel();
        /* Validate */
        if (
            !$this->validate([
                'username' => 'required|valid_email',
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
                'password_recovery'
            );
        }

        /* Getting request data */
        $to_email = $this->request->getVar('username');

        /* Setting user level */
        // Check if host or admin
        $host = $host_model
            ->where('host_referral_email', $to_email)
            ->first();
        $api_admin = $api_admin_model
            ->where('api_admin_username', $to_email)
            ->first();
        if ($host == null && $api_admin == null) {
            return $this->notifyError(
                'Email Not Found',
                'notFound',
                'password_recovery'
            );
        }
        // setting user level
        if ($api_admin != null) {
            $config->USER_LEVEL =
                $config->USER_LEVELS['admin'];
        }
        if ($host != null) {
            $config->USER_LEVEL =
                $config->USER_LEVELS['host'];
        }

        /* Create token */
        $key = getenv('TOKEN_SECRET');

        $payload = [
            'iat' => 1356999524,
            'nbf' => 1357000000,
            'username' => $to_email,
            'host_id' =>
                $host != null ? $host['host_id'] : 0,
            'admin_id' =>
                $api_admin != null
                    ? $api_admin['api_admin_id']
                    : 0,
            'user_level' => $config->USER_LEVEL,
            'exp' => time() + $config->EXPIRATION_PERIOD, //Expire the JWT after 30 days from now
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        $link =
            '<a href="s-api.italiapromotion.it/password-reset?token=' .
            $token .
            '">Click To Reset password</a>';

        /* Send message */
        $subject = 'Password Recovery';
        $message =
            'Hello. It seems you forgot your login details. A request to reset your password has been received. For security reasons the initial password cannot be sent over email, you must reset it. Please click on the link to reset your password.' .
            $link .
            '';

        $email = \Config\Services::email();
        $email->setTo($to_email);
        $email->setFrom(
            'alominadze79@gmail.com',
            'Avtandil Lominadze'
        );

        $email->setSubject($subject);
        $email->setMessage($message);
        if ($email->send()) {
            return $this->respond([
                'message' => 'Email successfully sent',
            ]);
        } else {
            $data = $email->printDebugger(['headers']);
            return $this->notifyError(
                $data,
                'bad_request',
                'password_recovery'
            );
        }
    }

    /**
     * Reset password
     * PUT/password-reset
     * @return mixed
     */
    public function password_reset()
    {
        helper(['form']);
        $config = config('Config\App');

        /* Getting user_level from JWT token */
        $response = $config->JWTresponse;
        $admin_id = $response['admin_id'];
        $host_id = $response['host_id'];
        $username = $response['username'];

        /* Load Model */
        $host_model = new HostModel();
        $api_admin_model = new ApiAdminModel();

        /* Validate */
        $rules = [
            'username' => 'required|valid_email',
            'password' =>
                'required|min_length[7]|regex_match[/[a-z]/]|regex_match[/[A-Z]/]|regex_match[/[0-9]/]|regex_match[/[!#$%^&*]/]',
        ];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'password_recovery'
            );
        }

        /* Getting request data */
        $email = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        /* validation */
        if ($username != $email) {
            return $this->notifyError(
                'This email is not email for recovery.',
                'invalid_data',
                'password_recovery'
            );
        }
        /* Update */
        if ($host_id != 0) {
            $data = [
                'host_password_security' => hash(
                    'sha512',
                    $password
                ),
            ];
            $host_data = $host_model
                ->where('host_referral_email', $email)
                ->first();
            if (
                !$host_model->update(
                    $host_data['host_id'],
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'password_recovery'
                );
            }
        }
        if ($admin_id != 0) {
            $api_admin_data = [
                'api_admin_password' => hash(
                    'sha512',
                    $password
                ),
            ];
            $admin_data = $api_admin_model
                ->where('api_admin_username', $email)
                ->first();
            if (
                !$api_admin_model->update(
                    $admin_data['api_admin_id'],
                    $api_admin_data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'password_recovery'
                );
            }
        }

        return $this->respond([
            'message' => 'Successfully updated',
        ]);
    }
}
