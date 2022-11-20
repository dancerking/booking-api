<?php

namespace App\Controllers;

use App\Models\ServiceLangModel;
use App\Models\ServiceMappingModel;
use App\Models\ServiceModel;
use App\Controllers\APIBaseController;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class Service extends APIBaseController
{
    /**
     * Return an array of Service
     * GET/services
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        /* Load Service Model */
        $service_model = new ServiceModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Getting service data from Service Model */
        $service_data = $service_model->get_service_data(
            $host_id
        );
        return $this->respond([
            'service_data' =>
                $service_data == null ? [] : $service_data,
        ]);
    }

    /**
     * Update a model resource
     * PUT/services/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Load Service relation Models */
        $service_model = new ServiceModel();
        $service_mapping_model = new ServiceMappingModel();
        $service_lang_model = new ServiceLangModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'service_id' => 'required',
            'service_setting' => 'required',
            'service_discount_markup' => 'required',
            'service_guests_included' => 'required',
            'service_downpayment' => 'required',
            'service_from_checkin' => 'required',
            'service_status' => 'required',
            'services_mapping' => 'required',
            'services_lang' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->fail(
                $this->validator->getErrors()
            );
        }

        /* Getting data from raw */
        $service_id = $this->request->getVar('service_id');
        $service_setting = $this->request->getVar(
            'service_setting'
        );
        $service_discount_markup = $this->request->getVar(
            'service_discount_markup'
        );
        $service_guests_included = $this->request->getVar(
            'service_guests_included'
        );
        $service_downpayment = $this->request->getVar(
            'service_downpayment'
        );
        $service_from_checkin = $this->request->getVar(
            'service_from_checkin'
        );
        $service_status = $this->request->getVar(
            'service_status'
        );
        $services_mapping = $this->request->getVar(
            'services_mapping'
        );
        $services_lang = $this->request->getVar(
            'services_lang'
        );

        /* Format validation */
        if (!ctype_digit((string) $service_id)) {
            return $this->notifyError(
                'id format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            !ctype_digit((string) $service_setting) ||
            !(
                $service_setting == 1 ||
                $service_setting == 2
            )
        ) {
            return $this->notifyError(
                'setting format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (fmod($service_discount_markup, 1) !== 0.0) {
            return $this->notifyError(
                'discount_markup format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_downpayment)) {
            return $this->notifyError(
                'service_downpayment format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            !(
                $service_downpayment == 1 ||
                $service_downpayment == 2
            )
        ) {
            return $this->notifyError(
                'service_downpayment must be 1 or 2.',
                'invalid_data',
                'service'
            );
        }
        if (
            !ctype_digit((string) $service_guests_included)
        ) {
            return $this->notifyError(
                'service_guests_included format is incorrect.',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_from_checkin)) {
            return $this->notifyError(
                'service_from_checkin format is incorrect.',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_status)) {
            return $this->notifyError(
                'service_status format is incorrect.',
                'invalid_data',
                'service'
            );
        }
        if ($service_status > 4) {
            return $this->notifyError(
                'status value must be equal or smaller than 4',
                'invalid_data',
                'service'
            );
        }
        if (!is_array($services_mapping)) {
            return $this->notifyError(
                'services_mapping must be array',
                'invalid_data',
                'service'
            );
        }
        if (!is_array($services_lang)) {
            return $this->notifyError(
                'services_lang must be array',
                'invalid_data',
                'service'
            );
        }

        /* Update data in DB */
        /** Service Model management */
        $service_data = [
            'service_host_id' => $host_id,
            'service_setting' => $service_setting,
            'service_discount_markup' => $service_discount_markup,
            'service_guests_included' => $service_guests_included,
            'service_downpayment' => $service_downpayment,
            'service_from_checkin' => $service_from_checkin,
            'service_status' => $service_status,
        ];
        if (
            !$service_model->update(
                $service_id,
                $service_data
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'service'
            );
        }
        /** Service mapping Model management */
        $is_existed_data = $service_mapping_model->is_existed_data(
            $host_id,
            $service_id
        );
        if ($is_existed_data != null) {
            if (
                !$service_mapping_model->delete_by(
                    $service_id,
                    $host_id
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'service'
                );
            }
        }
        foreach ($services_mapping as $mapping_item) {
            $service_mapping_data = [
                'service_mapping_host_id' => $host_id,
                'service_mapping_services_id' => $service_id,
                'service_mapping_type_code' =>
                    $mapping_item->service_mapping_type_code,
                'service_mapping_dowpayment_percentage' =>
                    $mapping_item->service_mapping_dowpayment_percentage,
                'service_mapping_alt_fixed_price' =>
                    $mapping_item->service_mapping_alt_fixed_price,
            ];
            if (
                !$service_mapping_model->insert(
                    $service_mapping_data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'service'
                );
            }
        }

        /** Service lang Model management */
        $is_existed_data = $service_lang_model->is_existed_data(
            $host_id,
            $service_id
        );
        if ($is_existed_data != null) {
            if (
                !$service_lang_model->delete_by(
                    $service_id,
                    $host_id
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'service'
                );
            }
        }
        foreach ($services_lang as $lang_item) {
            $service_lang_data = [
                'service_lang_host_id' => $host_id,
                'service_lang_code' => $service_id,
                'service_lang_rules' =>
                    $lang_item->service_lang_rules,
                'service_name' => $lang_item->service_name,
                'service_short_description' =>
                    $lang_item->service_short_description,
                'services_lang' =>
                    $lang_item->services_lang,
            ];
            if (
                !$service_lang_model->insert(
                    $service_lang_data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'service'
                );
            }
        }
        return $this->respond([
            'id' => $service_id,
            'message' => 'Successfully updated',
        ]);
    }

    /**
     * Create a model resource
     * POST/services/add
     * @return mixed
     */
    public function create()
    {
        /* Load Service relation Models */
        $service_model = new ServiceModel();
        $service_mapping_model = new ServiceMappingModel();
        $service_lang_model = new ServiceLangModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'service_setting' => 'required',
            'service_discount_markup' => 'required',
            'service_guests_included' => 'required',
            'service_downpayment' => 'required',
            'service_from_checkin' => 'required',
            'service_status' => 'required',
            'services_mapping' => 'required',
            'services_lang' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'service'
            );
        }

        /* Getting data from raw */
        $service_setting = $this->request->getVar(
            'service_setting'
        );
        $service_discount_markup = $this->request->getVar(
            'service_discount_markup'
        );
        $service_guests_included = $this->request->getVar(
            'service_guests_included'
        );
        $service_downpayment = $this->request->getVar(
            'service_downpayment'
        );
        $service_from_checkin = $this->request->getVar(
            'service_from_checkin'
        );
        $service_status = $this->request->getVar(
            'service_status'
        );
        $services_mapping = $this->request->getVar(
            'services_mapping'
        );
        $services_lang = $this->request->getVar(
            'services_lang'
        );

        /* Format validation */
        if (
            !ctype_digit((string) $service_setting) ||
            !(
                $service_setting == 1 ||
                $service_setting == 2
            )
        ) {
            return $this->notifyError(
                'setting format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (fmod($service_discount_markup, 1) !== 0.0) {
            return $this->notifyError(
                'discount_markup format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_downpayment)) {
            return $this->notifyError(
                'service_downpayment format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            !(
                $service_downpayment == 1 ||
                $service_downpayment == 2
            )
        ) {
            return $this->notifyError(
                'service_downpayment must be 1 or 2.',
                'invalid_data',
                'service'
            );
        }
        if (
            !ctype_digit((string) $service_guests_included)
        ) {
            return $this->notifyError(
                'service_guests_included format is incorrect.',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_from_checkin)) {
            return $this->notifyError(
                'service_from_checkin format is incorrect.',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_status)) {
            return $this->notifyError(
                'service_status format is incorrect.',
                'invalid_data',
                'service'
            );
        }
        if ($service_status > 4) {
            return $this->notifyError(
                'status value must be equal or smaller than 4',
                'invalid_data',
                'service'
            );
        }
        if (!is_array($services_mapping)) {
            return $this->notifyError(
                'services_mapping must be array',
                'invalid_data',
                'service'
            );
        }
        if (!is_array($services_lang)) {
            return $this->notifyError(
                'services_lang must be array',
                'invalid_data',
                'service'
            );
        }

        /* Update data in DB */
        /** Service Model management */
        $service_data = [
            'service_host_id' => $host_id,
            'service_setting' => $service_setting,
            'service_discount_markup' => $service_discount_markup,
            'service_guests_included' => $service_guests_included,
            'service_downpayment' => $service_downpayment,
            'service_from_checkin' => $service_from_checkin,
            'service_status' => $service_status,
        ];
        $new_id = $service_model->insert($service_data);
        if (!$new_id) {
            return $this->notifyError(
                'Failed create',
                'failed_create',
                'service'
            );
        }
        /** Insert into Service Mapping Model */
        foreach ($services_mapping as $mapping_item) {
            $service_mapping_data = [
                'service_mapping_host_id' => $host_id,
                'service_mapping_services_id' => $new_id,
                'service_mapping_type_code' =>
                    $mapping_item->service_mapping_type_code,
                'service_mapping_dowpayment_percentage' =>
                    $mapping_item->service_mapping_dowpayment_percentage,
                'service_mapping_alt_fixed_price' =>
                    $mapping_item->service_mapping_alt_fixed_price,
            ];
            if (
                !$service_mapping_model->insert(
                    $service_mapping_data
                )
            ) {
                $service_model->delete($new_id);
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'service'
                );
            }
        }

        /** Insert into Service Lang Model */
        foreach ($services_lang as $lang_item) {
            $service_lang_data = [
                'service_lang_host_id' => $host_id,
                'service_lang_code' => $new_id,
                'service_lang_rules' =>
                    $lang_item->service_lang_rules,
                'service_name' => $lang_item->service_name,
                'service_short_description' =>
                    $lang_item->service_short_description,
                'services_lang' =>
                    $lang_item->services_lang,
            ];
            if (
                !$service_lang_model->insert(
                    $service_lang_data
                )
            ) {
                $service_model->delete($new_id);
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'service'
                );
            }
        }
        return $this->respond([
            'id' => $new_id,
            'message' => 'Successfully created',
        ]);
    }

    /**
     * Delete Service content
     * DELETE /services/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        $host_id = $this->get_host_id();
        if (
            !$this->validate([
                'service_id' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service'
            );
        }
        $service_id = $this->request->getVar('service_id');
        $service_model = new ServiceModel();
        $check_id_exist = $service_model->is_existed_id(
            $service_id
        );
        if ($check_id_exist == null) {
            return $this->notifyError(
                'No Such Data',
                'notFound',
                'service'
            );
        }
        $service_mapping_model = new ServiceMappingModel();
        $service_lang_model = new ServiceLangModel();
        if (!$service_model->delete($service_id)) {
            return $this->notifyError(
                'Failed delete',
                'failed_delete',
                'service'
            );
        }
        if (
            !$service_mapping_model->delete_by(
                $service_id,
                $host_id
            )
        ) {
            return $this->notifyError(
                'Failed mapping data delete',
                'failed_delete',
                'service'
            );
        }
        if (
            !$service_lang_model->delete_by(
                $service_id,
                $host_id
            )
        ) {
            return $this->notifyError(
                'Failed lang data delete',
                'failed_delete',
                'service'
            );
        }
        return $this->respond([
            'id' => $service_id,
            'message' => 'Successfully Deleted',
        ]);
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function is_decimal($val)
    {
        return is_numeric($val) && floor($val) != $val;
    }
}
