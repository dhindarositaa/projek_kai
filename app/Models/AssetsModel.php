<?php
namespace App\Models;

use CodeIgniter\Model;

class AssetsModel extends Model
{
    protected $table      = 'assets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // Field sesuai tabel assets (tanpa deleted_at)
    protected $allowedFields = [
        'asset_code',
        'procurement_id',
        'asset_model_id',
        'serial_number',
        'purchase_date',
        'replaced_at',
        'unit_id',
        'employee_id',
        'specification',
        'label_attached',
        'condition',
        'created_at',
        'updated_at',
        'note',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    // Soft delete dimatikan
    protected $useSoftDeletes = false;
    protected $deletedField   = 'deleted_at';

    protected $validationRules = [
        'asset_code'     => 'required|max_length[120]',
        'procurement_id' => 'permit_empty|is_natural_no_zero',
        'asset_model_id' => 'permit_empty|is_natural_no_zero',
        'serial_number'  => 'permit_empty|max_length[200]',
        'purchase_date'  => 'permit_empty|valid_date[Y-m-d]',
        'unit_id'        => 'permit_empty|is_natural_no_zero',
        'employee_id'    => 'permit_empty|is_natural_no_zero',
        'label_attached' => 'permit_empty|in_list[Sudah,Belum]',
        'condition'      => 'permit_empty|in_list[baik,rusak,dipinjam,disposal]',
    ];

    protected $skipValidation = false;

    /**
     * List assets + relasi untuk index (bisa pakai search & filter kondisi).
     */
    public function getAssetsWithRelations(
    ?int $limit = null,
    ?int $offset = null,
    ?string $search = null,
    ?string $condition = null,
    bool $includeReplaced = true
) {
    $builder = $this->db->table($this->table . ' a');

    // 🔥 FILTER DIGANTI DIKONTROL DI SINI
    if (! $includeReplaced) {
        $builder->where('a.condition !=', 'diganti');
    }

    $builder->select('
        a.*,
        p.no_npd,
        p.procurement_date,
        u.name      AS unit_name,
        am.brand,
        am.model    AS model_name,
        e.name      AS employee_name
    ');

    $builder->join('procurements p', 'a.procurement_id = p.id', 'left');
    $builder->join('units u',        'a.unit_id        = u.id', 'left');
    $builder->join('asset_models am','a.asset_model_id = am.id','left');
    $builder->join('employees e',    'a.employee_id    = e.id', 'left');

    if ($search) {
        $builder->groupStart()
            ->like('a.asset_code', $search)
            ->orLike('am.brand', $search)
            ->orLike('am.model', $search)
            ->orLike('u.name', $search)
            ->orLike('e.name', $search)
            ->orLike('p.no_npd', $search)
        ->groupEnd();
    }

    if ($condition) {
        $builder->where('a.condition', $condition);
    }

    $builder->orderBy('a.id', 'DESC');

    if ($limit !== null) {
        $builder->limit($limit, $offset ?? 0);
    }

    return $builder->get()->getResultArray();
}



    /**
     * Satu asset + relasi untuk detail & edit.
     */
    public function findWithRelations($id)
    {
        $builder = $this->db->table($this->table . ' a');
        $builder->select('
            a.*,
            p.no_rab,
            p.no_npd,
            p.procurement_date,
            p.notes,
            u.name          AS unit_name,
            am.brand,
            am.model        AS model_name,
            e.name          AS employee_name,
            e.nipp          AS employee_nipp
        ');
        $builder->join('procurements p', 'a.procurement_id = p.id', 'left');
        $builder->join('units u',        'a.unit_id        = u.id', 'left');
        $builder->join('asset_models am','a.asset_model_id = am.id','left');
        $builder->join('employees e',    'a.employee_id    = e.id', 'left');
        $builder->where('a.id', $id);

        return $builder->get()->getRowArray();
    }

    /**
     * Helper cek foreign key.
     */
    public function foreignsExist(array $data): array
    {
        $errors = [];

        if (!empty($data['procurement_id'])) {
            if (! $this->db->table('procurements')->where('id', $data['procurement_id'])->countAllResults()) {
                $errors['procurement_id'] = 'Procurement tidak ditemukan';
            }
        }

        if (!empty($data['asset_model_id'])) {
            if (! $this->db->table('asset_models')->where('id', $data['asset_model_id'])->countAllResults()) {
                $errors['asset_model_id'] = 'Asset model tidak ditemukan';
            }
        }

        if (!empty($data['unit_id'])) {
            if (! $this->db->table('units')->where('id', $data['unit_id'])->countAllResults()) {
                $errors['unit_id'] = 'Unit tidak ditemukan';
            }
        }

        if (!empty($data['employee_id'])) {
            if (! $this->db->table('employees')->where('id', $data['employee_id'])->countAllResults()) {
                $errors['employee_id'] = 'Employee tidak ditemukan';
            }
        }

        return $errors;
    }
}
