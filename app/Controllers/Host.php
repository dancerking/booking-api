<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\CityModel;
use App\Models\HostAgreementModel;
use App\Models\HostLangModel;
use App\Models\HostModel;
use App\Models\LanguageModel;
use App\Models\TypeMainModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class Host extends APIBaseController
{
    /**
     * Return an array of objects
     * GET/hosts
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();
        if ($user_level != $config->USER_LEVELS['admin']) {
            return $this->notifyError(
                'Not allowed access',
                'notAllowedAccess',
                'host'
            );
        }
        // Load necessary Model
        $host_model = new HostModel();
        /* Validate */
        if (
            !$this->validate([
                'host_status' =>
                    'required|regex_match[/[01234]/]',
                'host_region_residence' => 'required',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_status = $this->request->getVar(
            'host_status'
        );
        $host_region_residence = $this->request->getVar(
            'host_region_residence'
        );

        /* Getting data from db*/
        $host_list = $host_model
            ->select(
                'host_id, host_referral_lang AS host_lang_name, host_company_name, host_city_residence, host_region_residence, host_referral_phone, host_referral_email, host_status'
            )
            ->where([
                'host_status' => $host_status,
                'host_region_residence' => $host_region_residence,
            ])
            ->findAll();
        return parent::respond([
            'host_list' =>
                $host_list == null ? [] : $host_list,
        ]);
    }

    /**
     * Return an array of objects
     * GET/host/main
     * @return mixed
     */
    public function main()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $host_agreement_model = new HostAgreementModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        /* Getting data from db*/
        $host_list = $host_model
            ->select(
                'host_category_code, host_sub_category_code, host_referral_surname, host_referral_name, host_referral_phone, host_mobile_phone, host_referral_email, host_company_name, host_city_residence, host_address_residence, host_postcode_residence, host_province_residence, host_region_residence, host_iso_state_residence'
            )
            ->find($host_id);
        $host_agreements = $host_agreement_model
            ->where('host_agreement_host_id', $host_id)
            ->findAll();
        $host_list['hosts_agreement'] = $host_agreements;
        return parent::respond([
            'main_host' =>
                $host_list == null ? [] : $host_list,
        ]);
    }

    /**
     * Update
     * PUT/host/main/update
     * @return mixed
     */
    public function main_update()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $host_agreement_model = new HostAgreementModel();
        $city_model = new CityModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'host_referral_phone' =>
                    'required|numeric|max_length[10]',
                'host_mobile_phone' =>
                    'required|numeric|max_length[10]',
                'host_referral_email' =>
                    'required|valid_email',
                'host_company_name' => 'required',
                'host_city_residence' => 'required',
                'host_address_residence' => 'required',
                'host_postcode_residence' => 'required',
                'host_province_residence' => 'required',
                'host_region_residence' => 'required',
                'host_iso_state_residence' => 'required',
                'hosts_agreement' => 'required',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $host_category_code = $this->request->getVar(
            'host_category_code'
        );
        $host_sub_category_code = $this->request->getVar(
            'host_sub_category_code'
        );
        $host_referral_surname = $this->request->getVar(
            'host_referral_surname'
        );
        $host_referral_name = $this->request->getVar(
            'host_referral_name'
        );
        $host_referral_phone = $this->request->getVar(
            'host_referral_phone'
        );
        $host_mobile_phone = $this->request->getVar(
            'host_mobile_phone'
        );
        $host_referral_email = $this->request->getVar(
            'host_referral_email'
        );
        $host_company_name = $this->request->getVar(
            'host_company_name'
        );
        $host_city_residence = $this->request->getVar(
            'host_city_residence'
        );
        $host_address_residence = $this->request->getVar(
            'host_address_residence'
        );
        $host_postcode_residence = $this->request->getVar(
            'host_postcode_residence'
        );
        $host_province_residence = $this->request->getVar(
            'host_province_residence'
        );
        $host_region_residence = $this->request->getVar(
            'host_region_residence'
        );
        $host_iso_state_residence = $this->request->getVar(
            'host_iso_state_residence'
        );
        $hosts_agreement = $this->request->getVar(
            'hosts_agreement'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        if (
            $host_category_code != null &&
            !ctype_digit((string) $host_category_code)
        ) {
            return $this->notifyError(
                'host_category_code format is incorrect',
                'invalid_data',
                'host'
            );
        }
        if (
            $host_sub_category_code != null &&
            !ctype_digit((string) $host_sub_category_code)
        ) {
            return $this->notifyError(
                'host_sub_category_code format is incorrect',
                'invalid_data',
                'host'
            );
        }
        // validattion for agreement data
        if (
            !isset($hosts_agreement->host_agreement_privacy)
        ) {
            return $this->notifyError(
                'host_agreement_privacy should be setted',
                'invalid_data',
                'host'
            );
        }
        if (
            !isset($hosts_agreement->host_agreement_rules)
        ) {
            return $this->notifyError(
                'host_agreement_rules should be setted',
                'invalid_data',
                'host'
            );
        }
        if (
            !$this->validateDate(
                $hosts_agreement->host_agreement_privacy
            ) ||
            !$this->validateDate(
                $hosts_agreement->host_agreement_rules
            ) ||
            ($hosts_agreement->host_agreement_newsletter !=
                null &&
                !$this->validateDate(
                    $hosts_agreement->host_agreement_newsletter
                ))
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'host'
            );
        }
        // check if region is included in city region
        if (
            $city_model
                ->where(
                    'city_region',
                    $host_region_residence
                )
                ->first() == null
        ) {
            return $this->notifyError(
                'No Such region',
                'notFound',
                'host'
            );
        }
        // check if duplicate email
        if (
            $host_model
                ->whereNotIn('host_id', (array) $host_id)
                ->where(
                    'host_referral_email',
                    $host_referral_email
                )
                ->first() != null
        ) {
            return $this->notifyError(
                'Email already exists.',
                'invalid_data',
                'host'
            );
        }
        /* update */
        $data = [
            'host_category_code' =>
                $host_category_code == null
                    ? 1
                    : $host_category_code,
            'host_sub_category_code' =>
                $host_sub_category_code == null
                    ? 1
                    : $host_sub_category_code,
            'host_referral_surname' => $host_referral_surname,
            'host_referral_name' => $host_referral_name,
            'host_referral_phone' => $host_referral_phone,
            'host_mobile_phone' => $host_mobile_phone,
            'host_referral_email' => $host_referral_email,
            'host_company_name' => $host_company_name,
            'host_city_residence' => $host_city_residence,
            'host_address_residence' => $host_address_residence,
            'host_postcode_residence' => $host_postcode_residence,
            'host_province_residence' => $host_province_residence,
            'host_region_residence' => $host_region_residence,
            'host_iso_state_residence' => $host_iso_state_residence,
        ];
        if (!$host_model->update($host_id, $data)) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'host'
            );
        }
        // update agreement data
        $agreement_data = [
            'host_agreement_host_id' => $host_id,
            'host_agreement_privacy' =>
                $hosts_agreement->host_agreement_privacy,
            'host_agreement_newsletter' =>
                $hosts_agreement->host_agreement_newsletter,
            'host_agreement_rules' =>
                $hosts_agreement->host_agreement_rules,
        ];
        $host_agreement_info = $host_agreement_model
            ->where('host_agreement_host_id', $host_id)
            ->first();
        if ($host_agreement_info == null) {
            $host_agreement_model->insert($agreement_data);
        } else {
            $host_agreement_model->update(
                $host_agreement_info['host_agreement_id'],
                $agreement_data
            );
        }

        return parent::respond([
            'message' => 'Successfully updated.',
        ]);
    }

    /**
     * Return an array of objects
     * GET/host/financial
     * @return mixed
     */
    public function financial()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $host_agreement_model = new HostAgreementModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        /* Getting data from db*/
        $host_list = $host_model
            ->select(
                'host_company_name_tax, host_phone_tax, host_address_tax, host_city_tax , host_postcode_tax, host_province_tax, host_iso_state_tax, host_taxnumber_tax, host_taxcode_tax, host_certemail_tax , host_sdicode_tax, host_activation_date, host_last_update, host_username_security, host_password_security, host_password_lastupdate_security, host_status'
            )
            ->find($host_id);
        return parent::respond([
            'financial_host' =>
                $host_list == null ? [] : $host_list,
        ]);
    }

    /**
     * Update
     * PUT/host/financial/update
     * @return mixed
     */
    public function financial_update()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'host_company_name_tax' => 'required',
                'host_address_tax' => 'required',
                'host_city_tax' => 'required',
                'host_postcode_tax' => 'required',
                'host_province_tax' => 'required',
                'host_iso_state_tax' => 'required',
                'host_taxnumber_tax' => 'required',
                'host_taxcode_tax' => 'required',
                'host_sdicode_tax' => 'required',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $host_company_name_tax = $this->request->getVar(
            'host_company_name_tax'
        );
        $host_phone_tax = $this->request->getVar(
            'host_phone_tax'
        );
        $host_address_tax = $this->request->getVar(
            'host_address_tax'
        );
        $host_city_tax = $this->request->getVar(
            'host_city_tax'
        );
        $host_postcode_tax = $this->request->getVar(
            'host_postcode_tax'
        );
        $host_province_tax = $this->request->getVar(
            'host_province_tax'
        );
        $host_iso_state_tax = $this->request->getVar(
            'host_iso_state_tax'
        );
        $host_taxnumber_tax = $this->request->getVar(
            'host_taxnumber_tax'
        );
        $host_taxcode_tax = $this->request->getVar(
            'host_taxcode_tax'
        );
        $host_certemail_tax = $this->request->getVar(
            'host_certemail_tax'
        );
        $host_sdicode_tax = $this->request->getVar(
            'host_sdicode_tax'
        );
        $host_activation_date = $this->request->getVar(
            'host_activation_date'
        );
        $host_last_update = $this->request->getVar(
            'host_last_update'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        if (
            $host_activation_date != null &&
            !$this->validateDate($host_activation_date)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'host'
            );
        }
        if (
            $host_last_update != null &&
            !$this->validateDate($host_last_update)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'host'
            );
        }
        /* update */
        $data = [
            'host_company_name_tax' => $host_company_name_tax,
            'host_phone_tax' => $host_phone_tax,
            'host_address_tax' => $host_address_tax,
            'host_city_tax' => $host_city_tax,
            'host_postcode_tax' => $host_postcode_tax,
            'host_province_tax' => $host_province_tax,
            'host_iso_state_tax' => $host_iso_state_tax,
            'host_taxnumber_tax' => $host_taxnumber_tax,
            'host_taxcode_tax' => $host_taxcode_tax,
            'host_certemail_tax' => $host_certemail_tax,
            'host_sdicode_tax' => $host_sdicode_tax,
            'host_activation_date' => $host_activation_date,
            'host_last_update' => $host_last_update,
        ];
        if (!$host_model->update($host_id, $data)) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'host'
            );
        }

        return parent::respond([
            'message' => 'Successfully updated.',
        ]);
    }

    /**
     * Return an array of objects
     * GET/host/lang
     * @return mixed
     */
    public function lang()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $host_lang_model = new HostLangModel();
        $host_agreement_model = new HostAgreementModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        /* Getting data from db*/
        $host_lang_list = $host_lang_model
            ->select(
                'host_id, host_lang_code, host_lang_name, host_lang_subtitle, host_short_description, host_lang_long_description, host_lang_booking_rules, host_lang_property_rules, host_lang_arrival_information'
            )
            ->where('host_id', $host_id)
            ->findAll();
        return parent::respond([
            'host_langs' =>
                $host_lang_list == null
                    ? []
                    : $host_lang_list,
        ]);
    }

    /**
     * Update
     * PUT/host/lang/update
     * @return mixed
     */
    public function lang_update()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $host_lang_model = new HostLangModel();
        $lang_model = new LanguageModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'host_langs' => 'required',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $host_langs = $this->request->getVar('host_langs');

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        if (!is_array($host_langs)) {
            return $this->notifyError(
                'host_langs should be array.',
                'invalid_data',
                'host'
            );
        }
        foreach ($host_langs as $row) {
            if (!isset($row->host_lang_code)) {
                return $this->notifyError(
                    'host_lang_code is required',
                    'invalid_data',
                    'host'
                );
            }
            if ($row->host_lang_code == '') {
                return $this->notifyError(
                    'host_lang_code is required',
                    'invalid_data',
                    'host'
                );
            }
            if (
                $lang_model
                    ->where(
                        'language_code',
                        $row->host_lang_code
                    )
                    ->first() == null
            ) {
                return $this->notifyError(
                    'Invalid language exists.',
                    'invalid_data',
                    'host'
                );
            }
            if (
                isset($row->lang_action) &&
                $row->lang_action != '' &&
                $row->lang_action != 'DELETE'
            ) {
                return $this->notifyError(
                    'lang_action shoule be empty string or `DELETE`.',
                    'invalid_data',
                    'host'
                );
            }
            if (
                !(
                    isset($row->lang_action) &&
                    $row->lang_action == 'DELETE'
                )
            ) {
                if (!isset($row->host_lang_name)) {
                    return $this->notifyError(
                        'host_lang_name is required',
                        'invalid_data',
                        'host'
                    );
                }
                if ($row->host_lang_name == '') {
                    return $this->notifyError(
                        'host_lang_name is required',
                        'invalid_data',
                        'host'
                    );
                }
                if (!isset($row->host_lang_subtitle)) {
                    return $this->notifyError(
                        'host_lang_subtitle is required',
                        'invalid_data',
                        'host'
                    );
                }
                if ($row->host_lang_subtitle == '') {
                    return $this->notifyError(
                        'host_lang_subtitle is required',
                        'invalid_data',
                        'host'
                    );
                }
                if (!isset($row->host_short_description)) {
                    return $this->notifyError(
                        'host_short_description is required',
                        'invalid_data',
                        'host'
                    );
                }
                if ($row->host_short_description == '') {
                    return $this->notifyError(
                        'host_short_description is required',
                        'invalid_data',
                        'host'
                    );
                }
                if (
                    !isset($row->host_lang_long_description)
                ) {
                    return $this->notifyError(
                        'host_lang_long_description is required',
                        'invalid_data',
                        'host'
                    );
                }
                if (
                    $row->host_lang_long_description == ''
                ) {
                    return $this->notifyError(
                        'host_lang_long_description is required',
                        'invalid_data',
                        'host'
                    );
                }
            }
        }
        /* update data in DB*/
        $multi_query = [];
        foreach ($host_langs as $row) {
            $data = [
                'host_id' => $host_id,
                'host_lang_code' => $row->host_lang_code,
                'host_lang_name' => $row->host_lang_name,
                'host_lang_subtitle' =>
                    $row->host_lang_subtitle,
                'host_short_description' =>
                    $row->host_short_description,
                'host_lang_long_description' =>
                    $row->host_lang_long_description,
                'host_lang_booking_rules' =>
                    $row->host_lang_booking_rules,
                'host_lang_property_rules' =>
                    $row->host_lang_property_rules,
                'host_lang_arrival_information' =>
                    $row->host_lang_arrival_information,
            ];
            $matched_id = $host_lang_model
                ->where([
                    'host_id' => $host_id,
                    'host_lang_code' =>
                        $data['host_lang_code'],
                ])
                ->first();
            if (
                !(
                    isset($row->lang_action) &&
                    $row->lang_action == 'DELETE'
                )
            ) {
                if ($matched_id != null) {
                    array_push(
                        $multi_query,
                        'UPDATE host_lang SET host_id = ' .
                            $data['host_id'] .
                            ', host_lang_code = "' .
                            $data['host_lang_code'] .
                            '", host_lang_name = "' .
                            $data['host_lang_name'] .
                            '", host_lang_subtitle = "' .
                            $data['host_lang_subtitle'] .
                            '", host_short_description = "' .
                            $data[
                                'host_short_description'
                            ] .
                            '", host_lang_long_description = "' .
                            $data[
                                'host_lang_long_description'
                            ] .
                            '", host_lang_booking_rules = "' .
                            $data[
                                'host_lang_booking_rules'
                            ] .
                            '", host_lang_property_rules = "' .
                            $data[
                                'host_lang_property_rules'
                            ] .
                            '", host_lang_arrival_information = "' .
                            $data[
                                'host_lang_arrival_information'
                            ] .
                            '" WHERE host_lang_id = ' .
                            $matched_id['host_lang_id']
                    );
                } else {
                    array_push(
                        $multi_query,
                        'INSERT INTO host_lang (host_id, host_lang_code, host_lang_name, host_lang_subtitle, host_short_description, host_lang_long_description, host_lang_booking_rules, host_lang_property_rules, host_lang_arrival_information)
                    VALUES (' .
                            $data['host_id'] .
                            ', "' .
                            $data['host_lang_code'] .
                            '", "' .
                            $data['host_lang_name'] .
                            '", "' .
                            $data['host_lang_subtitle'] .
                            '", "' .
                            $data[
                                'host_short_description'
                            ] .
                            '", "' .
                            $data[
                                'host_lang_long_description'
                            ] .
                            '", "' .
                            $data[
                                'host_lang_booking_rules'
                            ] .
                            '", "' .
                            $data[
                                'host_lang_property_rules'
                            ] .
                            '", "' .
                            $data[
                                'host_lang_arrival_information'
                            ] .
                            '")'
                    );
                }
            } else {
                if ($matched_id != null) {
                    array_push(
                        $multi_query,
                        'DELETE FROM host_lang WHERE host_lang_id = ' .
                            $matched_id['host_lang_id']
                    );
                }
            }
        }

        if (
            !$host_lang_model->multi_query_execute(
                $multi_query
            )
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'host'
            );
        }

        return parent::respond([
            'message' => 'Successfully updated',
        ]);
    }

    /**
     * Return an array of mapped property type
     * GET/host/propertytypes
     * @return mixed
     */
    public function mapped_property_types()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $type_mapping_model = new TypeMappingModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        /* Getting data from db*/
        $mapped_property_types = $type_mapping_model->get_mapped_property_types(
            $host_id
        );
        return parent::respond([
            'mapped_property_types' =>
                $mapped_property_types == null
                    ? []
                    : $mapped_property_types,
        ]);
    }

    /**
     * Return an array of mapped type
     * GET/host/types
     * @return mixed
     */
    public function mapped_types()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $type_mapping_model = new TypeMappingModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'status' =>
                    'required|regex_match[/[01234]/]',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $status = $this->request->getVar('status');
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such Id',
                'notFound',
                'host'
            );
        }
        /* Getting data from db*/
        $mapped_types = $type_mapping_model->get_mapped_types(
            $host_id,
            $status
        );
        return parent::respond([
            'mapped_types' =>
                $mapped_types == null ? [] : $mapped_types,
        ]);
    }

    /**
     * Update
     * PUT/host/types/update
     * @return mixed
     */
    public function mapped_types_update()
    {
        $config = config('Config\App');
        // Getting user level from JWT token
        $user_level = $this->get_userlevel();

        // Load necessary Model
        $host_model = new HostModel();
        $lang_model = new LanguageModel();
        $type_mapping_model = new TypeMappingModel();
        $type_main_model = new TypeMainModel();

        /* Validate */
        if (
            !$this->validate([
                'host_id' => 'required|integer',
                'main_type_code' => 'required',
                'type_mapping_code' => 'required',
                'type_mapping_name' => 'required',
                'type_mapping_description' => 'required',
                'type_mapping_lang' => 'required',
                'type_mapping_main_status' =>
                    'required|regex_match[/[01234]/]',
            ])
        ) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'host'
            );
        }

        /* Getting request data */
        $host_id = $this->request->getVar('host_id');
        $mapping_id = $this->request->getVar('mapping_id');
        $main_type_code = $this->request->getVar(
            'main_type_code'
        );
        $type_mapping_code = $this->request->getVar(
            'type_mapping_code'
        );
        $type_mapping_name = $this->request->getVar(
            'type_mapping_name'
        );
        $type_mapping_description = $this->request->getVar(
            'type_mapping_description'
        );
        $type_mapping_lang = $this->request->getVar(
            'type_mapping_lang'
        );
        $type_mapping_main_status = $this->request->getVar(
            'type_mapping_main_status'
        );

        /* Validation */
        if ($user_level != $config->USER_LEVELS['admin']) {
            $main_host_id = $this->get_host_id();
            if ($host_id != $main_host_id) {
                return $this->notifyError(
                    'host_id should be ' . $main_host_id,
                    'invalid_data',
                    'host'
                );
            }
        }
        if ($host_model->find($host_id) == null) {
            return $this->notifyError(
                'No Such host_id',
                'notFound',
                'host'
            );
        }
        if (
            $lang_model
                ->where('language_code', $type_mapping_lang)
                ->first() == null
        ) {
            return $this->notifyError(
                'Invalid language exists.',
                'invalid_data',
                'host'
            );
        }
        if (
            $mapping_id != null &&
            !ctype_digit((string) $mapping_id)
        ) {
            return $this->notifyError(
                'mapping_id should be integer',
                'invalid_data' . 'host'
            );
        }
        if (
            $mapping_id != null &&
            $type_mapping_model->find($mapping_id) == null
        ) {
            return $this->notifyError(
                'No Such mapping id',
                'notFound',
                'host'
            );
        }
        if (
            $type_main_model
                ->where('main_type_code', $main_type_code)
                ->first() == null
        ) {
            return $this->notifyError(
                'No Such main_type_code',
                'notFount',
                'host'
            );
        }
        /* update data in DB*/
        $data = [
            'type_mapping_host_id' => $host_id,
            'type_mapping_main_code' => $main_type_code,
            'type_mapping_code' => $type_mapping_code,
            'type_mapping_name' => $type_mapping_name,
            'type_mapping_description' => $type_mapping_description,
            'type_mapping_lang' => $type_mapping_lang,
            'type_mapping_main_status' => $type_mapping_main_status,
        ];
        // update
        $new_id = '';
        if ($mapping_id != null) {
            if (
                !$type_mapping_model->update(
                    $mapping_id,
                    $data
                )
            ) {
                return $this->notifyError(
                    'Failed update',
                    'failed_update',
                    'host'
                );
            }
        } else {
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_host_id' => $host_id,
                        'type_mapping_main_code' => $main_type_code,
                        'type_mapping_code' => $type_mapping_code,
                        'type_mapping_lang' => $type_mapping_lang,
                    ])
                    ->first() != null
            ) {
                return $this->notifyError(
                    'Duplication error',
                    'duplicate',
                    'host'
                );
            }
            $new_id = $type_mapping_model->insert($data);
            if (!$new_id) {
                return $this->notifyError(
                    'Failed create',
                    'failed_create',
                    'host'
                );
            }
        }

        return parent::respond([
            'id' =>
                $mapping_id == null ? $new_id : $mapping_id,
            'message' =>
                'Successfully ' .
                ($mapping_id == null
                    ? 'created'
                    : 'updated'),
        ]);
    }
}
