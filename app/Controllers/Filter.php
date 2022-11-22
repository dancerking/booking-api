<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\FilterMappingModel;
use App\Models\FilterModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class Filter extends APIBaseController
{
    /**
     * Insert Filter mapping
     * POST/filters
     * @return mixed
     */
    use ResponseTrait;
    public function map()
    {
        /* Load Rate Model */
        $filter_mapping_model = new FilterMappingModel();
        $filter_model = new FilterModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validation request data */
        if (
            !$this->validate([
                'filter_mapping_level' => 'required',
                'filter_mapping_type' => 'required',
                'filter_code' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'filter'
            );
        }

        /* Getting request data */
        $filter_mapping_level = $this->request->getVar(
            'filter_mapping_level'
        );
        $filter_mapping_type = $this->request->getVar(
            'filter_mapping_type'
        );
        $filter_code = $this->request->getVar(
            'filter_code'
        );
        if (
            $filter_mapping_level != 1 &&
            $filter_mapping_level != 2
        ) {
            return $this->notifyError(
                'filter_mapping_level must be 1 or 2',
                'invalid_data',
                'filter'
            );
        }
        $filter_codes = $filter_model
            ->where([
                'filter_level' => $filter_mapping_level,
            ])
            ->select('filter_code')
            ->findAll();
        $filter_codes_array = [];
        foreach ($filter_codes as $code) {
            array_push(
                $filter_codes_array,
                $code['filter_code']
            );
        }
        if (!in_array($filter_code, $filter_codes_array)) {
            return $this->notifyError(
                'Filter code is invalid code.',
                'invalid_data',
                'filter'
            );
        }
        /* Insert filter mapping ids in FilterMapping Model */
        if (
            $filter_mapping_level == 1 &&
            $filter_mapping_type != 0
        ) {
            return $this->notifyError(
                'When filter_mapping_level is 1, filter_mapping_type must be 0.',
                'invalid_data',
                'filter'
            );
        }
        if ($filter_mapping_level == 2) {
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_code' => $filter_mapping_type,
                    ])
                    ->findAll() == null
            ) {
                return $this->notifyError(
                    'No Such type_mapping_code',
                    'invalid_data',
                    'filter'
                );
            }
        }
        if (
            $filter_mapping_model
                ->where([
                    'filter_mapping_host_id' => $host_id,
                    'filter_mapping_code' => $filter_code,
                    'filter_mapping_level' => $filter_mapping_level,
                    'filter_mapping_type' => $filter_mapping_type,
                ])
                ->findAll() != null
        ) {
            return $this->notifyError(
                'Duplication error.',
                'duplicate',
                'filter'
            );
        }
        $new_id = $filter_mapping_model->insert([
            'filter_mapping_host_id' => $host_id,
            'filter_mapping_code' => $filter_code,
            'filter_mapping_level' => $filter_mapping_level,
            'filter_mapping_type' => $filter_mapping_type,
            'filter_mapping_status' => 1,
        ]);
        if (!$new_id) {
            return $this->notifyError(
                'Failed create',
                'failed_create',
                'filter'
            );
        }

        return $this->respond([
            'filters_mapping_id' => $new_id,
            'message' => 'Successfully created.',
        ]);
    }

    /**
     * Delete Filter mapping content
     * DELETE /filters/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        if (
            !$this->validate([
                'filter_mapping_id' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'filter'
            );
        }
        $filter_mapping_id = $this->request->getVar(
            'filter_mapping_id'
        );
        $filter_mapping_model = new FilterMappingModel();
        $check_id_exist = $filter_mapping_model->is_existed_id(
            $filter_mapping_id
        );
        if ($check_id_exist == null) {
            return $this->notifyError(
                'No Such Data',
                'notFound',
                'filter'
            );
        }
        if (
            !$filter_mapping_model->delete(
                $filter_mapping_id
            )
        ) {
            return $this->notifyError(
                'Failed delete',
                'failed_delete',
                'filter'
            );
        }
        return $this->respond([
            'id' => $filter_mapping_id,
            'message' => 'Successfully deleted.',
        ]);
    }
}
