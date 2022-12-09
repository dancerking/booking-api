<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\HostModel;
use App\Models\PropertyCalendarModel;
use App\Models\PropertyModel;
use App\Models\TriggerCronModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class PropertyCalendar extends APIBaseController
{
    /**
     * Return an array of Property Calendar
     * GET/propertycalendar
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');

        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        /* Load necessary Model */
        $property_calendar_model = new PropertyCalendarModel();
        $host_model = new HostModel();
        $property_model = new PropertyModel();

        /* Validate */
        $rules = [
            'property_id' => 'required|integer',
            'host_id' => 'required|integer',
        ];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'property_calendar'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $property_id = $this->request->getVar(
            'property_id'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'property_calendar'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such host_id',
                'notFound',
                'property_calendar'
            );
        }
        if ($property_model->find($property_id) == null) {
            return $this->notifyError(
                'No Such property_id',
                'invalid_data',
                'property_calendar'
            );
        }

        // getting available data from model
        $property_calendar = $property_calendar_model->get_property_calendar(
            $host_id,
            $property_id
        );

        return $this->respond([
            'property_calendar_set' =>
                $property_calendar == null
                    ? []
                    : $property_calendar,
        ]);
    }

    /**
     * Update model
     * PUT/propertycalendar/update
     * @return mixed
     */
    public function update($id = null)
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        /* Load necessary Model */
        $property_calendar_model = new PropertyCalendarModel();
        $property_model = new PropertyModel();
        $type_mapping_model = new TypeMappingModel();
        $trigger_cron_model = new TriggerCronModel();
        $host_model = new HostModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'host_id' => 'required|integer',
            'property_a_day' => 'required',
            'property_code' => 'required',
            'property_type_code' => 'required',
            'property_availability' =>
                'required|regex_match[/[01]/]',
        ];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'property_calendar'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $property_a_day = $this->request->getVar(
            'property_a_day'
        );
        $property_code = $this->request->getVar(
            'property_code'
        );
        $property_type_code = $this->request->getVar(
            'property_type_code'
        );
        $property_availability = $this->request->getVar(
            'property_availability'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'property_calendar'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'property_calendar'
            );
        }
        if (!$this->validateDate($property_a_day)) {
            return $this->notifyError(
                'Date format is incorrect.',
                'invalid_data',
                'property_calendar'
            );
        }
        $property_data = $property_model
            ->where([
                'property_host_id' => $host_id,
                'property_id' => $property_code,
            ])
            ->first();
        if ($property_data == null) {
            return $this->notifyError(
                'No Such property_code(property_id with same host_id)',
                'invalid_data',
                'property_calendar'
            );
        }
        if (
            $type_mapping_model
                ->where([
                    'type_mapping_code' => $property_type_code,
                    'type_mapping_host_id' => $host_id,
                    'type_mapping_main_status' => 1,
                ])
                ->findAll() == null
        ) {
            return $this->notifyError(
                'No Such property_type_code(type_mapping_code).',
                'notFound',
                'property_calendar'
            );
        }

        /* Update data in DB */
        $data = [
            'property_availability_host_id' => $host_id,
            'property_code' => $property_code,
            'property_a_day' => $property_a_day,
            'property_type_code' => $property_type_code,
            'property_availability' => $property_availability,
        ];
        // Update data
        $property_availability_data = $property_calendar_model
            ->where([
                'property_availability_host_id' => $host_id,
                'property_code' => $data['property_code'],
                'property_a_day' => $data['property_a_day'],
            ])
            ->first();
        $new_id = '';
        if ($property_availability_data != null) {
            if (
                !$property_calendar_model->update(
                    $property_availability_data[
                        'property_a_id'
                    ],
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'property_calendar'
                );
            }
        } else {
            $new_id = $property_calendar_model->insert(
                $data
            );
            if (!$new_id) {
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'property_calendar'
                );
            }
        }
        return $this->respond([
            'id' =>
                $property_availability_data == null
                    ? $new_id
                    : $property_availability_data[
                        'property_a_id'
                    ],
            'message' =>
                'Successfully ' .
                ($property_availability_data == null
                    ? 'created'
                    : 'updated'),
        ]);
    }
}
