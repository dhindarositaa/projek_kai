<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReplacedAtToAssets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assets', [
            'replaced_at' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'purchase_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('assets', 'replaced_at');
    }
}
