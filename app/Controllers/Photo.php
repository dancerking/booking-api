<?php

namespace App\Controllers;

use App\Models\PhotoContentModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Photo extends ResourceController
{
	/**
	 * Return an array of resource objects, themselves in array format
	 *
	 * @return mixed
	 */
	use ResponseTrait;
	public function index()
	{
        $host_id = 1;
        $photos = new PhotoContentModel();
        $L1_type_photos = $photos->get_level1_photo($host_id);
        $L2_type_photos = $photos->get_level2_photo($host_id);
        return $this->respond([
            'L1_type_photos' => $L1_type_photos == null ? [] : $L1_type_photos,
            'L2_type_photos' => $L2_type_photos == null ? [] : $L2_type_photos,
        ], 200);
		// $key = getenv('TOKEN_SECRET');
		// $header = $this->request->getServer('HTTP_AUTHORIZATION');
		// if(!$header) return $this->failUnauthorized('Token Required');
		// $token = explode(' ', $header)[1];
		// try {
		// 	$decoded = JWT::decode($token, new Key($key, 'HS256'));
		// 	$response = [
		// 		'id' => $decoded->uid,
		// 		'username' => $decoded->username,
        //         			];
		// 	return $this->respond($response);
		// } catch (\Throwable $th) {
		// 	return $this->fail('Invalid Token');
		// }
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
