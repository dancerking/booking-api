<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\HostModel;
use App\Models\IpWhiteListModel;
use Firebase\JWT\JWT;
class Login extends ResourceController
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
        $rules = [
            'username' => 'required|valid_email',
            'password' => 'required'
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        // Get Host IP address
        $host_ip = $this->request->getIPAddress();

        if(!$this->request->isValidIP($host_ip)) {
            return $this->fail('IP Not Valid');
        }

        $host_model = new HostModel();
        $ip_white_model = new IpWhiteListModel();

        $host = $host_model->where("host_referral_email", $this->request->getVar('username'))->first();
        if($host == null) return $this->failNotFound('Email Not Found');
        $verify = hash('sha512', $this->request->getVar('password')) == $host['host_password_security'] ? true : false;
        if(!$verify) return $this->fail('Wrong Password');
        $check_hostID = $ip_white_model->where("host_id", $host['host_id'])->first();
        if($check_hostID == null) return $this->fail('This host is black host');
        $check_whiteIP = $ip_white_model->where('white_ip', $host_ip)->first();
        if($check_whiteIP == null) return $this->fail('This IP is black IP');

        $key = getenv('TOKEN_SECRET');
        $payload = array(
            "iat"   => 1356999524,
            "nbf"   => 1357000000,
            "username" => $this->request->getVar('username'),
            "password" => $this->request->getVar('password'),
            "host_ip"  => $host_ip,
            "host_id"  => $host['host_id'],
            //"exp"   => time() + (30000), //Expire the JWT after 30 secs from now
        );

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond($token);
    }
}
