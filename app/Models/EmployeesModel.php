<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeesModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nipp','name','email','phone','created_at','updated_at'];
    protected $useTimestamps = true;
}
