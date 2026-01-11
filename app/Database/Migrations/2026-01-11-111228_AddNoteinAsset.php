<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoteinAsset extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assets', [
            'note' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'condition'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('assets', 'note');
    }
}
