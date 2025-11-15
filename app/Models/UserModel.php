<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    // HANYA FIELD YANG BENAR-BENAR ADA DI DATABASE
    protected $allowedFields = ['name', 'email', 'password_hash'];

    // MATIKAN TIMESTAMPS
    protected $useTimestamps = false;

    // Verifikasi password
    public function verifyPassword($plain, $hash)
    {
        return password_verify($plain, $hash);
    }
}
