<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\APIBaseController;
use App\Models\ApiAdminModel;
use App\Models\HostModel;
use App\Models\IpWhiteListModel;
use Firebase\JWT\JWT;
class Login extends APIBaseController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        helper(['form']);
        $config = config('Config\App');
        /* Validator */
        $rules = [
            'username' => 'required|valid_email',
            'password' => 'required',
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
                'login'
            );
        }

        // Get Host IP address
        $host_ip = $this->request->getIPAddress();
        // Load Models
        $host_model = new HostModel();
        $ip_white_model = new IpWhiteListModel();
        $api_admin_model = new ApiAdminModel();

        // Check if host or admin
        $host = $host_model
            ->where(
                'host_referral_email',
                $this->request->getVar('username')
            )
            ->first();
        $api_admin = $api_admin_model
            ->where(
                'api_admin_username',
                $this->request->getVar('username')
            )
            ->first();
        if ($host == null && $api_admin == null) {
            return $this->notifyError(
                'Email Not Found',
                'notFound',
                'login'
            );
        }
        // In admin case
        if ($api_admin != null) {
            $admin_verify =
                hash(
                    'sha512',
                    $this->request->getVar('password')
                ) == $api_admin['api_admin_password']
                    ? true
                    : false;

            if (!$admin_verify) {
                return $this->notifyError(
                    'Wrong password',
                    'notFound',
                    'login'
                );
            }
            $config->USER_LEVEL =
                $config->USER_LEVELS['admin'];
        }
        // In host case
        if ($host != null) {
            $host_verify =
                hash(
                    'sha512',
                    $this->request->getVar('password')
                ) == $host['host_password_security']
                    ? true
                    : false;

            if (!$host_verify) {
                return $this->notifyError(
                    'Wrong password',
                    'notFound',
                    'login'
                );
            }
            $config->USER_LEVEL =
                $config->USER_LEVELS['host'];
        }

        $check_hostID = $ip_white_model
            ->where('host_id', $host['host_id'])
            ->first();

        if ($check_hostID == null) {
            return $this->notifyError(
                'This host is black host',
                'notFound',
                'login'
            );
        }
        $check_whiteIP = $ip_white_model
            ->where('white_ip', $host_ip)
            ->first();

        if ($check_whiteIP == null) {
            return $this->notifyError(
                'This IP is black IP',
                'notFound',
                'login'
            );
        }

        $key = getenv('TOKEN_SECRET');

        $payload = [
            'iat' => 1356999524,
            'nbf' => 1357000000,
            'username' => $this->request->getVar(
                'username'
            ),
            'password' => $this->request->getVar(
                'password'
            ),
            'host_ip' => $host_ip,
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

        return $this->respond([
            'jwtToken' => $token,
            'message' => 'Successfully created.',
        ]);
    }
}
