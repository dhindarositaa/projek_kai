<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetsTables extends Migration
{
    public function up()
    {
        // procurements
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_rab' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'no_npd' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'procurement_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'vendor' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['no_rab','no_npd']);
        $this->forge->createTable('procurements', true);

        // asset_models
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'brand' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ],
            'model' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ],
            'specs' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['brand','model']);
        $this->forge->createTable('asset_models', true);

        // units
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('units', true);

        // employees
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nipp' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nipp');
        $this->forge->createTable('employees', true);

        // assets
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'asset_code' => [ // No Inventaris
                'type' => 'VARCHAR',
                'constraint' => '120',
                'null' => false,
            ],
            'procurement_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'asset_model_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ],
            'purchase_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'unit_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'specification' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'label_attached' => [
                'type' => "ENUM('Sudah','Belum')",
                'null' => true,
                'default' => 'Belum',
            ],
            'condition' => [
                'type' => "ENUM('baik','rusak','dipinjam','disposal','diganti')",
                'null' => true,
                'default' => 'baik',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('serial_number');
        $this->forge->addKey('asset_code');
        $this->forge->addForeignKey('procurement_id','procurements','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('asset_model_id','asset_models','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('unit_id','units','id','SET NULL','CASCADE');
        $this->forge->addForeignKey('employee_id','employees','id','SET NULL','CASCADE');
        $this->forge->createTable('assets', true);

        // documents
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'asset_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'procurement_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'doc_type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'doc_number' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ],
            'doc_link' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('asset_id','assets','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('procurement_id','procurements','id','CASCADE','CASCADE');
        $this->forge->createTable('documents', true);

        // asset_history
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'asset_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => '80',
                'null' => false,
            ],
            'event_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'from_unit_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'to_unit_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recorded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('asset_id','assets','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('from_unit_id','units','id','SET NULL','CASCADE');
        $this->forge->addForeignKey('to_unit_id','units','id','SET NULL','CASCADE');
        $this->forge->addForeignKey('recorded_by','employees','id','SET NULL','CASCADE');
        $this->forge->createTable('asset_history', true);

        // import_logs (optional helper table to store import results)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'imported_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'source_file' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'row_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('import_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('import_logs', true);
        $this->forge->dropTable('asset_history', true);
        $this->forge->dropTable('documents', true);
        $this->forge->dropTable('assets', true);
        $this->forge->dropTable('employees', true);
        $this->forge->dropTable('units', true);
        $this->forge->dropTable('asset_models', true);
        $this->forge->dropTable('procurements', true);
    }
}
