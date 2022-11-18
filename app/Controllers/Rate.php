<?php

namespace App\Controllers;

use App\Models\RateLangModel;
use App\Models\RateMappingModel;
use App\Models\RateModel;
use App\Controllers\APIBaseController;
use CodeIgniter\API\ResponseTrait;
use DateTime;

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
        return $this->respond($rate_data == null ? [] : $rate_data, 200);
	}

    /**
     * Update a model resource
     * PUT/baseratesettings/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Load Rate relation Models */
        $rate_model = new RateModel();
        $rate_mapping_model = new RateMappingModel();
        $rate_lang_model = new RateLangModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'rate_id'               => 'required',
            'rate_setting'          => 'required',
            'rate_discount_markup'  => 'required',
            'rate_guests_included'  => 'required',
            'rate_downpayment'      => 'required',
            'rates_mapping'         => 'required',
            'rates_lang'            => 'required',
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        /* Getting data from raw */
        $rate_id   = $this->request->getVar('rate_id');
        $rate_setting = $this->request->getVar('rate_setting');
        $rate_discount_markup  = $this->request->getVar('rate_discount_markup');
        $rate_guests_included  = $this->request->getVar('rate_guests_included');
        $rate_downpayment  = $this->request->getVar('rate_downpayment');
        $rates_mapping  = $this->request->getVar('rates_mapping');
        $rates_lang  = $this->request->getVar('rates_lang');

        /* Format validation */
        if(!ctype_digit((string)$rate_id)) {
            return $this->respond([
                'Int format error' => 'rate_id format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$rate_setting)) {
            return $this->respond([
                'Int format error' => 'rate_setting format is incorrect.'
            ]);
        }
        if(!($rate_setting == 1 || $rate_setting == 2)) {
            return $this->respond([
                'Format error' => 'rate_setting must be 1 or 2.'
            ]);
        }
        if(fmod($rate_discount_markup, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'rate_discount_markup format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$rate_downpayment)) {
            return $this->respond([
                'Int format error' => 'rate_downpayment format is incorrect.'
            ]);
        }
        if(!($rate_downpayment == 1 || $rate_downpayment == 2)) {
            return $this->respond([
                'Format error' => 'rate_downpayment must be 1 or 2.'
            ]);
        }
        if(!ctype_digit((string)$rate_guests_included)) {
            return $this->respond([
                'Int format error' => 'rate_guests_included format is incorrect.'
            ]);
        }
        if(!is_array($rates_mapping)) {
            return $this->respond([
                'Format error' => 'rates_mapping format must be array.'
            ]);
        }
        if(!is_array($rates_lang)) {
            return $this->respond([
                'Format error' => 'rates_lang format must be array.'
            ]);
        }

        /* Update data in DB */
        /** Rate Model management */
        $rate_data = [
            'rate_host_id'          => $host_id,
            'rate_setting'          => $rate_setting,
            'rate_discount_markup'  => $rate_discount_markup,
            'rate_guests_included'  => $rate_guests_included,
            'rate_downpayment'      => $rate_downpayment,
        ];
        if(!$rate_model->update($rate_id, $rate_data)) {
            return $this->respond([
                'Failed message' => 'Failed update'
            ]);
        }
        /** Rate mapping Model management */
        $is_existed_data = $rate_mapping_model->is_existed_data($host_id, $rate_id);
        if($is_existed_data != null) {
            if($rate_mapping_model->delete_by($rate_id, $host_id) == false) {
                return $this->respond([
                    'Error' => 'Failed Mapping data delete'
                ]);
            }
        }
        foreach($rates_mapping as $mapping_item) {
            $rate_mapping_data = [
                'rate_mapping_host_id'                  => $host_id,
                'rate_mapping_rates_id'                 => $rate_id,
                'rate_mapping_type_code'                => $mapping_item->rate_mapping_type_code,
                'rate_mapping_dowpayment_percentage'    => $mapping_item->rate_mapping_dowpayment_percentage,
                'rate_mapping_alt_fixed_price'          => $mapping_item->rate_mapping_alt_fixed_price
            ];
            if(!$rate_mapping_model->insert($rate_mapping_data)) {
                return $this->respond([
                    'Error' => 'Failed Mapping data insert'
                ]);
            }
        }

        /** Rate lang Model management */
        $is_existed_data = $rate_lang_model->is_existed_data($host_id, $rate_id);
        if($is_existed_data != null) {
            if($rate_lang_model->delete_by($rate_id, $host_id) == false) {
                return $this->respond([
                    'Error' => 'Failed Lang data delete'
                ]);
            }
        }
        foreach($rates_lang as $lang_item) {
            $rate_lang_data = [
                'rate_lang_host_id'         => $host_id,
                'rate_lang_code'            => $rate_id,
                'rate_lang_rules'           => $lang_item->rate_lang_rules,
                'rate_name'                 => $lang_item->rate_name,
                'rate_short_description'    => $lang_item->rate_short_description,
                'rates_lang'                => $lang_item->rates_lang
            ];
            if(!$rate_lang_model->insert($rate_lang_data)) {
                return $this->respond([
                    'Error' => 'Failed Lang data insert'
                ]);
            }
        }
        return $this->respond([
            'Success' => 'rate_id:' . $rate_id . ' Successfully updated'
        ]);
    }

    /**
     * Create a model resource
     * POST/baseratesettings/add
     * @return mixed
     */
    public function create()
    {
        /* Load Rate relation Models */
        $rate_model = new RateModel();
        $rate_mapping_model = new RateMappingModel();
        $rate_lang_model = new RateLangModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'rate_setting'          => 'required',
            'rate_discount_markup'  => 'required',
            'rate_guests_included'  => 'required',
            'rate_downpayment'      => 'required',
            'rates_mapping'         => 'required',
            'rates_lang'            => 'required',
        ];
        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        /* Getting data from raw */
        $rate_setting = $this->request->getVar('rate_setting');
        $rate_discount_markup  = $this->request->getVar('rate_discount_markup');
        $rate_guests_included  = $this->request->getVar('rate_guests_included');
        $rate_downpayment  = $this->request->getVar('rate_downpayment');
        $rates_mapping  = $this->request->getVar('rates_mapping');
        $rates_lang  = $this->request->getVar('rates_lang');

        /* Format validation */
        if(!ctype_digit((string)$rate_setting)) {
            return $this->respond([
                'Int format error' => 'rate_setting format is incorrect.'
            ]);
        }
        if(!($rate_setting == 1 || $rate_setting == 2)) {
            return $this->respond([
                'Format error' => 'rate_setting must be 1 or 2.'
            ]);
        }
        if(fmod($rate_discount_markup, 1) !== 0.00) {
            return $this->respond([
                'Int format error' => 'rate_discount_markup format is incorrect.'
            ]);
        }
        if(!ctype_digit((string)$rate_downpayment)) {
            return $this->respond([
                'Int format error' => 'rate_downpayment format is incorrect.'
            ]);
        }
        if(!($rate_downpayment == 1 || $rate_downpayment == 2)) {
            return $this->respond([
                'Format error' => 'rate_downpayment must be 1 or 2.'
            ]);
        }
        if(!ctype_digit((string)$rate_guests_included)) {
            return $this->respond([
                'Int format error' => 'rate_guests_included format is incorrect.'
            ]);
        }
        if(!is_array($rates_mapping)) {
            return $this->respond([
                'Format error' => 'rates_mapping format must be array.'
            ]);
        }
        if(!is_array($rates_lang)) {
            return $this->respond([
                'Format error' => 'rates_lang format must be array.'
            ]);
        }

        /* Insert data in DB */
        /** Insert into Rate Model */
        $rate_data = [
            'rate_host_id'          => $host_id,
            'rate_setting'          => $rate_setting,
            'rate_discount_markup'  => $rate_discount_markup,
            'rate_guests_included'  => $rate_guests_included,
            'rate_downpayment'      => $rate_downpayment,
        ];
        $new_id = $rate_model->insert($rate_data);
        if(!$new_id) {
            return $this->respond([
                'Failed message' => 'Failed insert'
            ]);
        }
        /** Insert into Rate Mapping Model */
        foreach($rates_mapping as $mapping_item) {
            $rate_mapping_data = [
                'rate_mapping_host_id'                  => $host_id,
                'rate_mapping_rates_id'                 => $new_id,
                'rate_mapping_type_code'                => $mapping_item->rate_mapping_type_code,
                'rate_mapping_dowpayment_percentage'    => $mapping_item->rate_mapping_dowpayment_percentage,
                'rate_mapping_alt_fixed_price'          => $mapping_item->rate_mapping_alt_fixed_price
            ];
            if(!$rate_mapping_model->insert($rate_mapping_data)) {
                return $this->respond([
                    'Error' => 'Failed Mapping data insert'
                ]);
            }
        }

        /** Insert into Rate Lang Model */
        foreach($rates_lang as $lang_item) {
            $rate_lang_data = [
                'rate_lang_host_id'         => $host_id,
                'rate_lang_code'            => $new_id,
                'rate_lang_rules'           => $lang_item->rate_lang_rules,
                'rate_name'                 => $lang_item->rate_name,
                'rate_short_description'    => $lang_item->rate_short_description,
                'rates_lang'                => $lang_item->rates_lang
            ];
            if(!$rate_lang_model->insert($rate_lang_data)) {
                return $this->respond([
                    'Error' => 'Failed Lang data insert'
                ]);
            }
        }
        return $this->respond([
            'Success' => 'rate_id:' . $new_id . ' Successfully created'
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
