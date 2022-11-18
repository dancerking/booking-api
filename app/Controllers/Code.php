<?php

namespace App\Controllers;

use App\Models\FilterMappingModel;
use App\Models\FilterModel;
use App\Models\GuestTypeModel;
use App\Models\HostModel;
use App\Models\LanguageModel;
use App\Models\PhotoContentModel;
use App\Models\TypeMainModel;
use App\Models\TypeMappingModel;
use App\Models\VideoChannelModel;
use App\Controllers\APIBaseController;
use CodeIgniter\API\ResponseTrait;
class Code extends APIBaseController
{
	/**
	 * Return an array of objects(The select WHERE condition to obtain data for this call  is host_ID (received from the JWT Token) AND Except for the host table, for every other table, the record status = 1 (it means we take only data with Active status))
	 * GET/codes
	 * @return mixed
	 */
	use ResponseTrait;
	public function index()
	{
        // Get host_id from JWT token
		$host_id = $this->get_host_id();

        // Load models
        $host_model = new HostModel();
        $language_model = new LanguageModel();
        $types_main_model = new TypeMainModel();
        $types_mapping_model = new TypeMappingModel();
        $guest_type_model = new GuestTypeModel();
        $filter_model = new FilterModel();
        $filter_mapping_model = new FilterMappingModel();
        $photo_content_model = new PhotoContentModel();
        $video_channel_model = new VideoChannelModel();

        /* Validate */
        if (! $this->validate([
            'record_status' => 'required',
        ])) {
            return $this->notifyError('Input data format is incorrect.', 'invalid_data', 'code');
        }

        // Get data
        $record_status = $this->request->getVar('record_status');
        $main_host_data = $host_model->get_host_data($host_id);
        $available_languages = $language_model->get_available_languages($record_status);
        $available_main_types = $types_main_model->get_type_main($record_status);
        $mapped_host_types = $types_mapping_model->get_mapping_types($host_id, $record_status);
        $available_guest_types = $guest_type_model->get_guest_types($record_status);
        $filters = $filter_model->get_filters($record_status);
        $mapped_filters = $filter_mapping_model->get_mapped_filters($host_id, $record_status);
        $photo_contents = $photo_content_model->get_photo_contents($host_id, $record_status);
        $channel_video_codes = $video_channel_model->get_video_channel($host_id, $record_status);

        return $this->respond([
            'main_host_data' => $main_host_data == null ? [] : $main_host_data,
            'available_languages' => $available_languages == null ? [] : $available_languages,
            'available_main_types' => $available_main_types == null ? [] : $available_main_types,
            'mapped_host_types' => $mapped_host_types == null ? [] : $mapped_host_types,
            'available_guest_types' => $available_guest_types == null? [] : $available_guest_types,
            'filters' => $filters == null ? [] : $filters,
            'mapped_filters' => $mapped_filters == null ? [] : $mapped_filters,
            'photo_contents' => $photo_contents == null ? [] : $photo_contents,
            'channel_video_codes' => $channel_video_codes == null ? [] : $channel_video_codes
        ]);
	}
}
