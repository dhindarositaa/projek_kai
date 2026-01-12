<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetLogModel extends Model
{
    protected $table = 'asset_logs';
    protected $allowedFields = [
        'asset_id','user_id','field','old_value','new_value','action','ip_address','created_at'
    ];
    public $timestamps = false;
}
