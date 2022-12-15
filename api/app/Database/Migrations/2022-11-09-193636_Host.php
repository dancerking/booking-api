<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Host extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'host_id' => [
                'type'          => 'INT',
                'constraint'    => '11',
                'auto_increment' => true,
                'unsigned'      => true,
            ],
            'host_category_code' => [
                'type'          => "INT",
                'constraint'    => '11',
                'default'       => '1',
            ],
            'host_sub_category_code' => [
                'type'          => "INT",
                'constraint'    => '11',
            ],
            'host_referral_surname' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null
            ],
            'host_referral_name' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null
            ],
            'host_referral_phone' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null
            ],
            'host_mobile_phone' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null
            ],
            'host_referral_email' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null
            ],
            'host_referral_lang' => [
                'type'          => "VARCHAR",
                'constraint'    => '2',
                'default'       => 'it',
                'null'          => true,
            ],
            'host_company_name' => [
                'type'          => "VARCHAR",
                'constraint'    => '250',
                'default'       => null,
            ],
            'host_company_logo' => [
                'type'          => "VARCHAR",
                'constraint'    => '255',
                'null'          => true,
            ],
            'host_city_residence' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'null'          => true,
            ],
            'host_address_residence' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                 'null'          => true,
            ],
            'host_gps' => [
                'type'          => "VARCHAR",
                'constraint'    => '200',
            ],
            'host_postcode_residence' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'null'          => true,
            ],
            'host_province_residence' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_region_residence' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_iso_state_residence' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_company_name_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '100',
                'default'       => null,
            ],
            'host_email_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_phone_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_address_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_city_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_postcode_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '20',
                'default'       => null,
            ],
            'host_province_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_iso_state_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '2',
                'default'       => null,
            ],
            'host_taxnumber_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '20',
                'default'       => null,
            ],
            'host_taxcode_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '20',
                'default'       => null,
            ],
            'host_certemail_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_sdicode_tax' => [
                'type'          => "VARCHAR",
                'constraint'    => '20',
                'default'       => null,
            ],
            'host_activation_date' => [
                'type'          => "TIMESTAMP",
                'default'       => null,
            ],
            'host_ip_connection' => [
                'type'          => "VARCHAR",
                'constraint'    => '15',
                'default'       => null,
            ],
            'host_last_update' => [
                'type'          => 'TIMESTAMP',
                'default'       => new RawSql('CURRENT_TIMESTAMP'),
                'null'          => true,
            ],
            'host_username_security' => [
                'type'          => "VARCHAR",
                'constraint'    => '50',
                'default'       => null,
            ],
            'host_password_security' => [
                'type'          => "VARCHAR",
                'constraint'    => '200',
                'default'       => null,
            ],
            'host_password_lastupdate_security' => [
                'type'          => 'TIMESTAMP',
                'default'       => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'host_status' => [
                'type'          => "INT",
                'constraint'    => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('host_id');
        $this->forge->createTable('hosts');
    }

    public function down()
    {
        $this->forge->dropTable('hosts');
    }
}
