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
                'To date should be larger than From date.',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            date_diff(
                new DateTime($serviceto),
                new DateTime($servicefrom)
            )->days > $config->maximum_date_range
        ) {
            return $this->notifyError(
                'date range is maximum ' .
                    $config->maximum_date_range .
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
                )->days > $config->maximum_date_range
            ) {
                return $this->notifyError(
                    'date range is maximum ' .
                        $config->maximum_date_range .
                        ' days',
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
     * Insert model
     * POST/servicecalendar/add
     * @return mixed
     */
    public function create($id = null)
    {
        /* Load necessary Model */
        $service_calendar_model = new ServiceCalendarModel();
        $service_mapping_model = new ServiceMappingModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'service_price_code' => 'required',
            'service_price_type' => 'required',
            'service_price_day' => 'required',
            'service_price' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }

        /* Getting data from raw */
        $service_price_code = $this->request->getVar(
            'service_price_code'
        );
        $service_price_type = $this->request->getVar(
            'service_price_type'
        );
        $service_price_day = $this->request->getVar(
            'service_price_day'
        );
        $service_price = $this->request->getVar(
            'service_price'
        );
        $data = [];
        /* Format validation */
        if (!$this->validateDate($service_price_day)) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }
        if (!ctype_digit((string) $service_price_code)) {
            return $this->notifyError(
                'service_price_code ormat is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }
        if (fmod($service_price, 1) !== 0.0) {
            return $this->notifyError(
                'service_price format is incorrect.',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            $service_mapping_model->find(
                $service_price_code
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
                    'type_mapping_code' => $service_price_type,
                    'type_mapping_host_id' => $host_id,
                ])
                ->findAll() == null
        ) {
            return $this->notifyError(
                'No Such service_price_type(type_mapping_code)',
                'notFound',
                'service_calendar'
            );
        }
        /* Insert data in DB */
        if (
            $service_calendar_model
                ->where([
                    'service_price_code' => $service_price_code,
                    'service_price_type' => $service_price_type,
                    'service_price_day' => $service_price_day,
                    'service_price' => $service_price,
                    'service_price_host_id' => $host_id,
                ])
                ->findAll() != null
        ) {
            return $this->notifyError(
                'Duplication error',
                'duplicate',
                'service_calendar'
            );
        }
        $data = [
            'service_price_code' => $service_price_code,
            'service_price_type' => $service_price_type,
            'service_price_day' => $service_price_day,
            'service_price' => $service_price,
            'service_price_host_id' => $host_id,
        ];
        $new_id = $service_calendar_model->insert($data);
        if (!$new_id) {
            return $this->notifyError(
                'Failed create',
                'failed_create',
                'service_calendar'
            );
        }
        return $this->respond([
            'id' => $new_id,
            'Success' => 'Successfully created.',
        ]);
    }
    /**
     * Update model
     * PUT/servicecalendar/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Load necessary Model */
        $service_calendar_model = new ServiceCalendarModel();
        $service_mapping_model = new ServiceMappingModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'service_price_id' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }

        /* Getting data from raw */
        $service_price_id = $this->request->getVar(
            'service_price_id'
        );
        $service_price_code = $this->request->getVar(
            'service_price_code'
        );
        $service_price_type = $this->request->getVar(
            'service_price_type'
        );
        $service_price_day = $this->request->getVar(
            'service_price_day'
        );
        $service_price = $this->request->getVar(
            'service_price'
        );
        $data = [];
        /* Format validation */
        if (!ctype_digit((string) $service_price_id)) {
            return $this->notifyError(
                'Type service_price_id id format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            $service_price_day != null &&
            !$this->validateDate($service_price_day)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }
        if ($service_price_day != null) {
            $data['service_price_day'] = $service_price_day;
        }
        if (
            $service_price_code != null &&
            !ctype_digit((string) $service_price_code)
        ) {
            return $this->notifyError(
                'service_price_code ormat is incorrect',
                'invalid_data',
                'service_calendar'
            );
        }
        if (
            $service_price != null &&
            fmod($service_price, 1) !== 0.0
        ) {
            return $this->notifyError(
                'service_price format is incorrect.',
                'invalid_data',
                'service_calendar'
            );
        }
        if ($service_price != null) {
            $data['service_price'] = $service_price;
        }
        if (
            $service_price_code != null &&
            $service_mapping_model->find(
                $service_price_code
            ) == null
        ) {
            return $this->notifyError(
                'No Such service_price_code(service_mapping_id)',
                'notFound',
                'service_calendar'
            );
        }
        if ($service_price_code != null) {
            $data[
                'service_price_code'
            ] = $service_price_code;
        }
        if (
            $service_price_type != null &&
            $type_mapping_model
                ->where([
                    'type_mapping_code' => $service_price_type,
                    'type_mapping_host_id' => $host_id,
                ])
                ->findAll() == null
        ) {
            return $this->notifyError(
                'No Such service_price_type(type_mapping_code)',
                'notFound',
                'service_calendar'
            );
        }
        if ($service_price_type != null) {
            $data[
                'service_price_type'
            ] = $service_price_type;
        }
        /* Update data in DB */

        if (
            !$service_calendar_model->update(
                $service_price_id,
                $data
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'service_calendar'
            );
        }
        return $this->respond([
            'id' => $service_price_id,
            'Success' => 'Successfully updated',
        ]);
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
