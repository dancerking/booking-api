<?php

namespace App\Controllers;

use App\Models\ContentCaptionModel;
use App\Models\VideoContentModel;
use App\Controllers\APIBaseController;
use App\Models\LanguageModel;
use CodeIgniter\API\ResponseTrait;

class Video extends APIBaseController
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
        $L1_type_videos = $videos->get_level1_video(
            $host_id
        );
        $L2_type_videos = $videos->get_level2_video(
            $host_id
        );

        return $this->respond(
            [
                'l1_type_videos' =>
                    $L1_type_videos == null
                        ? []
                        : $L1_type_videos,
                'l2_type_videos' =>
                    $L2_type_videos == null
                        ? []
                        : $L2_type_videos,
            ],
            200
        );
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
        if (
            !$this->validate([
                'video_content_level' =>
                    'required|min_length[1]|max_length[1]',
                'video_content_channel' => 'required',
                'video_content_code' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'video'
            );
        }
        $host_id = $this->get_host_id();
        $video_content_level = $this->request->getVar(
            'video_content_level'
        );
        $video_content_channel = $this->request->getVar(
            'video_content_channel'
        );
        $video_order = $this->request->getVar('video_order')
            ? $this->request->getVar('video_order')
            : 0;
        $video_content_connection =
            $video_content_level == 2
                ? $this->request->getVar(
                    'video_content_connection'
                )
                : '';
        $video_content_code = $this->request->getVar(
            'video_content_code'
        );
        $content_caption = $this->request->getVar(
            'content_caption'
        );

        // Validation
        if (
            $video_content_level == 2 &&
            $video_content_connection == ''
        ) {
            return $this->notifyError(
                'video_content_connection is required',
                'invalid_data',
                'video'
            );
        }

        // Insert video content
        $video_content_model = new VideoContentModel();
        $data = [
            'video_content_host_id' => $host_id,
            'video_content_level' => $video_content_level,
            'video_content_channel' => $video_content_channel,
            'video_content_connection' => $video_content_connection,
            'video_content_code' => $video_content_code,
            'photo_content_order' => $video_order,
            'video_content_status' => 1,
        ];

        $new_id = $video_content_model->insert($data);
        if ($new_id) {
            // Insert Caption Info into content_captions table
            $content_caption_model = new ContentCaptionModel();
            $language_model = new LanguageModel();
            $languages = $language_model->get_available_languages(
                1
            );
            if ($languages != null) {
                foreach ($languages as $language) {
                    $language_code =
                        $language->language_code;
                    if (
                        isset(
                            $content_caption->$language_code
                        ) &&
                        $content_caption->$language_code !=
                            null
                    ) {
                        $caption_data = [
                            'content_caption_host_id' => $host_id,
                            'content_caption_type' => 2,
                            'content_caption_connection_id' => $new_id,
                            'content_caption' =>
                                $content_caption->$language_code,
                            'content_caption_lang' => $language_code,
                            'content_caption_status' => 1,
                        ];
                        if (
                            !$content_caption_model->insert(
                                $caption_data
                            )
                        ) {
                            $video_content_model->delete(
                                $new_id
                            );
                            return $this->notifyError(
                                'Failed content caption data insert',
                                'failed_create',
                                'photo'
                            );
                        }
                    }
                }
            }
            return $this->respond([
                'id' => $new_id,
                'message' => 'Successfully created',
            ]);
        }
        return $this->notifyError(
            'Failed create',
            'failed_create',
            'video'
        );
    }

    /**
     * Delete video content
     * DELETE /videos/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        $host_id = $this->get_host_id();
        if (
            !$this->validate([
                'video_content_id' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'video'
            );
        }
        $video_content_id = $this->request->getVar(
            'video_content_id'
        );
        if (!ctype_digit((string) $video_content_id)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'video'
            );
        }
        $video_content_model = new VideoContentModel();
        $check_id_exist = $video_content_model->is_existed_id(
            $video_content_id
        );
        if ($check_id_exist == null) {
            return $this->notifyError(
                'No Such Data',
                'notFound',
                'video'
            );
        }
        if (
            $video_content_model->delete($video_content_id)
        ) {
            $content_caption_model = new ContentCaptionModel();
            $content_caption_model->delete_by(
                $host_id,
                2,
                $video_content_id
            );
            return $this->respond([
                'id' => $video_content_id,
                'success' => 'Successfully Deleted',
            ]);
        }
        return $this->notifyError(
            'Failed Delete',
            'failed_delete',
            'video'
        );
    }
}
