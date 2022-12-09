<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\FilterMappingModel;
use App\Models\FilterModel;
use App\Models\HostModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class FiltersMapping extends APIBaseController
{
    /**
     * return array of filters with comping level or type_level
     * GET/filtersmapping
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
        $filter_mapping_model = new FilterMappingModel();
        $filter_model = new FilterModel();

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
                'filters_mapping'
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
                    'filters_mapping'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such host_id',
                'notFound',
                'filters_mapping'
            );
        }
        /* Getting data from db*/
        $filters = $filter_mapping_model->get_filters(
            $host_id
        );
        return $this->respond([
            'filters' => $filters == null ? [] : $filters,
        ]);
    }
    /**
     * Update
     * PUT/filtersmapping/update
     * @return mixed
     */
    public function update($id = '')
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $filter_mapping_model = new FilterMappingModel();
        $filter_model = new FilterModel();
        $type_mapping_model = new TypeMappingModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'filter_mapping_code' => 'required',
                'filter_mapping_level' =>
                    'required|regex_match[/[12]/]',
                'filter_mapping_type' => 'required',
                'filter_mapping_status' =>
                    'required|regex_match[/[0123]/]',
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
                'filters_mapping'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $filter_mapping_id = $this->request->getVar(
            'filter_mapping_id'
        );
        $filter_mapping_code = $this->request->getVar(
            'filter_mapping_code'
        );
        $filter_mapping_level = $this->request->getVar(
            'filter_mapping_level'
        );
        $filter_mapping_type = $this->request->getVar(
            'filter_mapping_type'
        );
        $filter_mapping_status = $this->request->getVar(
            'filter_mapping_status'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'filters_mapping'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'filters_mapping'
            );
        }
        if (
            $filter_mapping_id != null &&
            !ctype_digit((string) $filter_mapping_id)
        ) {
            return $this->notifyError(
                'filter_mapping_id should be integer',
                'invalid_data',
                'filters_mapping'
            );
        }
        if (
            $filter_mapping_id != null &&
            $filter_mapping_model->find(
                $filter_mapping_id
            ) == null
        ) {
            return $this->notifyError(
                'No Such filter_mapping_id',
                'notFound',
                'filters_mapping'
            );
        }
        if (
            $filter_model
                ->where('filter_code', $filter_mapping_code)
                ->first() == null
        ) {
            return $this->notifyError(
                'No Such filter_mapping_code(filter_code)',
                'notFound',
                'filters_mapping'
            );
        }
        if (
            $filter_mapping_level == 1 &&
            $filter_mapping_type != 0
        ) {
            return $this->notifyError(
                'Once filter_mapping_level is 1, filter_mapping_type should be 0.',
                'invalid_data',
                'filters_mapping'
            );
        }
        if (
            $filter_mapping_level == 2 &&
            $type_mapping_model->where([
                'type_mapping_host_id' => $host_id,
                'type_mapping_code' => $filter_mapping_type,
            ]) == null
        ) {
            return $this->notifyError(
                'No Such mapping_type(type_mapping_code)',
                'invalid_data',
                'filters_mapping'
            );
        }
        /* update */
        $data = [
            'filter_mapping_host_id' => $host_id,
            'filter_mapping_code' => $filter_mapping_code,
            'filter_mapping_level' => $filter_mapping_level,
            'filter_mapping_type' => $filter_mapping_type,
            'filter_mapping_status' => $filter_mapping_status,
        ];
        $new_id = '';
        if ($filter_mapping_id != null) {
            if (
                !$filter_mapping_model->update(
                    $filter_mapping_id,
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'filters_mapping'
                );
            }
        } else {
            if (
                $filter_mapping_model
                    ->where([
                        'filter_mapping_host_id' => $host_id,
                        'filter_mapping_code' => $filter_mapping_code,
                        'filter_mapping_level' => $filter_mapping_level,
                    ])
                    ->first() != null
            ) {
                return $this->notifyError(
                    'Duplication error',
                    'duplicate',
                    'filters_mapping'
                );
            }
            $new_id = $filter_mapping_model->insert($data);
            if (!$new_id) {
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'filters_mapping'
                );
            }
        }
        return $this->respond([
            'id' =>
                $filter_mapping_id == null
                    ? $new_id
                    : $filter_mapping_id,
            'message' =>
                'Successfully ' .
                ($filter_mapping_id == null
                    ? 'created'
                    : 'updated'),
        ]);
    }
}
