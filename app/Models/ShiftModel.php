<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table            = 'shift';
    protected $primaryKey       = 'id_shift';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_kasir',
        'waktu_mulai',
        'waktu_selesai',
        'total_kas_tunai',
        'total_kas_qris',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    // Tabel shift tidak memiliki kolom updated_at.
    protected $updatedField  = '';
}
