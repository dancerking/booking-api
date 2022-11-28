<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\TypeAvailabilityModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;
use mysqli;

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
                'To date should be greater than From date.',
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
                    'maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days back date range',
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
            'type_code' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'availability'
            );
        }

        /* Getting request data */
        $type_code = $this->request->getVar('type_code');
        if (!is_array($type_code)) {
            return $this->notifyError(
                'type_code should be array',
                'invalid_data',
                'availability'
            );
        }
        if (
            count($type_code) > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'Max rows are ' .
                    $config->MAXIMUM_DATE_RANGE,
                'overflow',
                'availability'
            );
        }
        /* Format validation */
        foreach ($type_code as $row) {
            if (!isset($row->type_availability_code)) {
                return $this->notifyError(
                    'type_availability_code is required',
                    'invalid_data',
                    'availability'
                );
            }

            if (!isset($row->type_availability_from)) {
                return $this->notifyError(
                    'type_availability_from is required',
                    'invalid_data',
                    'availability'
                );
            }

            if (!isset($row->type_availability_to)) {
                return $this->notifyError(
                    'type_availability_to is required',
                    'invalid_data',
                    'availability'
                );
            }

            if (!isset($row->type_availability_qty)) {
                return $this->notifyError(
                    'type_availability_qty is required',
                    'invalid_data',
                    'availability'
                );
            }

            if (!isset($row->type_availability_msa)) {
                return $this->notifyError(
                    'type_availability_msa is required',
                    'invalid_data',
                    'availability'
                );
            }

            if (!isset($row->type_availability_coa)) {
                return $this->notifyError(
                    'type_availability_coa is required',
                    'invalid_data',
                    'availability'
                );
            }

            if (!isset($row->type_availability_cod)) {
                return $this->notifyError(
                    'type_availability_cod is required',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_host_id' => $host_id,
                        'type_mapping_code' =>
                            $row->type_availability_code,
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
                !$this->validateDate(
                    $row->type_availability_from
                ) ||
                !$this->validateDate(
                    $row->type_availability_to
                )
            ) {
                return $this->notifyError(
                    'Date format is incorrect',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                new DateTime() >
                new DateTime($row->type_availability_from)
            ) {
                return $this->notifyError(
                    'type_availability_from should be greater than today',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                new DateTime($row->type_availability_to) <
                new DateTime($row->type_availability_from)
            ) {
                return $this->notifyError(
                    'type_availability_to should be greater than type_availability_from.',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                date_diff(
                    new DateTime(
                        $row->type_availability_to
                    ),
                    new DateTime(
                        $row->type_availability_from
                    )
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days back date range',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                !ctype_digit(
                    (string) $row->type_availability_qty
                )
            ) {
                return $this->notifyError(
                    'Type availability qty format is incorrect',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                !ctype_digit(
                    (string) $row->type_availability_msa
                )
            ) {
                return $this->notifyError(
                    'Type availability msa format is incorrect',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                !ctype_digit(
                    (string) $row->type_availability_coa
                )
            ) {
                return $this->notifyError(
                    'Type availability coa format is incorrect',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                $row->type_availability_coa < 0 ||
                $row->type_availability_coa > 1
            ) {
                return $this->notifyError(
                    'type_availability_coa should be 0 or 1',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                !ctype_digit(
                    (string) $row->type_availability_cod
                )
            ) {
                return $this->notifyError(
                    'Type availability cod format is incorrect',
                    'invalid_data',
                    'availability'
                );
            }
            if (
                $row->type_availability_cod < 0 ||
                $row->type_availability_cod > 1
            ) {
                return $this->notifyError(
                    'type_availability_cod should be 0 or 1',
                    'invalid_data',
                    'availability'
                );
            }
        }

        /* Update data in DB */
        $multi_query = [];
        foreach ($type_code as $row) {
            $from = $row->type_availability_from;
            $to = $row->type_availability_to;
            while (strtotime($from) <= strtotime($to)) {
                $data = [
                    'type_availability_host_id' => $host_id,
                    'type_availability_code' =>
                        $row->type_availability_code,
                    'type_availability_day' => $from,
                    'type_availability_qty' =>
                        $row->type_availability_qty,
                    'type_availability_msa' =>
                        $row->type_availability_msa,
                    'type_availability_coa' =>
                        $row->type_availability_coa,
                    'type_availability_cod' =>
                        $row->type_availability_cod,
                ];
                $matched_ids = $type_availability_model
                    ->where([
                        'type_availability_host_id' => $host_id,
                        'type_availability_code' =>
                            $data['type_availability_code'],
                        'type_availability_day' =>
                            $data['type_availability_day'],
                    ])
                    ->findAll();
                if ($matched_ids != null) {
                    foreach ($matched_ids as $matched_id) {
                        array_push(
                            $multi_query,
                            'UPDATE type_availability SET type_availability_host_id = ' .
                                $data[
                                    'type_availability_host_id'
                                ] .
                                ', type_availability_code = "' .
                                $data[
                                    'type_availability_code'
                                ] .
                                '", type_availability_day = "' .
                                $data[
                                    'type_availability_day'
                                ] .
                                '", type_availability_qty = ' .
                                $data[
                                    'type_availability_qty'
                                ] .
                                ', type_availability_msa = ' .
                                $data[
                                    'type_availability_msa'
                                ] .
                                ', type_availability_coa = ' .
                                $data[
                                    'type_availability_coa'
                                ] .
                                ', type_availability_cod = ' .
                                $data[
                                    'type_availability_cod'
                                ] .
                                ' WHERE type_availability_id = ' .
                                $matched_id[
                                    'type_availability_id'
                                ]
                        );
                    }
                } else {
                    array_push(
                        $multi_query,
                        'INSERT INTO type_availability (type_availability_host_id, type_availability_code, type_availability_day, type_availability_qty, type_availability_msa, type_availability_coa, type_availability_cod)
                    VALUES (' .
                            $data[
                                'type_availability_host_id'
                            ] .
                            ', "' .
                            $data[
                                'type_availability_code'
                            ] .
                            '", "' .
                            $data['type_availability_day'] .
                            '", ' .
                            $data['type_availability_qty'] .
                            ', ' .
                            $data['type_availability_msa'] .
                            ', ' .
                            $data['type_availability_coa'] .
                            ', ' .
                            $data['type_availability_cod'] .
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
            !$type_availability_model->multi_query_execute(
                $multi_query
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'availability'
            );
        }
        return $this->respond([
            'message' => 'Successfully updated',
        ]);
    }
}
