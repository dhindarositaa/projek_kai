<?php
namespace App\Models;

use CodeIgniter\Model;

class UnitsModel extends Model
{
    protected $table = 'units';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name','description','created_at','updated_at'];
    protected $useTimestamps = true;
}
