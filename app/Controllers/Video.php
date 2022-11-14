<?php

namespace App\Controllers;

use App\Models\ContentCaptionModel;
use App\Models\VideoContentModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Video extends ResourceController
{
	/**
	* Return an array of photo content
	 * GET/videos
	 * @return mixed
	 */
	use ResponseTrait;
	public function index()
	{
        $videos = new VideoContentModel();
        $host_id = $this->get_host_id();
       /* Getting photo for level1, level2 from PhotoContentModel */
        $L1_type_videos = $videos->get_level1_video($host_id);
        $L2_type_videos = $videos->get_level2_video($host_id);

        return $this->respond([
            'l1_type_videos' => $L1_type_videos == null ? [] : $L1_type_videos,
            'l2_type_videos' => $L2_type_videos == null ? [] : $L2_type_videos,
        ], 200);
	}

    /**
     * Create a new video content
     * POST: /videos/add
     * @return mixed
     */
    public function create()
    {
        //Video content
        $response = [];
        if (! $this->validate([
            'video_content_level' => 'required|min_length[1]|max_length[1]',
            'video_content_channel' => 'required',
            'video_content_code'    => 'required',
            //'video_url' => 'required'
        ])) {
            return $this->fail('Input Data format is incorrect.');
        }
        $host_id = $this->get_host_id();
        $video_content_level = $this->request->getVar('video_content_level');
        $video_content_channel = $this->request->getVar('video_content_channel');
        $video_order = $this->request->getVar('video_order') ? $this->request->getVar('video_order') : 0;
        $video_content_connection = $video_content_level == 2 ? $this->request->getVar('video_content_connection') : '';
        $video_content_code = $this->request->getVar('video_content_code');
        $content_caption = $this->request->getVar('content_caption');
        // $video_url = $this->request->getVar('img_url');

        // Insert video content
        $video_content_model = new VideoContentModel();
        $data = [
            'video_content_host_id'     => $host_id,
            'video_content_level'       => $video_content_level,
            'video_content_channel'     => $video_content_channel,
            'video_content_connection'  => $video_content_connection,
            'video_content_code'       => $video_content_code,
            'photo_content_order'       => $video_order,
            'video_content_status'      => 1,
        ];

        $new_id = $video_content_model->insert($data);
        if($new_id) {
            if($content_caption->it == null) {
                return $this->fail('Could not find Caption Data(it)');
            }
            if($content_caption->en == null) {
                return $this->fail('Could not find Caption Data(en)');
            }
            $content_caption_model = new ContentCaptionModel();
            $caption_data_it = [
                'content_caption_host_id'       => $host_id,
                'content_caption_type'          => 2,
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
                'content_caption_type'          => 2,
                'content_caption_connection_id' => $new_id,
                'content_caption'               => $content_caption->en,
                'content_caption_lang'          => 'en',
                'content_caption_status'        => 1,
            ];
            if(!$content_caption_model->insert($caption_data_en)) {
                return $this->fail('Failed Caption Data(en) insert');
            }
            $data = [
                'video_id'  => $new_id
            ];
            return $this->respondCreated($data, 'Data saved');
        }
        return $this->fail('Could not find new id');
    }

    /**
     * Delete video content
     * DELETE /videos/delete
     * @return mixed
     */
    public function delete($video_content_id = null)
    {
        $host_id = $this->get_host_id();
        if($video_content_id == null) {
            return $this->fail('Could Not Find Such ID');
        }
        $video_content_model = new VideoContentModel();
        $check_id_exist = $video_content_model->is_existed_id($video_content_id);
        if($check_id_exist == null) {
            return $this->failNotFound('No Such Data');
        }
        if ($video_content_model->delete($video_content_id)) {
            $content_caption_model = new ContentCaptionModel();
            $content_caption_model->delete_by($host_id, 2, $video_content_id);
            return $this->respond([
               'success' =>  'id:' . $video_content_id . ' Successfully Deleted'
            ]);
        }
        return $this->fail('Failed Deleted');
    }
}
