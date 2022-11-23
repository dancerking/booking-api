<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Guest extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'guest_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'guest_category_code' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ],
            'guest_sub_category_code' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ],
            'guest_referral_surname' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_referral_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_referral_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_mobile_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_referral_email' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_referral_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
                'null' => true,
                'default' => null,
            ],
            'guest_company_name' => [
                'type' => 'VARCHAR',
                'constraint' => '250',
                'null' => true,
                'default' => null,
            ],
            'guest_account_img' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => null,
            ],
            'guest_city_residence' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_address_residence' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_postcode_residence' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_province_residence' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_region_residence' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_iso_state_residence' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_company_name_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => null,
            ],
            'guest_email_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_phone_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_address_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_city_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_postcode_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'default' => null,
            ],
            'guest_province_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_iso_state_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
                'null' => true,
                'default' => null,
            ],
            'guest_taxnumber_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'default' => null,
            ],
            'guest_taxcode_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'default' => null,
            ],
            'guest_certemail_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_sdicode_tax' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'default' => null,
            ],
            'guest_activation_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
            'guest_ip_connection' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
                'default' => null,
            ],
            'guest_last_update' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'guest_username_security' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'default' => null,
            ],
            'guest_password_security' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => null,
            ],
            'guest_password_lastupdate_security' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'guest_status' => [
                'type' => 'INT',
                'constraint' => '11',
                'deafult' => '0',
            ],
        ]);
        $this->forge->addPrimaryKey('guest_id');
        $this->forge->createTable('guests');
    }

    public function down()
    {
        $this->forge->dropTable('guests');
    }
}
