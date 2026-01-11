<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoWoBastToDocuments extends Migration
{
    public function up()
    {
       $this->forge->addColumn('documents', [
            'no_wo_bast' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'doc_number'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('documents', 'no_wo_bast');
    }
}
