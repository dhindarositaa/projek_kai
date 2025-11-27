<?php namespace App\Models;

use CodeIgniter\Model;

class DocumentsModel extends Model
{
    protected $table      = 'documents';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'asset_id',
        'procurement_id',
        'doc_type',
        'doc_number',
        'doc_link',
        'uploaded_at',
        'created_at',
        'updated_at'
    ];

    // jika tabel memiliki timestamp field custom gunakan ini (sesuaikan jika tidak)
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari dokumen BAST/WO/FILE berdasarkan procurement_id dulu,
     * fallback ke asset_id bila tidak ada.
     * Mengembalikan row array atau null.
     */
    public function findBastByProcurementOrAsset($procurementId = null, $assetId = null)
    {
        // prioritas: procurement_id
        if (!empty($procurementId)) {
            $q = $this->where('procurement_id', $procurementId)
                      ->like('doc_type', 'BAST', 'both')
                      ->orderBy('id', 'DESC')
                      ->first();
            if ($q) return $q;
        }

        // fallback: cari berdasarkan asset_id
        if (!empty($assetId)) {
            $q = $this->where('asset_id', $assetId)
                      ->like('doc_type', 'BAST', 'both')
                      ->orderBy('id', 'DESC')
                      ->first();
            if ($q) return $q;
        }

        // jika tidak ditemukan dokumen dengan doc_type BAST, coba ambil dokumen apapun terkait procurement_id/asset_id
        if (!empty($procurementId)) {
            $q = $this->where('procurement_id', $procurementId)
                      ->orderBy('id', 'DESC')
                      ->first();
            if ($q) return $q;
        }
        if (!empty($assetId)) {
            $q = $this->where('asset_id', $assetId)
                      ->orderBy('id', 'DESC')
                      ->first();
            if ($q) return $q;
        }

        return null;
    }
}
