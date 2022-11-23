<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\RateLangModel;
use App\Models\RateMappingModel;
use App\Models\RateModel;
use CodeIgniter\API\ResponseTrait;

class Rate extends APIBaseController
{
    /**
     * Return an array of Rate
     * GET/baseratesettings
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        /* Load Rate Model */
        $rate_model = new RateModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Getting rate data from Rate Model */
        $rate_data = $rate_model->get_rate_data($host_id);
        return $this->respond([
            'rate_data' =>
                $rate_data == null ? [] : $rate_data,
        ]);
    }

    /**
     * Update a model resource
     * PUT/baseratesettings/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Load necessary Models */
        $rate_model = new RateModel();
        $rate_mapping_model = new RateMappingModel();
        $rate_lang_model = new RateLangModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'rate_id' => 'required',
            'rate_setting' => 'required',
            'rate_discount_markup' => 'required',
            'rate_guests_included' => 'required',
            'rate_downpayment' => 'required',
            'rate_from_checkin' => 'required',
            'rate_status' => 'required',
            'rates_mapping' => 'required',
            'rates_lang' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->fail(
                $this->validator->getErrors()
            );
        }

        /* Getting request data */
        $rate_id = $this->request->getVar('rate_id');
        $rate_setting = $this->request->getVar(
            'rate_setting'
        );
        $rate_discount_markup = $this->request->getVar(
            'rate_discount_markup'
        );
        $rate_guests_included = $this->request->getVar(
            'rate_guests_included'
        );
        $rate_downpayment = $this->request->getVar(
            'rate_downpayment'
        );
        $rate_from_checkin = $this->request->getVar(
            'rate_from_checkin'
        );
        $rate_status = $this->request->getVar(
            'rate_status'
        );
        $rates_mapping = $this->request->getVar(
            'rates_mapping'
        );
        $rates_lang = $this->request->getVar('rates_lang');

        /* Format validation */
        if (!ctype_digit((string) $rate_id)) {
            return $this->notifyError(
                'id format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (
            !ctype_digit((string) $rate_setting) ||
            !($rate_setting == 1 || $rate_setting == 2)
        ) {
            return $this->notifyError(
                'setting format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (fmod($rate_discount_markup, 1) !== 0.0) {
            return $this->notifyError(
                'discount_markup format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_downpayment)) {
            return $this->notifyError(
                'rate_downpayment format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (
            !(
                $rate_downpayment == 1 ||
                $rate_downpayment == 2
            )
        ) {
            return $this->notifyError(
                'rate_downpayment must be 1 or 2.',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_guests_included)) {
            return $this->notifyError(
                'rate_guests_included format is incorrect.',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_from_checkin)) {
            return $this->notifyError(
                'rate_from_checkin format is incorrect.',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_status)) {
            return $this->notifyError(
                'rate_status format is incorrect.',
                'invalid_data',
                'rate'
            );
        }
        if ($rate_status > 4) {
            return $this->notifyError(
                'status value must be equal or smaller than 4',
                'invalid_data',
                'rate'
            );
        }
        if (!is_array($rates_mapping)) {
            return $this->notifyError(
                'rates_mapping must be array',
                'invalid_data',
                'rate'
            );
        }
        if (!is_array($rates_lang)) {
            return $this->notifyError(
                'rates_lang must be array',
                'invalid_data',
                'rate'
            );
        }

        /* Update data in DB */
        /** Rate Model management */
        $rate_data = [
            'rate_host_id' => $host_id,
            'rate_setting' => $rate_setting,
            'rate_discount_markup' => $rate_discount_markup,
            'rate_guests_included' => $rate_guests_included,
            'rate_downpayment' => $rate_downpayment,
            'rate_from_checkin' => $rate_from_checkin,
            'rate_status' => $rate_status,
        ];
        if (!$rate_model->update($rate_id, $rate_data)) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'rate'
            );
        }
        /** Rate mapping Model management */
        if ($rates_mapping != null) {
            if (
                $rate_mapping_model
                    ->where([
                        'rate_mapping_host_id' => $host_id,
                        'rate_mapping_rates_id' => $rate_id,
                    ])
                    ->findAll() != null
            ) {
                if (
                    !$rate_mapping_model->delete_by(
                        $rate_id,
                        $host_id
                    )
                ) {
                    return $this->notifyError(
                        'Failed update',
                        'failed_update',
                        'rate'
                    );
                }
            }
            foreach ($rates_mapping as $mapping_item) {
                $rate_mapping_data = [
                    'rate_mapping_host_id' => $host_id,
                    'rate_mapping_rates_id' => $rate_id,
                    'rate_mapping_type_code' =>
                        $mapping_item->rate_mapping_type_code,
                    'rate_mapping_dowpayment_percentage' =>
                        $mapping_item->rate_mapping_dowpayment_percentage,
                    'rate_mapping_alt_fixed_price' =>
                        $mapping_item->rate_mapping_alt_fixed_price,
                ];
                if (
                    !$rate_mapping_model->insert(
                        $rate_mapping_data
                    )
                ) {
                    return $this->notifyError(
                        'Failed update',
                        'failed_update',
                        'rate'
                    );
                }
            }
        }

        /** Rate lang Model management */
        if ($rates_lang != null) {
            if (
                $rate_lang_model
                    ->where([
                        'rate_lang_host_id' => $host_id,
                        'rate_lang_code' => $rate_id,
                    ])
                    ->findAll() != null
            ) {
                if (
                    !$rate_lang_model->delete_by(
                        $rate_id,
                        $host_id
                    )
                ) {
                    return $this->notifyError(
                        'Failed update',
                        'failed_update',
                        'rate'
                    );
                }
            }
            foreach ($rates_lang as $lang_item) {
                $rate_lang_data = [
                    'rate_lang_host_id' => $host_id,
                    'rate_lang_code' => $rate_id,
                    'rate_lang_rules' =>
                        $lang_item->rate_lang_rules,
                    'rate_name' => $lang_item->rate_name,
                    'rate_short_description' =>
                        $lang_item->rate_short_description,
                    'rates_lang' => $lang_item->rates_lang,
                ];
                if (
                    !$rate_lang_model->insert(
                        $rate_lang_data
                    )
                ) {
                    return $this->notifyError(
                        'Failed update',
                        'failed_update',
                        'rate'
                    );
                }
            }
        }

        return $this->respond([
            'id' => $rate_id,
            'message' => 'Successfully updated',
        ]);
    }

    /**
     * Create a model resource
     * POST/baseratesettings/add
     * @return mixed
     */
    public function create()
    {
        /* Load necessary Models */
        $rate_model = new RateModel();
        $rate_mapping_model = new RateMappingModel();
        $rate_lang_model = new RateLangModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'rate_setting' => 'required',
            'rate_discount_markup' => 'required',
            'rate_guests_included' => 'required',
            'rate_downpayment' => 'required',
            'rate_from_checkin' => 'required',
            'rate_status' => 'required',
            'rates_mapping' => 'required',
            'rates_lang' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'rate'
            );
        }

        /* Getting request data */
        $rate_setting = $this->request->getVar(
            'rate_setting'
        );
        $rate_discount_markup = $this->request->getVar(
            'rate_discount_markup'
        );
        $rate_guests_included = $this->request->getVar(
            'rate_guests_included'
        );
        $rate_downpayment = $this->request->getVar(
            'rate_downpayment'
        );
        $rate_from_checkin = $this->request->getVar(
            'rate_from_checkin'
        );
        $rate_status = $this->request->getVar(
            'rate_status'
        );
        $rates_mapping = $this->request->getVar(
            'rates_mapping'
        );
        $rates_lang = $this->request->getVar('rates_lang');

        /* Format validation */
        if (
            !ctype_digit((string) $rate_setting) ||
            !($rate_setting == 1 || $rate_setting == 2)
        ) {
            return $this->notifyError(
                'setting format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (fmod($rate_discount_markup, 1) !== 0.0) {
            return $this->notifyError(
                'discount_markup format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_downpayment)) {
            return $this->notifyError(
                'rate_downpayment format is incorrect',
                'invalid_data',
                'rate'
            );
        }
        if (
            !(
                $rate_downpayment == 1 ||
                $rate_downpayment == 2
            )
        ) {
            return $this->notifyError(
                'rate_downpayment must be 1 or 2.',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_guests_included)) {
            return $this->notifyError(
                'rate_guests_included format is incorrect.',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_from_checkin)) {
            return $this->notifyError(
                'rate_from_checkin format is incorrect.',
                'invalid_data',
                'rate'
            );
        }
        if (!ctype_digit((string) $rate_status)) {
            return $this->notifyError(
                'rate_status format is incorrect.',
                'invalid_data',
                'rate'
            );
        }
        if ($rate_status > 4) {
            return $this->notifyError(
                'status value must be equal or smaller than 4',
                'invalid_data',
                'rate'
            );
        }
        if (!is_array($rates_mapping)) {
            return $this->notifyError(
                'rates_mapping must be array',
                'invalid_data',
                'rate'
            );
        }
        if (!is_array($rates_lang)) {
            return $this->notifyError(
                'rates_lang must be array',
                'invalid_data',
                'rate'
            );
        }

        /* Update data in DB */
        /** Rate Model management */
        if (
            $rate_model
                ->where([
                    'rate_host_id' => $host_id,
                    'rate_setting' => $rate_setting,
                    'rate_discount_markup' => $rate_discount_markup,
                    'rate_guests_included' => $rate_guests_included,
                    'rate_downpayment' => $rate_downpayment,
                    'rate_from_checkin' => $rate_from_checkin,
                ])
                ->findAll() != null
        ) {
            return $this->notifyError(
                'Duplication error',
                'duplicate',
                'rate'
            );
        }
        $rate_data = [
            'rate_host_id' => $host_id,
            'rate_setting' => $rate_setting,
            'rate_discount_markup' => $rate_discount_markup,
            'rate_guests_included' => $rate_guests_included,
            'rate_downpayment' => $rate_downpayment,
            'rate_from_checkin' => $rate_from_checkin,
            'rate_status' => $rate_status,
        ];
        $new_id = $rate_model->insert($rate_data);
        if (!$new_id) {
            return $this->notifyError(
                'Failed create',
                'failed_create',
                'rate'
            );
        }
        /** Insert into Rate Mapping Model */
        foreach ($rates_mapping as $mapping_item) {
            $rate_mapping_data = [
                'rate_mapping_host_id' => $host_id,
                'rate_mapping_rates_id' => $new_id,
                'rate_mapping_type_code' =>
                    $mapping_item->rate_mapping_type_code,
                'rate_mapping_dowpayment_percentage' =>
                    $mapping_item->rate_mapping_dowpayment_percentage,
                'rate_mapping_alt_fixed_price' =>
                    $mapping_item->rate_mapping_alt_fixed_price,
            ];
            if (
                !$rate_mapping_model->insert(
                    $rate_mapping_data
                )
            ) {
                $rate_model->delete($new_id);
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'rate'
                );
            }
        }

        /** Insert into Rate Lang Model */
        foreach ($rates_lang as $lang_item) {
            $rate_lang_data = [
                'rate_lang_host_id' => $host_id,
                'rate_lang_code' => $new_id,
                'rate_lang_rules' =>
                    $lang_item->rate_lang_rules,
                'rate_name' => $lang_item->rate_name,
                'rate_short_description' =>
                    $lang_item->rate_short_description,
                'rates_lang' => $lang_item->rates_lang,
            ];
            if (
                !$rate_lang_model->insert($rate_lang_data)
            ) {
                $rate_model->delete($new_id);
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'rate'
                );
            }
        }
        return $this->respond([
            'id' => $new_id,
            'message' => 'Successfully created',
        ]);
    }

    /**
     * Delete Rate content
     * DELETE /baseratesettings/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        /* Validate */
        if (
            !$this->validate([
                'rate_id' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'rate'
            );
        }

        /* Getting request data */
        $rate_id = $this->request->getVar('rate_id');
        $rate_model = new RateModel();

        /* Delete with status=4 */
        if ($rate_model->find($rate_id) == null) {
            return $this->notifyError(
                'No Such Data',
                'notFound',
                'rate'
            );
        }
        if (
            !$rate_model->update($rate_id, [
                'rate_status' => 4,
            ])
        ) {
            return $this->notifyError(
                'Failed delete',
                'failed_delete',
                'rate'
            );
        }
        return $this->respond([
            'id' => $rate_id,
            'message' => 'Successfully Deleted',
        ]);
    }
}
