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

    /**
     * API: Login admin dengan NIM dan password khusus
     */
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek kredensial admin khusus
        if ($credentials['nim'] === '202407199804091068' && $credentials['password'] === 'hasyahanin2025') {
            return response()->json([
                'message' => 'Login admin berhasil',
                'is_admin' => true,
            ]);
        }

        return response()->json([
            'message' => 'NIM atau password admin salah',
        ], 401);
    }

    /**
     * API: Get semua data mahasiswa (hanya untuk admin)
     */
    public function getAllStudents(Request $request)
    {
        // Validasi admin (sederhana, bisa ditingkatkan dengan session/token)
        $isAdmin = $request->header('X-Admin-Auth') === 'hasyahanin2025';
        
        if (!$isAdmin) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $students = Student::orderBy('nama', 'asc')->get();
        
        return response()->json($students);
    }

    /**
     * API: Create mahasiswa baru (hanya untuk admin)
     */
    public function createStudent(Request $request)
    {
        // Validasi admin
        $isAdmin = $request->header('X-Admin-Auth') === 'hasyahanin2025';
        
        if (!$isAdmin) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'nim' => 'required|string|unique:students,nim',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'password' => 'required|string',
            'nilai_sikap' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'nilai_tugas_akhir' => 'nullable|numeric|min:0|max:100',
            'nilai_partisipatif' => 'nullable|numeric|min:0|max:100',
        ]);

        $student = Student::create($validated);

        // Calculate nilai akhir dan nilai huruf jika ada nilai
        if ($student->nilai_sikap || $student->nilai_uts || $student->nilai_uas || 
            $student->nilai_tugas_akhir || $student->nilai_partisipatif) {
            $student->calculateNilai();
        }

        return response()->json([
            'message' => 'Mahasiswa berhasil ditambahkan',
            'student' => $student,
        ], 201);
    }

    /**
     * API: Update nilai mahasiswa (hanya untuk admin)
     */
    public function updateNilai(Request $request, $id)
    {
        // Validasi admin
        $isAdmin = $request->header('X-Admin-Auth') === 'hasyahanin2025';
        
        if (!$isAdmin) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'nilai_sikap' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'nilai_tugas_akhir' => 'nullable|numeric|min:0|max:100',
            'nilai_partisipatif' => 'nullable|numeric|min:0|max:100',
        ]);

        // Update nilai
        $student->update($validated);

        // Recalculate nilai akhir dan nilai huruf
        $student->calculateNilai();

        return response()->json([
            'message' => 'Nilai berhasil diupdate',
            'student' => $student,
        ]);
    }
}
