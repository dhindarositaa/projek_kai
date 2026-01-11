<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameDocTypeToNoWoBast extends Migration
{
    public function up()
    {
       $this->db->query("
            ALTER TABLE documents 
            CHANGE doc_type no_wo_bast VARCHAR(100) NULL
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE documents 
            CHANGE no_wo_bast doc_type VARCHAR(50) NULL
        ");
    }
}
