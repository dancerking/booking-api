<?php

namespace App\Controllers;

use App\Models\RateCalendarModel;
use App\Models\RateLangModel;
use App\Models\RateMappingModel;
use App\Models\RateModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class RateCalendar extends ResourceController
{
	/**
	 * Return an array of Rate
	 * GET/baseratesettings
	 * @return mixed
	 */
	use ResponseTrait;

    /**
     * Create a model resource
     * POST/baseratesettings/add
     * @return mixed
     */
    public function create()
    {
        /* Load Rate relation Models */
        $rate_calendar_model = new RateCalendarModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'daily_rate_code'           => 'required',
            'daily_rate_type'           => 'required',
            'daily_rate_day'            => 'required',
            'daily_rate_baserate'       => 'required',
            'daily_rate_guesttype_1'    => 'required',
            'daily_rate_guesttype_2'    => 'required',
            'daily_rate_guesttype_3'    => 'required',
            'daily_rate_guesttype_4'    => 'required',
            'daily_rate_minstay'        => 'required',
            'daily_rate_maxstay'        => 'required',
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        /* Getting data from raw */
        $daily_rate_code = $this->request->getVar('daily_rate_code');
        $daily_rate_type = $this->request->getVar('daily_rate_type');
        $daily_rate_day = $this->request->getVar('daily_rate_day');
        $daily_rate_baserate = $this->request->getVar('daily_rate_baserate');
        $daily_rate_guesttype_1 = $this->request->getVar('daily_rate_guesttype_1');
        $daily_rate_guesttype_2 = $this->request->getVar('daily_rate_guesttype_2');
        $daily_rate_guesttype_3 = $this->request->getVar('daily_rate_guesttype_3');
        $daily_rate_guesttype_4 = $this->request->getVar('daily_rate_guesttype_4');
        $daily_rate_minstay = $this->request->getVar('daily_rate_minstay');
        $daily_rate_maxstay = $this->request->getVar('daily_rate_maxstay');

        /* Format validation */
        if(!ctype_digit((string)$daily_rate_code)) {
            return $this->respond([
                'Int format error' => 'daily_rate_code format is incorrect.'
            ]);
        }
        if(!$this->validateDate($daily_rate_day)) {
            return $this->respond([
                'Date format error' => 'daily_rate_day format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_baserate, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_baserate format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_1, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_1 format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_2, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_2 format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_3, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_3 format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_4, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_4 format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$daily_rate_minstay)) {
            return $this->respond([
                'Int format error' => 'daily_rate_minstay format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$daily_rate_maxstay)) {
            return $this->respond([
                'Int format error' => 'daily_rate_maxstay format is incorrect.'
            ]);
        }
        /* Update data in DB */
        /** Rate Calendar Model management */
        $rate_calendar_data = [
            'rate_calendar_host_id'     => $host_id,
            'daily_rate_code'           => $daily_rate_code,
            'daily_rate_type'           => $daily_rate_type,
            'daily_rate_day'            => $daily_rate_day,
            'daily_rate_baserate'       => $daily_rate_baserate,
            'daily_rate_guesttype_1'    => $daily_rate_guesttype_1,
            'daily_rate_guesttype_2'    => $daily_rate_guesttype_2,
            'daily_rate_guesttype_3'    => $daily_rate_guesttype_3,
            'daily_rate_guesttype_4'    => $daily_rate_guesttype_4,
            'daily_rate_minstay'        => $daily_rate_minstay,
            'daily_rate_maxstay'        => $daily_rate_maxstay,
        ];
        $new_id = $rate_calendar_model->insert($rate_calendar_data);
        if($new_id == null) {
            return $this->respond([
                'Failed message' => 'Failed insert.'
            ]);
        }
        return $this->respond([
            'Success' => 'new_id:' . $new_id . ' Successfully created.'
        ]);
    }

    /**
     * Update a model resource
     * PUT/baseratesettings/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Load Rate relation Models */
        $rate_calendar_model = new RateCalendarModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'daily_rate_id'             => 'required',
            'daily_rate_code'           => 'required',
            'daily_rate_type'           => 'required',
            'daily_rate_day'            => 'required',
            'daily_rate_baserate'       => 'required',
            'daily_rate_guesttype_1'    => 'required',
            'daily_rate_guesttype_2'    => 'required',
            'daily_rate_guesttype_3'    => 'required',
            'daily_rate_guesttype_4'    => 'required',
            'daily_rate_minstay'        => 'required',
            'daily_rate_maxstay'        => 'required',
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        /* Getting data from raw */
        $daily_rate_id = $this->request->getVar('daily_rate_id');
        $daily_rate_code = $this->request->getVar('daily_rate_code');
        $daily_rate_type = $this->request->getVar('daily_rate_type');
        $daily_rate_day = $this->request->getVar('daily_rate_day');
        $daily_rate_baserate = $this->request->getVar('daily_rate_baserate');
        $daily_rate_guesttype_1 = $this->request->getVar('daily_rate_guesttype_1');
        $daily_rate_guesttype_2 = $this->request->getVar('daily_rate_guesttype_2');
        $daily_rate_guesttype_3 = $this->request->getVar('daily_rate_guesttype_3');
        $daily_rate_guesttype_4 = $this->request->getVar('daily_rate_guesttype_4');
        $daily_rate_minstay = $this->request->getVar('daily_rate_minstay');
        $daily_rate_maxstay = $this->request->getVar('daily_rate_maxstay');

        /* Format validation */
        if(!ctype_digit((string)$daily_rate_id)) {
            return $this->respond([
                'Int format error' => 'daily_rate_id format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$daily_rate_code)) {
            return $this->respond([
                'Int format error' => 'daily_rate_code format is incorrect.'
            ]);
        }
        if(!$this->validateDate($daily_rate_day)) {
            return $this->respond([
                'Date format error' => 'daily_rate_day format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_baserate, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_baserate format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_1, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_1 format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_2, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_2 format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_3, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_3 format is incorrect.'
            ]);
        }
        if(fmod($daily_rate_guesttype_4, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'daily_rate_guesttype_4 format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$daily_rate_minstay)) {
            return $this->respond([
                'Int format error' => 'daily_rate_minstay format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$daily_rate_maxstay)) {
            return $this->respond([
                'Int format error' => 'daily_rate_maxstay format is incorrect.'
            ]);
        }
        /* Update data in DB */
        /** Rate Calendar Model management */
        $rate_calendar_data = [
            'daily_rate_id'             => $daily_rate_id,
            'rate_calendar_host_id'     => $host_id,
            'daily_rate_code'           => $daily_rate_code,
            'daily_rate_type'           => $daily_rate_type,
            'daily_rate_day'            => $daily_rate_day,
            'daily_rate_baserate'       => $daily_rate_baserate,
            'daily_rate_guesttype_1'    => $daily_rate_guesttype_1,
            'daily_rate_guesttype_2'    => $daily_rate_guesttype_2,
            'daily_rate_guesttype_3'    => $daily_rate_guesttype_3,
            'daily_rate_guesttype_4'    => $daily_rate_guesttype_4,
            'daily_rate_minstay'        => $daily_rate_minstay,
            'daily_rate_maxstay'        => $daily_rate_maxstay,
        ];
        if($rate_calendar_model->update($daily_rate_id, $rate_calendar_data) == null) {
            return $this->respond([
                'Failed message' => 'Failed update.'
            ]);
        }
        return $this->respond([
            'Success' => 'daily_rate_id:' . $daily_rate_id . ' Successfully updated.'
        ]);
    }

    /**
     * Delete Rate content
     * DELETE /baseratesettings/delete
     * @return mixed
     */
    public function delete($rate_id = null)
    {
        $host_id = $this->get_host_id();
        if($rate_id == null) {
            return $this->respond([
                'message' => [
                    'error' => 'Failed Delete'
                ]
                ]);
        }
        $rate_model = new RateModel();
        $check_id_exist = $rate_model->is_existed_id($rate_id);
        if($check_id_exist == null) {
            return $this->respond([
                'message' => [
                    'error' => 'No Such Data'
                ]
            ]);
        }
        if ($rate_model->delete($rate_id)) {
            $rate_mapping_model = new RateMappingModel();
            $rate_lang_model = new RateLangModel();
            if(!$rate_mapping_model->delete_by($rate_id, $host_id)) {
                return $this->respond([
                    'message' => [
                        'Error' => 'Failed Rate Mapping data Delete'
                    ]
                    ]);
            }
            if(!$rate_lang_model->delete_by($rate_id, $host_id)) {
                return $this->respond([
                    'message' => [
                        'Error' => 'Failed Rate lang data Delete'
                    ]
                    ]);
            }
            return $this->respond([
                'message' => [
                    'success' => 'id:' . $rate_id .' Successfully Deleted'
                ]
            ]);
        }
        return $this->respond([
            'message' => [
                'error' => 'Failed Deleted'
            ]
        ]);
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function is_decimal( $val )
    {
        var_dump(floor($val));
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
