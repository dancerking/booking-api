<?php

namespace App\Controllers;

use App\Models\ContentCaptionModel;
use App\Models\PhotoContentModel;
use App\Controllers\APIBaseController;
use App\Models\LanguageModel;
use CodeIgniter\API\ResponseTrait;

class Photo extends APIBaseController
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
            'img_url' => 'required',
        ])) {
            return $this->notifyError('Input data format is incorrect', 'invalid_data', 'photo');
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
            // Validation for download image size
            if (!@getimagesize($img_url)) {
                return $this->notifyError('No Such Image URL', 'notFound', 'photo');
            }
            $image_width = getimagesize($img_url)[0];
            $image_height = getimagesize($img_url)[1];
            $config = config('Config\App');
            $valid_image_size = $config->minimum_download_image_size;
            if(!($image_width >= $valid_image_size[0] && $image_height >= $valid_image_size[1])) {
                $photo_content_model->delete($new_id);
                return $this->notifyError('Minimum photo size is 2048*1080', 'invalid_data', 'photo');
            }
            $is_upload = $this->uploadImage($img_url, $new_id, $host_id);
            if($is_upload == null) {
                $photo_content_model->delete($new_id);
                return $this->notifyError(lang('Photo.failedUpload'),'invalid_data', 'photo');
            }
            if(!$photo_content_model->update($new_id, [
                'photo_content_url' =>  $new_id . '.' . $is_upload['extension'],
                'photo_content_status' => 1,
            ])){
                $photo_content_model->delete($new_id);
                return $this->notifyError(lang('Photo.failedSave'), 'invalid_data', 'photo');
            }

            // Insert Caption Info into content_captions table
            $content_caption_model = new ContentCaptionModel();
            $language_model = new LanguageModel();
            $languages = $language_model->get_available_languages(1);
            if($languages != null) {
                foreach($languages as $language) {
                    $language_code = $language->language_code;
                    if(isset($content_caption->$language_code) && $content_caption->$language_code != null) {
                        $caption_data = [
                            'content_caption_host_id'       => $host_id,
                            'content_caption_type'          => 1,
                            'content_caption_connection_id' => $new_id,
                            'content_caption'               => $content_caption->$language_code,
                            'content_caption_lang'          => $language_code,
                            'content_caption_status'        => 1,
                        ];
                        if(!$content_caption_model->insert($caption_data)) {
                            $photo_content_model->delete($new_id);
                            return $this->notifyError('Failed content caption data insert', 'failed_create', 'photo');
                        }
                    }
                }
            }
            return $this->respond([
                "id" => $new_id,
                'message' => 'Successfully created'
            ]);
        }
        return $this->notifyError('Failed create', 'failed_create', 'photo');
    }

    /**
     * Delete photo content
     * DELETE: /photos/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        $host_id = $this->get_host_id();
        if (! $this->validate([
            'photo_content_id' => 'required',
        ])) {
            return $this->notifyError('Input data format is incorrect', 'invalid_data', 'photo');
        }
        $photo_content_id = $this->request->getVar('photo_content_id');
        if(!ctype_digit((string)$photo_content_id)) {
            return $this->notifyError('Input data format is incorrect', 'invalid_data', 'photo');
        }
        $photo_content_model = new PhotoContentModel();
        $check_id_exist = $photo_content_model->is_existed_id($photo_content_id);
        if($check_id_exist == null) {
            return $this->notifyError('No Such Data', 'notFound', 'photo');
        }
        $photo_content_filename = $photo_content_model->find($photo_content_id);

        if ($photo_content_model->delete($photo_content_id)) {
            $content_caption_model = new ContentCaptionModel();
            $content_caption_model->delete_by($host_id, 1, $photo_content_id);
            if($photo_content_filename['photo_content_url'] != '') {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos/0_' . $photo_content_filename['photo_content_url']);
                unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos/1_' . $photo_content_filename['photo_content_url']);
                unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos/2_' . $photo_content_filename['photo_content_url']);
            }
            return $this->respond([
                'id' => $photo_content_id,
                'success' => 'Successfully Deleted'
            ]);
        }
        return $this->notifyError('Failed Delete', 'failed_delete', 'photo');
    }

    public function uploadImage($image_url, $photo_content_id, $host_id)
    {
        $url_to_image = $image_url;

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos'))
        {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/');
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos');
        }

        $my_save_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $host_id . '/photos/';
        $basename = basename($url_to_image);
        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        $available_ext = ['jpg', 'webp'];

        if (!in_array($ext, $available_ext)) {
            return null;
        }
        $suffix_filename = $photo_content_id . '.' . $ext;
        $filename = '0_' . $suffix_filename;
        $complete_save_loc = $my_save_dir.$filename;
        $upload = file_put_contents($complete_save_loc,file_get_contents($url_to_image));
        if($upload) {
            $config = config('Config\App');
            $custom_photo1 = $config->Custom_photo1;
            $custom_photo2 = $config->Custom_photo2;
            \Config\Services::image()
                ->withFile($complete_save_loc)
                ->fit($custom_photo1[0], $custom_photo1[1], 'center')
                ->save($my_save_dir . '1_' . $suffix_filename);

            \Config\Services::image()
                ->withFile($complete_save_loc)
                ->fit($custom_photo2[0], $custom_photo2[1], 'center')
                ->save($my_save_dir . '2_' . $suffix_filename);

            return [
                'extension'  => $ext,
            ];
        }
        return null;
    }

}
