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
            'l2_type_code'      => 'required',
            'availabilityFrom'  => 'required',
            'availabilityTo'    => 'required'
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        /* Getting availability type from TypeAvailabilityModel */
        $type_availability_code = $this->request->getVar('l2_type_code'); // L2 type code
        $from = $this->request->getVar('availabilityFrom'); // From type_availability_day, it could write only starting from today, not on last periods
        $to = $this->request->getVar('availabilityTo'); // To type_availability_day

        // check if date format
        if(!$this->validateDate($from)) {
            return $this->respond([
                'Date format error' => 'from date format is incorrect'
            ]);
        }
        if(!$this->validateDate($to)) {
            return $this->respond([
                'Date format error' => 'to date format is incorrect'
            ]);
        }
        if(new DateTime($to) < new DateTime($from)) {
            return $this->respond([
                'Date Error' => 'To date should be larger than From date.'
            ]);
        }
        // getting availability data from model
        $availability_type = $type_availability_model->get_availability_types($host_id, $type_availability_code, $from, $to);

        return $this->respond($availability_type == null ? [] : $availability_type, 200);
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
            'type_availability_id'   => 'required',
            'type_availability_code' => 'required',
            'type_availability_day'  => 'required',
            'type_availability_qty'  => 'required',
            'type_availability_msa'  => 'required',
            'type_availability_coa'  => 'required',
            'type_availability_cod'  => 'required',
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        /* Getting data from raw */
        $type_availability_id   = $this->request->getVar('type_availability_id');
        $type_availability_code = $this->request->getVar('type_availability_code');
        $type_availability_day  = $this->request->getVar('type_availability_day');
        $type_availability_qty  = $this->request->getVar('type_availability_qty');
        $type_availability_msa  = $this->request->getVar('type_availability_msa');
        $type_availability_coa  = $this->request->getVar('type_availability_coa');
        $type_availability_cod  = $this->request->getVar('type_availability_cod');

        /* Format validation */
        if(!$this->validateDate($type_availability_day)) {
            return $this->respond([
                'Date format error' => 'Date format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$type_availability_id)) {
            return $this->respond([
                'Int format error' => 'type_availability_id format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$type_availability_qty)) {
            return $this->respond([
                'Int format error' => 'type_availability_qty format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$type_availability_msa)) {
            return $this->respond([
                'Int format error' => 'type_availability_msa format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$type_availability_coa)) {
            return $this->respond([
                'Int format error' => 'type_availability_coa format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$type_availability_cod)) {
            return $this->respond([
                'Int format error' => 'type_availability_cod format is incorrect.'
            ]);
        }

        /* Update data in DB */
        $data = [
            'type_availability_host_id' => $host_id,
            'type_availability_code'    => $type_availability_code,
            'type_availability_day'     => $type_availability_day,
            'type_availability_qty'     => $type_availability_qty,
            'type_availability_msa'     => $type_availability_msa,
            'type_availability_coa'     => $type_availability_coa,
            'type_availability_cod'     => $type_availability_cod,
        ];
        if(!$type_availability_model->update($type_availability_id, $data)) {
            return $this->respond([
                'Failed message' => 'Failed update'
            ]);
        }
        return $this->respond([
            'Success' => 'Successfully updated'
        ]);
    }

    public function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
