<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\CityModel;
use App\Models\GuestTypeMappingModel;
use App\Models\GuestTypeModel;
use App\Models\HostAgreementModel;
use App\Models\HostLangModel;
use App\Models\HostModel;
use App\Models\LanguageModel;
use App\Models\TypeMainModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class GuestTypes extends APIBaseController
{
    /**
     * Return an array of objects
     * GET/guesttypes
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $guest_type_model = new GuestTypeModel();

        /* Validate */
        if (
            !$this->validate([
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
                'guest_types'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'guest_types'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'guest_types'
            );
        }
        /* Getting data from db*/
        $guest_types = $guest_type_model->get_guest_types_with_mapping(
            $host_id
        );

        return parent::respond([
            'guest_types' =>
                $guest_types == null ? [] : $guest_types,
        ]);
    }

    /**
     * Update
     * PUT/guesttypes/update
     * @return mixed
     */
    public function update($id = '')
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $guest_type_model = new GuestTypeModel();
        $guest_type_mapping_model = new GuestTypeMappingModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'guest_type_code' => 'required',
                'guest_type_age_from' => 'required|integer',
                'guest_type_age_to' => 'required|integer',
                'guest_type_status' =>
                    'required|integer|regex_match[/[0123]/]',
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
                'guest_types'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $guest_type_code = $this->request->getVar(
            'guest_type_code'
        );
        $guest_type_age_from = $this->request->getVar(
            'guest_type_age_from'
        );
        $guest_type_age_to = $this->request->getVar(
            'guest_type_age_to'
        );
        $guest_type_status = $this->request->getVar(
            'guest_type_status'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'guest_types'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'guest_types'
            );
        }
        if ($guest_type_age_from > $guest_type_age_to) {
            return $this->notifyError(
                'age_to should be greater than age_from',
                'invalid_data',
                'guest_types'
            );
        }
        if (
            $guest_type_model
                ->where('guest_type_code', $guest_type_code)
                ->first() == null
        ) {
            return $this->notifyError(
                'No Such guest_type_code',
                'notFound',
                'guest_types'
            );
        }
        /* update */
        $data = [
            'guest_type_host_id' => $host_id,
            'guest_type_code' => $guest_type_code,
            'guest_type_age_from' => $guest_type_age_from,
            'guest_type_age_to' => $guest_type_age_to,
            'guest_type_status' => $guest_type_status,
        ];
        $new_id = '';
        $mapping_data = $guest_type_mapping_model
            ->where([
                'guest_type_host_id' => $host_id,
                'guest_type_code' => $guest_type_code,
            ])
            ->first();
        if ($mapping_data != null) {
            if (
                !$guest_type_mapping_model->update(
                    $mapping_data['guest_type_id'],
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'guest_types'
                );
            }
        } else {
            $new_id = $guest_type_mapping_model->insert(
                $data
            );
            if (!$new_id) {
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'guest_types'
                );
            }
        }

        return parent::respond([
            'id' =>
                $mapping_data == null
                    ? $new_id
                    : $mapping_data['guest_type_id'],
            'message' =>
                'Successfully ' .
                ($mapping_data == null
                    ? 'created'
                    : 'updated'),
        ]);
    }
}
