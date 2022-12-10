<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\ContentCaptionModel;
use App\Models\LanguageModel;
use App\Models\VideoContentModel;
use CodeIgniter\API\ResponseTrait;

class Video extends APIBaseController
{
    /**
     * Return an array of video content
     * GET/videos
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');
        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Load necessary Model */
        $videos = new VideoContentModel();

        /* Getting video for level1, level2 from VideoContentModel */
        $L1_type_videos = $videos->get_level1_video(
            $host_id,
            $config->LIMIT_FOR_L1_TYPE_video
        );
        $L2_type_videos = $videos->get_level2_video(
            $host_id,
            $config->LIMIT_FOR_L2_TYPE_video
        );

        return parent::respond(
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
        $config = config('Config\App');
        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Load necessary Model */
        $video_content_model = new VideoContentModel();
        $content_caption_model = new ContentCaptionModel();
        $language_model = new LanguageModel();

        /* Validate */
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

        /* Getting request data */
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

        /* Validation for data format */
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

        if (
            $video_content_level < 1 ||
            $video_content_level > 2
        ) {
            return $this->notifyError(
                'video_content_level should be 1 or 2.',
                'invalid_data',
                'video'
            );
        }

        if (
            in_array(
                $video_content_code,
                $config->INVALID_VIDEO_CODE_STRING
            )
        ) {
            return $this->notifyError(
                'video_content_code format is invalid',
                'invalid_data',
                'video'
            );
        }
        // Check if duplicated
        if (
            $video_content_model
                ->where([
                    'video_content_host_id' => $host_id,
                    'video_content_level' => $video_content_level,
                    'video_content_channel' => $video_content_channel,
                    'video_content_connection' => $video_content_connection,
                    'video_content_code' => $video_content_code,
                ])
                ->findAll() != null
        ) {
            return $this->notifyError(
                'Duplication error',
                'duplicate',
                'video'
            );
        }

        // Insert data
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
            return parent::respond([
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
        /* Import config variable */
        $config = config('Config\App');

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Load necessary Model */
        $video_content_model = new VideoContentModel();
        $content_caption_model = new ContentCaptionModel();

        /* Validate */
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

        /* Getting request data */
        $video_content_id = $this->request->getVar(
            'video_content_id'
        );

        /* Validation for data format */
        if (!ctype_digit((string) $video_content_id)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'video'
            );
        }

        /* Check if id exists */
        if (
            $video_content_model->find($video_content_id) ==
            null
        ) {
            return $this->notifyError(
                'No Such Data',
                'notFound',
                'video'
            );
        }

        /* Delete video content */
        if (
            !$video_content_model->delete($video_content_id)
        ) {
            return $this->notifyError(
                'Failed Delete',
                'failed_delete',
                'video'
            );
        }
        if (
            !$content_caption_model->delete_by(
                $host_id,
                $config->CONTENT_CAPTION_TYPE['video'],
                $video_content_id
            )
        ) {
            return $this->notifyError(
                'Failed delete',
                'failed_delete',
                'video'
            );
        }
        return parent::respond([
            'id' => $video_content_id,
            'message' => 'Successfully Deleted',
        ]);
    }
}
