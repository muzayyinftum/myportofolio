<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentPortalController extends Controller
{
    /**
     * API: Login portal nilai dengan NIM + password.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('nim', $credentials['nim'])->first();

        // PERINGATAN: perbandingan password plain-text, hanya untuk demo / lingkungan non-produksi
        if (! $student || $credentials['password'] !== $student->password) {
            return response()->json([
                'message' => 'NIM atau password salah',
            ], 401);
        }

        return response()->json([
            'nim' => $student->nim,
            'nama' => $student->nama,
            'kelas' => $student->kelas,
            'nilai_sikap' => $student->nilai_sikap,
            'nilai_uts' => $student->nilai_uts,
            'nilai_uas' => $student->nilai_uas,
            'nilai_tugas_akhir' => $student->nilai_tugas_akhir,
            'nilai_partisipatif' => $student->nilai_partisipatif,
            'nilai_akhir' => $student->nilai_akhir,
            'nilai_huruf' => $student->nilai_huruf,
        ]);
    }
}
