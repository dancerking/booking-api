<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\HostModel;
use App\Models\LanguageModel;
use App\Models\PhotoContentModel;
use App\Models\TypeMappingModel;
use App\Models\VideoContentModel;
use CodeIgniter\API\ResponseTrait;

class PropertyType extends APIBaseController
{
    /**
     * Return an array of photo content and video content
     * GET/propertytype
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();
        // Load necessary Model
        $photo_content_model = new PhotoContentModel();
        $video_content_model = new VideoContentModel();
        $type_mapping_model = new TypeMappingModel();
        $lang_model = new LanguageModel();
        $host_model = new HostModel();

        /* Validate */
        if (
            !$this->validate([
                'type_code' => 'required',
                'host_id' => 'required|integer',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'property_type'
            );
        }

        /* Getting request data */
        $type_code = $this->request->getVar('type_code');
        $host_id = $this->request->getVar('host_id');

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'property_type'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such host_id',
                'notFound',
                'property_type'
            );
        }
        if (
            $type_mapping_model
                ->where('type_mapping_code', $type_code)
                ->first() == null
        ) {
            return $this->notifyError(
                'No Such type_code(type_mapping_code)',
                'notFound',
                'property_type'
            );
        }
        /* Get data */
        $photo_content = $photo_content_model
            ->where([
                'photo_content_connection' => $type_code,
                'photo_content_host_id' => $host_id,
            ])
            ->findAll();
        $video_content = $video_content_model
            ->where([
                'video_content_connection' => $type_code,
                'video_content_host_id' => $host_id,
            ])
            ->findAll();
        $lang_content = $type_mapping_model
            ->where([
                'type_mapping_host_id' => $host_id,
                'type_mapping_code' => $type_code,
            ])
            ->findAll();
        return parent::respond(
            [
                'photo_content' =>
                    $photo_content == null
                        ? []
                        : $photo_content,
                'video_content' =>
                    $video_content == null
                        ? []
                        : $video_content,
                'lang_content' =>
                    $lang_content == null
                        ? []
                        : $lang_content,
            ],
            200
        );
    }
}
