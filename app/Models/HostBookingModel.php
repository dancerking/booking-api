<?php

namespace App\Models;

use CodeIgniter\Model;

class HostBookingModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'host_bookings';
    protected $primaryKey = 'host_booking_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'host_booking_id',
        'host_booking_host_id',
        'host_booking_code',
        'host_booking_ota',
        'host_booking_ota_code',
        'host_booking_registration_date',
        'host_booking_arrival',
        'host_booking_departure',
        'host_booking_property_type',
        'host_booking_guests_number',
        'host_booking_gtype1',
        'host_booking_gtype2',
        'host_booking_gtype3',
        'host_booking_gtype4',
        'host_booking_total_amount',
        'host_booking_paid',
        'host_booking_currency',
        'host_booking_referral_email',
        'host_booking_referral_name',
        'host_booking_referral_surname',
        'host_booking_referral_mobile_phone',
        'host_booking_referral_id',
        'host_booking_referral_lang',
        'host_booking_ota_fee',
        'host_booking_note',
        'host_booking_rules',
        'host_booking_rate',
        'host_booking_rate_id',
        'host_booking_promo',
        'host_booking_status',
        'host_booking_activation_date',
        'host_booking_ip_connection',
        'host_booking_last_update',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function get_bookings(
        $host_id,
        $bookingfrom,
        $bookingto
    ) {
        $query = $this->db->query(
            'SELECT host_booking_id, host_booking_code, host_booking_ota_code, host_booking_registration_date, host_booking_arrival, host_booking_departure, host_booking_property_type, host_booking_guests_number, host_booking_gtype1, host_booking_gtype2, host_booking_gtype3, host_booking_gtype3, host_booking_gtype4, host_booking_total_amount, host_booking_currency, host_booking_paid, host_booking_referral_email, host_booking_referral_name, host_booking_referral_surname, host_booking_referral_mobile_phone, host_booking_referral_id, host_booking_referral_lang, host_booking_note, host_booking_rate_id, host_booking_promo, host_booking_status, host_booking_last_update
        FROM host_bookings
        WHERE host_booking_host_id = ' .
                $host_id .
                ' AND host_booking_activation_date >= "' .
                $bookingfrom .
                '" AND host_booking_activation_date <= "' .
                $bookingto .
                '"'
        );
        $results = $query->getResult();
        if ($results == null) {
            return null;
        }
        foreach ($results as &$result) {
            $payment_query = $this->db->query(
                'SELECT payment_id, payment_date, payment_currency, payment_value, payment_guest_id, payment_method, payment_code FROM payments WHERE payment_booking_id = ' .
                    $result->host_booking_id
            );
            $payment_result = $payment_query->getResult();
            $result->host_booking_payment = $payment_result;
        }
        return $results;
    }
}
