<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AssetLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'asset_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],

            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],

            'field' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'old_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'new_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'action' => [
                'type' => 'ENUM',
                'constraint' => ['create','update','delete'],
                'default' => 'update',
            ],

            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],

            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('asset_id');
        $this->forge->addKey('user_id');

        $this->forge->createTable('asset_logs');
    }

    public function down()
    {
        $this->forge->dropTable('asset_logs');
    }
}
