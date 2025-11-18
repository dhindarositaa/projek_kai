<?php
namespace App\Models;

use CodeIgniter\Model;

class AssetsModel extends Model
{
    protected $table      = 'assets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // samakan dengan migration (kamu sudah kirim)
    protected $allowedFields = [
        'asset_code',
        'procurement_id',
        'asset_model_id',
        'serial_number',
        'purchase_date',
        'unit_id',
        'employee_id',
        'specification',
        'label_attached',
        'condition',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat = 'datetime';

    // soft delete (aktifkan bila ingin menggunakan soft delete via model)
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'asset_code'    => 'required|max_length[120]',
        'procurement_id'=> 'permit_empty|is_natural_no_zero',
        'asset_model_id'=> 'permit_empty|is_natural_no_zero',
        'serial_number' => 'permit_empty|max_length[200]',
        'purchase_date' => 'permit_empty|valid_date[Y-m-d]',
        'unit_id'       => 'permit_empty|is_natural_no_zero',
        'employee_id'   => 'permit_empty|is_natural_no_zero',
        'label_attached'=> 'permit_empty|in_list[Sudah,Belum]',
        'condition'     => 'permit_empty|in_list[baik,rusak,dipinjam,disposal]',
    ];

    protected $skipValidation = false;

    /**
     * Ambil list assets lengkap dengan join ke tables referensi
     * untuk tampilan index/detail.
     */
    public function getAssetsWithRelations($limit = null, $offset = null)
    {
        $builder = $this->db->table($this->table . ' a');
        $builder->select('a.*, p.no_npd, p.procurement_date, u.name as unit_name, am.brand, am.model as model_name, e.name as employee_name');
        $builder->join('procurements p', 'a.procurement_id = p.id', 'left');
        $builder->join('units u', 'a.unit_id = u.id', 'left');
        $builder->join('asset_models am', 'a.asset_model_id = am.id', 'left');
        $builder->join('employees e', 'a.employee_id = e.id', 'left');
        $builder->orderBy('a.id', 'DESC');
        if ($limit) $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    public function findWithRelations($id)
    {
        $builder = $this->db->table($this->table.' a');
        $builder->select('a.*, p.no_npd, p.procurement_date, u.name as unit_name, am.brand, am.model as model_name, e.name as employee_name');
        $builder->join('procurements p', 'a.procurement_id = p.id', 'left');
        $builder->join('units u', 'a.unit_id = u.id', 'left');
        $builder->join('asset_models am', 'a.asset_model_id = am.id', 'left');
        $builder->join('employees e', 'a.employee_id = e.id', 'left');
        $builder->where('a.id', $id);
        return $builder->get()->getRowArray();
    }

    // helper: cek FK exist (dipanggil di controller sebelum insert/update)
    public function foreignsExist(array $data): array
    {
        $errors = [];
        if (!empty($data['procurement_id'])) {
            if (!$this->db->table('procurements')->where('id', $data['procurement_id'])->countAllResults()) {
                $errors['procurement_id'] = 'Procurement tidak ditemukan';
            }
        }
        if (!empty($data['asset_model_id'])) {
            if (!$this->db->table('asset_models')->where('id', $data['asset_model_id'])->countAllResults()) {
                $errors['asset_model_id'] = 'Asset model tidak ditemukan';
            }
        }
        if (!empty($data['unit_id'])) {
            if (!$this->db->table('units')->where('id', $data['unit_id'])->countAllResults()) {
                $errors['unit_id'] = 'Unit tidak ditemukan';
            }
        }
        if (!empty($data['employee_id'])) {
            if (!$this->db->table('employees')->where('id', $data['employee_id'])->countAllResults()) {
                $errors['employee_id'] = 'Employee tidak ditemukan';
            }
        }
        return $errors;
    }
}
