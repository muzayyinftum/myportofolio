<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'nama',
        'kelas',
        'password', // disimpan sebagai string biasa (TIDAK DI-HASH)
        'nilai_sikap',
        'nilai_uts',
        'nilai_uas',
        'nilai_tugas_akhir',
        'nilai_partisipatif',
        'nilai_akhir',
        'nilai_huruf',
    ];

    // Jangan sembunyikan password jika ingin terlihat saat debug / admin (opsional)
}
