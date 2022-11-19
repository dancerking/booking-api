<?php

namespace App\Controllers;

use App\Models\RateCalendarModel;
use App\Models\RateLangModel;
use App\Models\RateMappingModel;
use App\Models\RateModel;
use App\Controllers\APIBaseController;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class RateCalendar extends APIBaseController
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
        if(!$this->validate($rules)) return $this->notifyError('Input data format is incorrect.', 'invalid_data', 'rate_calendar');

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
            return $this->notifyError('daily_rate_code format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        $rate_model = new RateModel();
        if($rate_model->is_existed_id($daily_rate_code) == null) {
            return $this->notifyError('No Such daily_rate_code', 'invalid_data', 'rate_calendar');
        }
        if(!$this->validateDate($daily_rate_day)) {
            return $this->notifyError('daily_rate_day format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(fmod($daily_rate_baserate, 1) !== 0.00) {
            return $this->notifyError('daily_rate_baserate format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(fmod($daily_rate_guesttype_1, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_1 format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(fmod($daily_rate_guesttype_2, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_2 format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(fmod($daily_rate_guesttype_3, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_3 format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(fmod($daily_rate_guesttype_4, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_4 format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(!ctype_digit((string)$daily_rate_minstay)) {
            return $this->notifyError('daily_rate_minstay format is incorrect.', 'invalid_data', 'rate_calendar');
        }
        if(!ctype_digit((string)$daily_rate_maxstay)) {
            return $this->notifyError('daily_rate_maxstay format is incorrect.', 'invalid_data', 'rate_calendar');
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
           return $this->notifyError('Falied create', 'failed_create', 'rate_calendar');
        }
        return $this->respond([
            'id'        => $new_id,
            'message'   => 'Successfully created.'
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
        if(!$this->validate($rules)) return $this->notifyError('Input data format is incorrect.', 'invalid_data', 'rate_calendar');

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
            return $this->notifyError('daily_rate_id format is incorrect');
        }
        if(!ctype_digit((string)$daily_rate_code)) {
            return $this->notifyError('daily_rate_code format is incorrect');
        }
        $rate_model = new RateModel();
        if($rate_model->is_existed_id($daily_rate_code) == null) {
            return $this->notifyError('No Such daily_rate_code', 'invalid_data', 'rate_calendar');
        }
        if(!$this->validateDate($daily_rate_day)) {
            return $this->notifyError('daily_rate_day format is incorrect');
        }
        if(fmod($daily_rate_baserate, 1) !== 0.00) {
            return $this->notifyError('daily_rate_baserate format is incorrect');
        }
        if(fmod($daily_rate_guesttype_1, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_1 format is incorrect');
        }
        if(fmod($daily_rate_guesttype_2, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_2 format is incorrect');
        }
        if(fmod($daily_rate_guesttype_3, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_3 format is incorrect');
        }
        if(fmod($daily_rate_guesttype_4, 1) !== 0.00) {
            return $this->notifyError('daily_rate_guesttype_4 format is incorrect');
        }
        if(!ctype_digit((string)$daily_rate_minstay)) {
            return $this->notifyError('daily_rate_minstay format is incorrect');
        }
        if(!ctype_digit((string)$daily_rate_maxstay)) {
            return $this->notifyError('daily_rate_maxstay format is incorrect');
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
        if(!$rate_calendar_model->update($daily_rate_id, $rate_calendar_data)) {
            return $this->notifyError('Failed update', 'failed_update', 'rate_calendar');
        }
        return $this->respond([
            'id'        => $daily_rate_id,
            'message'   => 'Successfully updated.'
        ]);
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
