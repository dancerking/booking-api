<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        // $rules = [
        //     'email' => 'required|valid_email',
        //     'password' => 'required|min_length[6]'
        // ];
        // if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        // $model = new UserModel();
        // $user = $model->where("email", $this->request->getVar('email'))->first();
        // if(!$user) return $this->failNotFound('Email Not Found');

        // $verify = password_verify($this->request->getVar('password'), $user['password']);
        // if(!$verify) return $this->fail('Wrong Password');

        $key = getenv('TOKEN_SECRET');
        $payload = array(
            "iat"   => 1356999524,
            "nbf"   => 1357000000,
            //"uid"   => $user['id'],
            //"email" => $user['email'],
            "username" => 'dancerking',
            "password" => '123456',
            "host_id"  => '1',
            //"exp"   => time() + (30000), //Expire the JWT after 30 secs from now
        );

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond($token);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
