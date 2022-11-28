<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\ServiceCalendarModel;
use App\Models\ServiceMappingModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class ServicePriceCalendar extends APIBaseController
{
    /**
     * Return an array of Service Price Calendar
     * GET/servicecalendar
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');

        /* Load ServiceCalendar Model */
        $service_calendar_model = new ServiceCalendarModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'property_type' => 'required',
            'serviceFrom' => 'required',
            'serviceTo' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }

        /* Getting service calendar from ServicePriceCalendarModel */
        // service_price_type
        $service_price_type = $this->request->getVar(
            'property_type'
        );
        // From service_price_day, today > serviceFrom, today-serviceFrom <= 90
        $servicefrom = $this->request->getVar(
            'serviceFrom'
        );
        // To service_price_day, serviceTo > serviceFrom, serviceTo-serviceFrom <= 90,
        $serviceto = $this->request->getVar('serviceTo');

        // check if date format
        if (
            !$this->validateDate($servicefrom) ||
            !$this->validateDate($serviceto)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            new DateTime($serviceto) <
            new DateTime($servicefrom)
        ) {
            return $this->notifyError(
                'To date should be greater than From date.',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            date_diff(
                new DateTime($serviceto),
                new DateTime($servicefrom)
            )->days > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'date range is maximum ' .
                    $config->MAXIMUM_DATE_RANGE .
                    ' days',
                'invalid_data',
                'service_calendar'
            );
        }
        if (new DateTime($servicefrom) < new DateTime()) {
            if (
                date_diff(
                    new DateTime(),
                    new DateTime($servicefrom)
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days back date range',
                    'invalid_data',
                    'service_calendar'
                );
            }
        }

        // getting availabile data from model
        $service_price_calendar = $service_calendar_model->get_price_calendar(
            $host_id,
            $service_price_type,
            $servicefrom,
            $serviceto
        );

        return $this->respond([
            'service_price_calendar' =>
                $service_price_calendar == null
                    ? []
                    : $service_price_calendar,
        ]);
    }

    /**
     * Update model
     * PUT/servicecalendar/update
     * @return mixed
     */
    public function update($id = null)
    {
        $config = config('Config\App');
        /* Load necessary Model */
        $service_calendar_model = new ServiceCalendarModel();
        $service_mapping_model = new ServiceMappingModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'price_code' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }

        /* Getting request data */
        $price_code = $this->request->getVar('price_code');
        if (!is_array($price_code)) {
            return $this->notifyError(
                'price_code should be array',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            count($price_code) > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'Max rows are ' .
                    $config->MAXIMUM_DATE_RANGE,
                'overflow',
                'service_calendar'
            );
        }

        /* Format Validation */
        foreach ($price_code as $row) {
            if (!isset($row->service_price_code)) {
                return $this->notifyError(
                    'service_price_code is required',
                    'invalid_data',
                    'service_calendar'
                );
            }

            if (!isset($row->service_price_type)) {
                return $this->notifyError(
                    'service_price_type is required',
                    'invalid_data',
                    'service_calendar'
                );
            }

            if (!isset($row->service_price_from)) {
                return $this->notifyError(
                    'service_price_from is required',
                    'invalid_data',
                    'service_calendar'
                );
            }

            if (!isset($row->service_price_to)) {
                return $this->notifyError(
                    'service_price_to is required',
                    'invalid_data',
                    'service_calendar'
                );
            }

            if (!isset($row->service_price)) {
                return $this->notifyError(
                    'service_price is required',
                    'invalid_data',
                    'service_calendar'
                );
            }

            if (
                !$this->validateDate(
                    $row->service_price_from
                ) ||
                !$this->validateDate($row->service_price_to)
            ) {
                return $this->notifyError(
                    'Date format is incorrect',
                    'invalid_data',
                    'service_calendar'
                );
            }
            if (
                new DateTime() >
                new DateTime($row->service_price_from)
            ) {
                return $this->notifyError(
                    'service_price_from should be greater than today',
                    'invalid_data',
                    'service_calendar'
                );
            }
            if (
                new DateTime($row->service_price_from) >
                new DateTime($row->service_price_to)
            ) {
                return $this->notifyError(
                    'service_price_from should be smaller than service_price_to',
                    'invalid_data',
                    'service_calendar'
                );
            }
            if (
                date_diff(
                    new DateTime($row->service_price_to),
                    new DateTime($row->service_price_from)
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'date range is maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days',
                    'invalid_data',
                    'service_calendar'
                );
            }
            if (
                !ctype_digit(
                    (string) $row->service_price_code
                )
            ) {
                return $this->notifyError(
                    'service_price_code format is incorrect',
                    'invalid_data',
                    'service_calendar'
                );
            }
            if (
                $service_mapping_model->find(
                    $row->service_price_code
                ) == null
            ) {
                return $this->notifyError(
                    'No Such service_price_code(service_mapping_id)',
                    'notFound',
                    'service_calendar'
                );
            }
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_code' =>
                            $row->service_price_type,
                        'type_mapping_host_id' => $host_id,
                    ])
                    ->findAll() == null
            ) {
                return $this->notifyError(
                    'No Such service_price_type(type_mapping_code).',
                    'notFound',
                    'service_calendar'
                );
            }
            if (!is_numeric($row->service_price)) {
                return $this->notifyError(
                    'service_price format is incorrect.',
                    'invalid_data',
                    'service_calendar'
                );
            }
        }

        /* Update data in DB */
        foreach ($price_code as $row) {
            $from = $row->service_price_from;
            $to = $row->service_price_to;
            while (strtotime($from) <= strtotime($to)) {
                $data = [
                    'service_price_host_id' => $host_id,
                    'service_price_code' =>
                        $row->service_price_code,
                    'service_price_type' =>
                        $row->service_price_type,
                    'service_price_day' =>
                        $row->service_price_from,
                    'service_price' => $row->service_price,
                ];
                // Check if data exist
                $matched_ids = $service_calendar_model
                    ->where([
                        'service_price_host_id' => $host_id,
                        'service_price_code' =>
                            $data['service_price_code'],
                        'service_price_type' =>
                            $data['service_price_type'],
                        'service_price_day' =>
                            $data['service_price_day'],
                    ])
                    ->findAll();
                // Update data
                if ($matched_ids != null) {
                    foreach ($matched_ids as $matched_id) {
                        if (
                            !$service_calendar_model->update(
                                $matched_id[
                                    'service_price_id'
                                ],
                                $data
                            )
                        ) {
                            return $this->notifyError(
                                'Failed update',
                                'failed_update',
                                'service_calendar'
                            );
                        }
                    }
                } else {
                    if (
                        !$service_calendar_model->insert(
                            $data
                        )
                    ) {
                        return $this->notifyError(
                            'Failed insert',
                            'failed_insert',
                            'service_calendar'
                        );
                    }
                }
                $from = date(
                    'Y-m-d',
                    strtotime('+1 day', strtotime($from))
                );
            }
        }

        return $this->respond([
            'message' => 'Successfully updated.',
        ]);
    }
}
