<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class Register extends ResourceController
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
			'email' => 'required|valid_email|is_unique[users.email]',
			'password' => 'required|min_length[6]',
			'confpassword' => 'matches[password]'
		];
		if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
		$data = [
			'email' 	=> $this->request->getVar('email'),
			'password'  => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
		];
		$model = new UserModel();
		$registered = $model->save($data);
		$this->respondCreated($registered);
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
