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

    /**
     * Hitung nilai akhir dan nilai huruf berdasarkan komponen nilai
     */
    public function calculateNilai()
    {
        // Bobot: Sikap 10%, UTS 20%, UAS 30%, Tugas Akhir 25%, Partisipatif 15%
        $nilai_akhir = (
            ($this->nilai_sikap ?? 0) * 0.10 +
            ($this->nilai_uts ?? 0) * 0.20 +
            ($this->nilai_uas ?? 0) * 0.30 +
            ($this->nilai_tugas_akhir ?? 0) * 0.25 +
            ($this->nilai_partisipatif ?? 0) * 0.15
        );

        $this->nilai_akhir = round($nilai_akhir, 2);

        // Konversi ke huruf
        if ($nilai_akhir >= 85) {
            $this->nilai_huruf = 'A';
        } elseif ($nilai_akhir >= 75) {
            $this->nilai_huruf = 'B';
        } elseif ($nilai_akhir >= 65) {
            $this->nilai_huruf = 'C';
        } elseif ($nilai_akhir >= 55) {
            $this->nilai_huruf = 'D';
        } else {
            $this->nilai_huruf = 'E';
        }

        $this->save();
    }
}
