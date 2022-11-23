<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\RateCalendarModel;
use App\Models\RateModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class RateCalendar extends APIBaseController
{
    /**
     * Return an array of Rate calendar
     * GET/baseratesettings
     * @return mixed
     */
    use ResponseTrait;

    /**
     * Update a model resource
     * PUT/baseratesettings/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Load Rate relation Models */
        $rate_calendar_model = new RateCalendarModel();
        $rate_model = new RateModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'daily_rate_code' => 'required',
            'daily_rate_type' => 'required',
            'daily_rate_day' => 'required',
            'daily_rate_baserate' => 'required',
            'daily_rate_guesttype_1' => 'required',
            'daily_rate_guesttype_2' => 'required',
            'daily_rate_guesttype_3' => 'required',
            'daily_rate_guesttype_4' => 'required',
            'daily_rate_minstay' => 'required',
            'daily_rate_maxstay' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'rate_calendar'
            );
        }

        /* Getting data from raw */
        $daily_rate_code = $this->request->getVar(
            'daily_rate_code'
        );
        $daily_rate_type = $this->request->getVar(
            'daily_rate_type'
        );
        $daily_rate_day = $this->request->getVar(
            'daily_rate_day'
        );
        $daily_rate_baserate = $this->request->getVar(
            'daily_rate_baserate'
        );
        $daily_rate_guesttype_1 = $this->request->getVar(
            'daily_rate_guesttype_1'
        );
        $daily_rate_guesttype_2 = $this->request->getVar(
            'daily_rate_guesttype_2'
        );
        $daily_rate_guesttype_3 = $this->request->getVar(
            'daily_rate_guesttype_3'
        );
        $daily_rate_guesttype_4 = $this->request->getVar(
            'daily_rate_guesttype_4'
        );
        $daily_rate_minstay = $this->request->getVar(
            'daily_rate_minstay'
        );
        $daily_rate_maxstay = $this->request->getVar(
            'daily_rate_maxstay'
        );

        /* Format validation */
        if (!ctype_digit((string) $daily_rate_code)) {
            return $this->notifyError(
                'daily_rate_code format is incorrect',
                'invalid_data',
                'rate_calendar'
            );
        }
        if ($rate_model->find($daily_rate_code) == null) {
            return $this->notifyError(
                'No Such daily_rate_code(rate_id)',
                'notFound',
                'rate_calendar'
            );
        }
        if (
            $type_mapping_model
                ->where([
                    'type_mapping_code' => $daily_rate_type,
                ])
                ->findAll() == null
        ) {
            return $this->notifyError(
                'No Such daily_rate_type(type_mapping_code).',
                'notFound',
                'rate_calendar'
            );
        }
        if (!$this->validateDate($daily_rate_day)) {
            return $this->notifyError(
                'daily_rate_day format is incorrect'
            );
        }
        if (
            new DateTime() > new DateTime($daily_rate_day)
        ) {
            return $this->notifyError(
                'daily_rate_day should be larger than today',
                'invalid_data',
                'availability'
            );
        }
        if (!is_numeric($daily_rate_baserate)) {
            return $this->notifyError(
                'daily_rate_baserate format is incorrect'
            );
        }
        if (!is_numeric($daily_rate_guesttype_1)) {
            return $this->notifyError(
                'daily_rate_guesttype_1 format is incorrect'
            );
        }
        if (!is_numeric($daily_rate_guesttype_2)) {
            return $this->notifyError(
                'daily_rate_guesttype_2 format is incorrect'
            );
        }
        if (!is_numeric($daily_rate_guesttype_3)) {
            return $this->notifyError(
                'daily_rate_guesttype_3 format is incorrect'
            );
        }
        if (!is_numeric($daily_rate_guesttype_4)) {
            return $this->notifyError(
                'daily_rate_guesttype_4 format is incorrect'
            );
        }
        if (!ctype_digit((string) $daily_rate_minstay)) {
            return $this->notifyError(
                'daily_rate_minstay format is incorrect'
            );
        }
        if (!ctype_digit((string) $daily_rate_maxstay)) {
            return $this->notifyError(
                'daily_rate_maxstay format is incorrect'
            );
        }
        /* Update data in DB */
        /** Rate Calendar Model management */

        // Check if data exist
        $matched_datum = $rate_calendar_model
            ->where([
                'rate_calendar_host_id' => $host_id,
                'daily_rate_code' => $daily_rate_code,
                'daily_rate_type' => $daily_rate_type,
                'daily_rate_day' => $daily_rate_day,
            ])
            ->findAll();

        // Update data
        $rate_calendar_data = [
            'rate_calendar_host_id' => $host_id,
            'daily_rate_code' => $daily_rate_code,
            'daily_rate_type' => $daily_rate_type,
            'daily_rate_day' => $daily_rate_day,
            'daily_rate_baserate' => $daily_rate_baserate,
            'daily_rate_guesttype_1' => $daily_rate_guesttype_1,
            'daily_rate_guesttype_2' => $daily_rate_guesttype_2,
            'daily_rate_guesttype_3' => $daily_rate_guesttype_3,
            'daily_rate_guesttype_4' => $daily_rate_guesttype_4,
            'daily_rate_minstay' => $daily_rate_minstay,
            'daily_rate_maxstay' => $daily_rate_maxstay,
        ];
        if ($matched_datum != null) {
            foreach ($matched_datum as $matched_data) {
                if (
                    !$rate_calendar_model->update(
                        $matched_data['daily_rate_id'],
                        $rate_calendar_data
                    )
                ) {
                    return $this->notifyError(
                        'Failed update',
                        'failed_update',
                        'rate_calendar'
                    );
                }
            }
        }
        if (
            $matched_datum == null &&
            !$rate_calendar_model->insert(
                $rate_calendar_data
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'rate_calendar'
            );
        }
        return $this->respond([
            'message' => 'Successfully updated.',
        ]);
    }
}
