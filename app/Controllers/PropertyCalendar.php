<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\HostModel;
use App\Models\PropertyCalendarModel;
use App\Models\PropertyModel;
use App\Models\TriggerCronModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

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

        return parent::respond([
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
            'property_codes' => 'required',
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
        $property_codes = $this->request->getVar(
            'property_codes'
        );
        if (!is_array($property_codes)) {
            return $this->notifyError(
                'property_codes should be array',
                'invalid_data',
                'property_calendar'
            );
        }
        if (
            count($property_codes) >
            $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'Max rows are ' .
                    $config->MAXIMUM_DATE_RANGE,
                'overflow',
                'property_calendar'
            );
        }
        /* host_id Validation */
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
        /* Format Validation */
        foreach ($property_codes as $row) {
            if (!isset($row->property_availability_from)) {
                return $this->notifyError(
                    'property_availability_from is required',
                    'invalid_data',
                    'property_calendar'
                );
            }

            if (!isset($row->property_availability_to)) {
                return $this->notifyError(
                    'property_availability_to is required',
                    'invalid_data',
                    'property_calendar'
                );
            }

            if (!isset($row->property_code)) {
                return $this->notifyError(
                    'property_code is required',
                    'invalid_data',
                    'property_calendar'
                );
            }

            if (!isset($row->property_type_code)) {
                return $this->notifyError(
                    'property_type_code is required',
                    'invalid_data',
                    'property_calendar'
                );
            }
            // property_code validation
            $property_data = $property_model
                ->where([
                    'property_host_id' => $host_id,
                    'property_id' => $row->property_code,
                ])
                ->first();
            if ($property_data == null) {
                return $this->notifyError(
                    'No Such property_code(property_id with same host_id)',
                    'invalid_data',
                    'property_calendar'
                );
            }

            // type_code validation
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_code' =>
                            $row->property_type_code,
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

            // Date validation
            if (
                !$this->validateDate(
                    $row->property_availability_from
                ) ||
                !$this->validateDate(
                    $row->property_availability_to
                )
            ) {
                return $this->notifyError(
                    'Date format is incorrect',
                    'invalid_data',
                    'property_calendar'
                );
            }
            if (
                new DateTime() >
                new DateTime(
                    $row->property_availability_from
                )
            ) {
                return $this->notifyError(
                    'property_availability_from should be equal or greater than today',
                    'invalid_data',
                    'property_calendar'
                );
            }
            if (
                new DateTime(
                    $row->property_availability_from
                ) >
                new DateTime($row->property_availability_to)
            ) {
                return $this->notifyError(
                    'From date should be smaller than to date',
                    'invalid_data',
                    'property_calendar'
                );
            }
            if (
                date_diff(
                    new DateTime(
                        $row->property_availability_to
                    ),
                    new DateTime(
                        $row->property_availability_from
                    )
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'date range is maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days',
                    'invalid_data',
                    'property_calendar'
                );
            }

            // property_availability validation
            if (
                !ctype_digit(
                    (string) $row->property_availability
                )
            ) {
                return $this->notifyError(
                    'property_availability format should be 0 or 1.',
                    'invalid_data',
                    'property_calendar'
                );
            }
            if (
                $row->property_availability < 0 ||
                $row->property_availability > 1
            ) {
                return $this->notifyError(
                    'property_availability format should be 0 or 1.',
                    'invalid_data',
                    'property_calendar'
                );
            }
        }

        /* Update data in DB */
        $multi_query = [];
        foreach ($property_codes as $row) {
            $from = $row->property_availability_from;
            $to = $row->property_availability_to;
            while (strtotime($from) <= strtotime($to)) {
                $data = [
                    'property_availability_host_id' => $host_id,
                    'property_code' => $row->property_code,
                    'property_a_day' => $from,
                    'property_type_code' =>
                        $row->property_type_code,
                    'property_availability' =>
                        $row->property_availability,
                ];
                // Check if data exist
                $matched_ids = $property_calendar_model
                    ->where([
                        'property_availability_host_id' => $host_id,
                        'property_code' =>
                            $data['property_code'],
                        'property_a_day' =>
                            $data['property_a_day'],
                    ])
                    ->findAll();
                // Update data
                if ($matched_ids != null) {
                    foreach ($matched_ids as $matched_id) {
                        array_push(
                            $multi_query,
                            'UPDATE property_availability SET property_availability_host_id = ' .
                                $data[
                                    'property_availability_host_id'
                                ] .
                                ', property_code = ' .
                                $data['property_code'] .
                                ', property_type_code = "' .
                                $data[
                                    'property_type_code'
                                ] .
                                '", property_a_day = "' .
                                $data['property_a_day'] .
                                '", property_availability = ' .
                                $data[
                                    'property_availability'
                                ] .
                                ' WHERE property_a_id = ' .
                                $matched_id['property_a_id']
                        );
                    }
                } else {
                    array_push(
                        $multi_query,
                        'INSERT INTO property_availability (property_availability_host_id, property_code, property_type_code, property_a_day, property_availability)
                    VALUES (' .
                            $data[
                                'property_availability_host_id'
                            ] .
                            ', ' .
                            $data['property_code'] .
                            ', "' .
                            $data['property_type_code'] .
                            '", "' .
                            $data['property_a_day'] .
                            '", ' .
                            $data['property_availability'] .
                            ')'
                    );
                }
                $from = date(
                    'Y-m-d',
                    strtotime('+1 day', strtotime($from))
                );
            }
        }
        if (
            !$property_calendar_model->multi_query_execute(
                $multi_query
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'property_calendar'
            );
        }
        $trigger_multi_query = [];
        foreach ($property_codes as $row) {
            array_push(
                $trigger_multi_query,
                'INSERT INTO trigger_cron (trigger_property_id, trigger_type_code)
            VALUES (' .
                    $row->property_code .
                    ', ' .
                    $row->property_type_code .
                    ')'
            );
        }
        if (
            !$trigger_cron_model->multi_query_execute(
                $trigger_multi_query
            )
        ) {
            return $this->notifyError(
                'Failed insert for trigger_cron',
                'failed_insert',
                'property_calendar'
            );
        }
        return parent::respond([
            'message' => 'Successfully updated.',
        ]);
    }
}
