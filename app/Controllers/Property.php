<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\HostModel;
use App\Models\PropertyModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class Property extends APIBaseController
{
    /**
     * Return an array of objects
     * GET/properties
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
        $property_model = new PropertyModel();

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
                'property'
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
                    'property'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'property'
            );
        }
        /* Getting data from db*/
        $property_data = $property_model
            ->select(
                'property_id, property_name, property_type'
            )
            ->where('property_host_id', $host_id)
            ->findAll();
        return $this->respond([
            'property_data' =>
                $property_data == null
                    ? []
                    : $property_data,
        ]);
    }

    /**
     * Update
     * PUT/properties/update
     * @return mixed
     */
    public function update($id = '')
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $property_model = new PropertyModel();
        $type_mapping_model = new TypeMappingModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'property_name' => 'required',
                'property_type' => 'required',
                'property_status' =>
                    'required|regex_match[/[01234]/]',
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
                'property'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $property_id = $this->request->getVar(
            'property_id'
        );
        $property_name = $this->request->getVar(
            'property_name'
        );
        $property_type = $this->request->getVar(
            'property_type'
        );
        $property_status = $this->request->getVar(
            'property_status'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'property'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'property'
            );
        }
        if (
            $property_id != null &&
            !ctype_digit((string) $property_id)
        ) {
            return $this->notifyError(
                'property_id should be integer.',
                'invalid_data',
                'property'
            );
        }
        if (
            $property_id != null &&
            $property_model->find($property_id) == null
        ) {
            return $this->notifyError(
                'No Such property_id',
                'invalid_data',
                'property'
            );
        }
        if (
            $type_mapping_model
                ->where([
                    'type_mapping_host_id' => $host_id,
                    'type_mapping_code' => $property_type,
                ])
                ->first() == null
        ) {
            return $this->notifyError(
                'No Such mapped type code',
                'notFound',
                'property'
            );
        }
        /* update */
        $data = [
            'property_host_id' => $host_id,
            'property_name' => $property_name,
            'property_type' => $property_type,
            'property_status' => $property_status,
        ];
        $new_id = '';
        if ($property_id == null) {
            if (
                $property_model
                    ->where([
                        'property_host_id' => $host_id,
                        'property_name' => $property_name,
                        'property_type' => $property_type,
                    ])
                    ->first() != null
            ) {
                return $this->notifyError(
                    'Duplication error',
                    'duplicate',
                    'property'
                );
            }
            $new_id = $property_model->insert($data);
            if (!$new_id) {
                return $this->notifyError(
                    'Failed create',
                    'failed_crate',
                    'property'
                );
            }
        } else {
            if (
                !$property_model->update(
                    $property_id,
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'property'
                );
            }
        }
        return $this->respond([
            'id' =>
                $property_id == null
                    ? $new_id
                    : $property_id,
            'message' =>
                'Successfully ' .
                ($property_id == null
                    ? 'created'
                    : 'updated'),
        ]);
    }
}
