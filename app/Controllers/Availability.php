<?php

namespace App\Controllers;

use App\Models\TypeAvailabilityModel;
use App\Controllers\APIBaseController;
use CodeIgniter\API\ResponseTrait;
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
            )->days > 90
        ) {
            return $this->notifyError(
                'date range is maximum 90 days',
                'invalid_data',
                'availability'
            );
        }
        // getting availability data from model
        $availability_type = $type_availability_model->get_availability_types(
            $host_id,
            $type_availability_code,
            $from,
            $to
        );

        return $this->respond([
            'availability_type' =>
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
        /* Load TypeAvailability Model */
        $type_availability_model = new TypeAvailabilityModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'type_availability_id' => 'required',
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

        /* Getting data from raw */
        $type_availability_id = $this->request->getVar(
            'type_availability_id'
        );
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
        if (!ctype_digit((string) $type_availability_id)) {
            return $this->notifyError(
                'Type availability id format is incorrect',
                'invalid_data',
                'availability'
            );
        }
        if (!$this->validateDate($type_availability_day)) {
            return $this->notifyError(
                'Date format is incorrect',
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
        if (!ctype_digit((string) $type_availability_cod)) {
            return $this->notifyError(
                'Type availability cod format is incorrect',
                'invalid_data',
                'availability'
            );
        }

        /* Update data in DB */
        $data = [
            'type_availability_host_id' => $host_id,
            'type_availability_code' => $type_availability_code,
            'type_availability_day' => $type_availability_day,
            'type_availability_qty' => $type_availability_qty,
            'type_availability_msa' => $type_availability_msa,
            'type_availability_coa' => $type_availability_coa,
            'type_availability_cod' => $type_availability_cod,
        ];
        if (
            !$type_availability_model->update(
                $type_availability_id,
                $data
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'availability'
            );
        }
        return $this->respond([
            'id' => $type_availability_id,
            'Success' => 'Successfully updated',
        ]);
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
