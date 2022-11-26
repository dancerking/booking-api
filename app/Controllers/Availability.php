<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\TypeAvailabilityModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateInterval;
use DatePeriod;
use DateTime;

class Availability extends APIBaseController
{
    /**
     * Return an array of Per type Calendar availability
     * GET/availability
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');
        /* Load TypeAvailability Model */
        $type_availability_model = new TypeAvailabilityModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'l2_type_code' => 'required',
            'availabilityFrom' => 'required',
            'availabilityTo' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'availability'
            );
        }

        /* Getting availability type from TypeAvailabilityModel */
        $type_availability_code = $this->request->getVar(
            'l2_type_code'
        ); // L2 type code
        $from = $this->request->getVar('availabilityFrom'); // From type_availability_day, it could write only starting from today, not on last periods
        $to = $this->request->getVar('availabilityTo'); // To type_availability_day

        // check if date format
        if (
            !$this->validateDate($from) ||
            !$this->validateDate($to)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (new DateTime($to) < new DateTime($from)) {
            return $this->notifyError(
                'To date should be larger than From date.',
                'invalid_data',
                'availability'
            );
        }
        if (
            date_diff(
                new DateTime($to),
                new DateTime($from)
            )->days > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'date range is maximum ' .
                    $config->MAXIMUM_DATE_RANGE .
                    ' days',
                'invalid_data',
                'availability'
            );
        }
        if (new DateTime($from) < new DateTime()) {
            if (
                date_diff(
                    new DateTime(),
                    new DateTime($from)
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'date range is maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days',
                    'invalid_data',
                    'availability'
                );
            }
        }

        // getting availability data from model
        $availability_type = $type_availability_model->get_availability_types(
            $host_id,
            $type_availability_code,
            $from,
            $to
        );

        return $this->respond([
            'availability_types' =>
                $availability_type == null
                    ? []
                    : $availability_type,
        ]);
    }

    /**
     * Update model
     * PUT/availability/update
     * @return mixed
     */
    public function update($id = null)
    {
        $config = config('Config\App');
        /* Load TypeAvailability Model */
        $type_availability_model = new TypeAvailabilityModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'type_availability_code' => 'required',
            'type_availability_day' => 'required',
            'type_availability_qty' => 'required',
            'type_availability_msa' => 'required',
            'type_availability_coa' => 'required',
            'type_availability_cod' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'availability'
            );
        }

        /* Getting request data */
        $type_availability_code = $this->request->getVar(
            'type_availability_code'
        );
        $type_availability_day = $this->request->getVar(
            'type_availability_day'
        );
        $type_availability_qty = $this->request->getVar(
            'type_availability_qty'
        );
        $type_availability_msa = $this->request->getVar(
            'type_availability_msa'
        );
        $type_availability_coa = $this->request->getVar(
            'type_availability_coa'
        );
        $type_availability_cod = $this->request->getVar(
            'type_availability_cod'
        );

        /* Format validation */
        if (
            $type_mapping_model
                ->where([
                    'type_mapping_host_id' => $host_id,
                    'type_mapping_code' => $type_availability_code,
                ])
                ->findAll() == null
        ) {
            return $this->notifyError(
                'type_mapping_code is invalid',
                'notFound',
                'availability'
            );
        }
        if (
            !isset($type_availability_day->from) ||
            !isset($type_availability_day->to)
        ) {
            return $this->notifyError(
                'type_availability_day must have `from` and `to` fields',
                'invalid_data',
                'availability'
            );
        }
        if (
            !$this->validateDate(
                $type_availability_day->from
            ) ||
            !$this->validateDate($type_availability_day->to)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (
            new DateTime() >
                new DateTime(
                    $type_availability_day->from
                ) ||
            new DateTime() >
                new DateTime($type_availability_day->to)
        ) {
            return $this->notifyError(
                'type_availability_day should be larger than today',
                'invalid_data',
                'availability'
            );
        }
        if (
            new DateTime($type_availability_day->to) <
            new DateTime($type_availability_day->from)
        ) {
            return $this->notifyError(
                'To date should be larger than From date.',
                'invalid_data',
                'availability'
            );
        }
        if (
            date_diff(
                new DateTime($type_availability_day->to),
                new DateTime($type_availability_day->from)
            )->days > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'date range is maximum ' .
                    $config->MAXIMUM_DATE_RANGE .
                    ' days',
                'invalid_data',
                'availability'
            );
        }
        if (!ctype_digit((string) $type_availability_qty)) {
            return $this->notifyError(
                'Type availability qty format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (!ctype_digit((string) $type_availability_msa)) {
            return $this->notifyError(
                'Type availability msa format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (!ctype_digit((string) $type_availability_coa)) {
            return $this->notifyError(
                'Type availability coa format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (
            $type_availability_coa < 0 ||
            $type_availability_coa > 1
        ) {
            return $this->notifyError(
                'type_availability_coa should be 0 or 1',
                'invalid_data',
                'availability'
            );
        }
        if (!ctype_digit((string) $type_availability_cod)) {
            return $this->notifyError(
                'Type availability cod format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (
            $type_availability_cod < 0 ||
            $type_availability_cod > 1
        ) {
            return $this->notifyError(
                'type_availability_cod should be 0 or 1',
                'invalid_data',
                'availability'
            );
        }
        /* Update data in DB */
        $from = $type_availability_day->from;
        $to = $type_availability_day->to;
        while (strtotime($from) <= strtotime($to)) {
            $data = [
                'type_availability_host_id' => $host_id,
                'type_availability_code' => $type_availability_code,
                'type_availability_day' => $from,
                'type_availability_qty' => $type_availability_qty,
                'type_availability_msa' => $type_availability_msa,
                'type_availability_coa' => $type_availability_coa,
                'type_availability_cod' => $type_availability_cod,
            ];
            $matched_ids = $type_availability_model
                ->where([
                    'type_availability_host_id' => $host_id,
                    'type_availability_code' => $type_availability_code,
                    'type_availability_day' =>
                        $data['type_availability_day'],
                ])
                ->findAll();
            if ($matched_ids != null) {
                foreach ($matched_ids as $matched_id) {
                    if (
                        !$type_availability_model->update(
                            $matched_id[
                                'type_availability_id'
                            ],
                            $data
                        )
                    ) {
                        return $this->notifyError(
                            'Failed update',
                            'failed_update',
                            'availability'
                        );
                    }
                }
            } else {
                if (
                    !$type_availability_model->insert($data)
                ) {
                    return $this->notifyError(
                        'Failed insert',
                        'failed_insert',
                        'availability'
                    );
                }
            }
            $from = date(
                'Y-m-d',
                strtotime('+1 day', strtotime($from))
            );
        }
        return $this->respond([
            'message' => 'Successfully updated',
        ]);
    }
}
