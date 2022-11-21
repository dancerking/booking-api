<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $users = new UserModel();
        return $this->respond(
            ['users' => $users->findAll()],
            200
        );
    }

    public function show($id = null)
    {
        $model = new UserModel();
        $data = $model
            ->getWhere(['id' => $id])
            ->getResult();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound(
                'No Data Found with id ' . $id
            );
        }
    }

    public function create()
    {
        $model = new UserModel();
        $data = [
            'email' => $this->request->getVar('email'),
            'password' => password_hash(
                $this->request->getVar('password'),
                PASSWORD_DEFAULT
            ),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $model->insert($data);
        $response = [
            'status' => 201,
            'error' => null,
            'messages' => [
                'success' => 'Data Saved',
            ],
        ];
        return $this->respondCreated($response);
    }

    // update product
    public function update($id = null)
    {
        $model = new UserModel();
        $data = [
            'email' => $this->request->getVar('email'),
            'password' => password_hash(
                $this->request->getVar('password'),
                PASSWORD_DEFAULT
            ),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $model->update($id, $data);
        $response = [
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => 'Data Updated',
            ],
        ];
        return $this->respond($response);
    }

    // delete product
    public function delete($id = null)
    {
        $model = new UserModel();
        $model->delete($id);
        $response = [
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => 'Data Deleted',
            ],
        ];
        return $this->respond($response);
    }
}
