<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\CityModel;
use App\Models\HostAgreementModel;
use App\Models\HostModel;
use App\Models\IpWhiteListModel;
use CodeIgniter\API\ResponseTrait;

class Register extends APIBaseController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        helper(['form']);
        $config = config('Config\App');

        /* Load Model */
        $host_model = new HostModel();
        $ip_white_list_model = new IpWhiteListModel();
        $host_agreement_model = new HostAgreementModel();
        $city_model = new CityModel();

        /* Validate */
        $rules = [
            'host_company_name' => 'required',
            'host_region_residence' => 'required',
            'host_postcode_residence' => 'required',
            'host_city_residence' => 'required',
            'host_province_residence' => 'required',
            'host_iso_state_residence' => 'required',
            'host_referral_phone' => 'required',
            'host_referral_email' => 'required|valid_email',
            'host_referral_surnamme' => 'required',
            'host_referral_name' => 'required',
            'host_mobile_phone' => 'required',
            'host_agreement_privacy' => 'required',
            'host_agreement_rules' => 'required',
            'password' =>
                'required|min_length[7]|regex_match[/[a-z]/]|regex_match[/[A-Z]/]|regex_match[/[0-9]/]|regex_match[/[!#$%^&*]/]',
            'repeat_password' =>
                'required|matches[password]',
            'host_company_name_tax' => 'required',
            'host_postcode_tax' => 'required',
            'host_city_tax' => 'required',
            'host_province_tax' => 'required',
            'host_iso_state_tax' => 'required',
            'host_address_tax' => 'required',
            'host_taxnumber_tax' => 'required',
            'host_taxcode_tax' => 'required',
            'host_certemail_tax' => 'required',
            'host_sdicode_tax' => 'required',
        ];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $error_string = '';
            foreach ($errors as $key => $value) {
                $error_string .= $value . ' ';
            }
            return $this->notifyError(
                $error_string,
                'invalid_data',
                'register'
            );
        }

        /* Validation */
        $email = $this->request->getVar(
            'host_referral_email'
        );
        if (
            $host_model
                ->where('host_referral_email', $email)
                ->findAll() != null
        ) {
            return $this->notifyError(
                'This email already exists in db.',
                'invalid_data',
                'register'
            );
        }
        $host_agreement_privacy = $this->request->getVar(
            'host_agreement_privacy'
        );
        $host_agreement_rules = $this->request->getVar(
            'host_agreement_rules'
        );
        $host_agreement_newsletter = $this->request->getVar(
            'host_agreement_newsletter'
        );

        if (
            !$this->validateDate($host_agreement_privacy) ||
            !$this->validateDate($host_agreement_rules) ||
            ($host_agreement_newsletter != null &&
                !$this->validateDate(
                    $host_agreement_newsletter
                ))
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'register'
            );
        }
        $host_region_residence = $this->request->getVar(
            'host_region_residence'
        );
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
                'register'
            );
        }
        /* Register */
        $data = [
            'host_company_name' => $this->request->getVar(
                'host_company_name'
            ),
            'host_region_residence' => $this->request->getVar(
                'host_region_residence'
            ),
            'host_postcode_residence' => $this->request->getVar(
                'host_postcode_residence'
            ),
            'host_city_residence' => $this->request->getVar(
                'host_city_residence'
            ),
            'host_province_residence' => $this->request->getVar(
                'host_province_residence'
            ),
            'host_iso_state_residence' => $this->request->getVar(
                'host_iso_state_residence'
            ),
            'host_referral_phone' => $this->request->getVar(
                'host_referral_phone'
            ),
            'host_referral_email' => $this->request->getVar(
                'host_referral_email'
            ),
            'host_referral_surnamme' => $this->request->getVar(
                'host_referral_surnamme'
            ),
            'host_referral_name' => $this->request->getVar(
                'host_referral_name'
            ),
            'host_mobile_phone' => $this->request->getVar(
                'host_mobile_phone'
            ),
            'host_password_security' => hash(
                'sha512',
                $this->request->getVar('password')
            ),
            'host_company_name_tax' => $this->request->getVar(
                'host_company_name_tax'
            ),
            'host_postcode_tax' => $this->request->getVar(
                'host_postcode_tax'
            ),
            'host_city_tax' => $this->request->getVar(
                'host_city_tax'
            ),
            'host_province_tax' => $this->request->getVar(
                'host_province_tax'
            ),
            'host_iso_state_tax' => $this->request->getVar(
                'host_iso_state_tax'
            ),
            'host_address_tax' => $this->request->getVar(
                'host_address_tax'
            ),
            'host_taxnumber_tax' => $this->request->getVar(
                'host_taxnumber_tax'
            ),
            'host_taxcode_tax' => $this->request->getVar(
                'host_taxcode_tax'
            ),
            'host_certemail_tax' => $this->request->getVar(
                'host_certemail_tax'
            ),
            'host_sdicode_tax' => $this->request->getVar(
                'host_sdicode_tax'
            ),
            'host_ip_connection' => $this->request->getIPAddress(),
            'host_password_lastupdate_security' => date(
                'Y-m-d H:i:s'
            ),
            'host_status' => 0,
        ];
        $host_model = new HostModel();
        $new_id = $host_model->insert($data);
        if (!$new_id) {
            return $this->notifyError(
                'Failed register',
                'failed_create',
                'register'
            );
        }

        // register host_agreement_data
        $host_agreement_model->insert([
            'host_agreement_privacy' => $host_agreement_privacy,
            'host_agreement_rules' => $host_agreement_rules,
            'host_agreement_newsletter' => $host_agreement_newsletter,
            'host_agreement_host_id' => $new_id,
        ]);
        // registering white ip
        $ip_white_list_model->insert([
            'white_ip' => $config->EXTRANET_IP,
            'host_id' => $new_id,
        ]);

        return $this->respond([
            'message' => 'Successfully registered',
        ]);
    }
}
