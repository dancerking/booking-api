<?php

namespace App\Controllers;

use App\Models\ContentCaptionModel;
use App\Models\PhotoContentModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Photo extends ResourceController
{
	/**
	 * Return an array of photo content
	 * GET/photos
	 * @return mixed
	 */
	use ResponseTrait;
	public function index()
	{
        $photos = new PhotoContentModel();
        $host_id = $this->get_host_id();
        /* Getting photo for level1, level2 from PhotoContentModel */
        $L1_type_photos = $photos->get_level1_photo($host_id);
        $L2_type_photos = $photos->get_level2_photo($host_id);

        return $this->respond([
            'l1_type_photos' => $L1_type_photos == null ? [] : $L1_type_photos,
            'l2_type_photos' => $L2_type_photos == null ? [] : $L2_type_photos,
        ], 200);
	}

    /**
     * Create a new photo content
     * POST: /photos/add
     * @return mixed
     */
    public function create()
    {
        // Photo content
        $response = [];
        if (! $this->validate([
            'photo_content_level' => 'required|min_length[1]|max_length[1]',
            'img_url' => 'required'
        ])) {
            return $this->fail('Input Data format is incorrect.');
        }
        $host_id = $this->get_host_id();
        $photo_content_level = $this->request->getVar('photo_content_level');
        $photo_content_connection = $photo_content_level == 2 ? $this->request->getVar('photo_content_connection') : '';
        $photo_content_order = $this->request->getVar('photo_content_order') ? $this->request->getVar('photo_content_order') : 0;
        $content_caption = $this->request->getVar('content_caption');
        $img_url = $this->request->getVar('img_url');

        // Insert photo content
        $photo_content_model = new PhotoContentModel();
        $data = [
            'photo_content_host_id'     => $host_id,
            'photo_content_level'       => $photo_content_level,
            'photo_content_connection'  => $photo_content_connection,
            'photo_content_order'       => $photo_content_order,
            'photo_content_status'      => 0,
        ];

        $new_id = $photo_content_model->insert($data);
        if($new_id) {
            $is_upload = $this->uploadImage($img_url, $new_id, $host_id);
            if($is_upload == null) {
                return $this->failNotFound('Failed upload: Cound not find image url');
            }
            if(!$photo_content_model->update($new_id, [
                'photo_content_url' =>  $new_id . '.' . $is_upload['extension'],
                'photo_content_status' => 1
            ])){
                return $this->fail('Failed photo content save');
            }

            // Insert Caption Info into content_captions table
            if($content_caption->it == null) {
                return $this->fail('Could not find Caption Data(it)');
            }
            if($content_caption->en == null) {
                return $this->fail('Could not find Caption Data(en)');
            }
            $content_caption_model = new ContentCaptionModel();
            $caption_data_it = [
                'content_caption_host_id'       => $host_id,
                'content_caption_type'          => 1,
                'content_caption_connection_id' => $new_id,
                'content_caption'               => $content_caption->it,
                'content_caption_lang'          => 'it',
                'content_caption_status'        => 1,
            ];
            if(!$content_caption_model->insert($caption_data_it)) {
                return $this->fail('Failed Caption Data(it) insert');
            }
            $caption_data_en = [
                'content_caption_host_id'       => $host_id,
                'content_caption_type'          => 1,
                'content_caption_connection_id' => $new_id,
                'content_caption'               => $content_caption->en,
                'content_caption_lang'          => 'en',
                'content_caption_status'        => 1,
            ];
            if(!$content_caption_model->insert($caption_data_en)) {
                return $this->fail('Failed Caption Data(en) insert');
            }
            $data = [
                'photo_id'  => $new_id
            ];
            return $this->respondCreated($data, 'Data saved');
        }
        return $this->fail('Could not find new id');
    }

    /**
     * Delete photo content
     * DELETE: /photos/delete
     * @return mixed
     */
    public function delete($photo_content_id = null)
    {
        $host_id = $this->get_host_id();
        if($photo_content_id == null) {
            return $this->fail('Could Not Find Such ID');
        }
        $photo_content_model = new PhotoContentModel();
        $check_id_exist = $photo_content_model->is_existed_id($photo_content_id);
        if($check_id_exist == null) {
            return $this->failNotFound('No Such Data');
        }
        if ($photo_content_model->delete($photo_content_id)) {
            $content_caption_model = new ContentCaptionModel();
            $content_caption_model->delete_by($host_id, 1, $photo_content_id);
            return $this->respond([
                'success' => 'id:' . $photo_content_id . ' Successfully Deleted'
            ]);
        }
        return $this->fail('Failed Deleted');
    }

    public function uploadImage($image_url, $photo_content_id, $host_id)
    {
        $url_to_image=$image_url;

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos'))
        {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos');
        }

        $my_save_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos/';
        $basename = basename($url_to_image);
        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        $available_ext = ['jpg', 'webp', 'png'];

        if (!in_array($ext, $available_ext)) {
            return null;
        }
        $suffix_filename = $photo_content_id . '.' . $ext;
        $filename = '0_' . $suffix_filename;
        $complete_save_loc = $my_save_dir.$filename;
        $upload = file_put_contents($complete_save_loc,file_get_contents($url_to_image));
        if($upload) {
            \Config\Services::image()
                ->withFile($url_to_image)
                ->resize(1024, 540, true, 'height')
                ->save($my_save_dir . '1_' . $suffix_filename);

            \Config\Services::image()
                ->withFile($url_to_image)
                ->resize(330, 174, true, 'height')
                ->save($my_save_dir . '2_' . $suffix_filename);

            return [
                'extension'  => $ext,
            ];
        }
        return null;
    }
}
