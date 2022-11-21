<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\FilterMappingModel;
use App\Models\FilterModel;
use CodeIgniter\API\ResponseTrait;

class Filter extends APIBaseController
{
    /**
     * Return an array of Filter mapping
     * GET/filters
     * @return mixed
     */
    use ResponseTrait;
    public function map()
    {
        /* Load Rate Model */
        $filter_mapping_model = new FilterMappingModel();
        $filter_model = new FilterModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validation request data */
        if (
            !$this->validate([
                'filter_mapping_level' => 'required',
                'filter_mapping_type' => 'required',
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

        /* Getting filter mapping ids from FilterMapping Model */
        $filter_mapping_ids = $filter_mapping_model->get_available_ids(
            $host_id,
            $filter_mapping_type,
            $filter_mapping_level
        );

        return $this->respond([
            'filters_mapping_id' =>
                $filter_mapping_ids == null
                    ? []
                    : $filter_mapping_ids,
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
