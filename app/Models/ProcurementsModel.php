<?php
namespace App\Models;

use CodeIgniter\Model;

class ProcurementsModel extends Model
{
    protected $table = 'procurements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['no_rab','no_npd','procurement_date','vendor','notes','created_at','updated_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
