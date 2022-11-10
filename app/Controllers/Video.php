<?php

namespace App\Controllers;

use App\Models\VideoContentModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Video extends ResourceController
{
	/**
	 * Return an array of resource objects, themselves in array format
	 *
	 * @return mixed
	 */
	use ResponseTrait;
	public function index()
	{
        $videos = new VideoContentModel();

        /* Getting header_id from JWT token */
        $config = config('Config\App');
        $response = $config->JWTresponse;
		$host_id = $response['host_id'];

       /* Getting photo for level1, level2 from PhotoContentModel */
        $L1_type_videos = $videos->get_level1_video($host_id);
        $L2_type_videos = $videos->get_level2_video($host_id);

        return $this->respond([
            'L1_type_videos' => $L1_type_videos == null ? [] : $L1_type_videos,
            'L2_type_videos' => $L2_type_videos == null ? [] : $L2_type_videos,
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
