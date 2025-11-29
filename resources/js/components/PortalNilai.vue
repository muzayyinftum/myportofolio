<template>
  <div class="portal-container">
    <h2>Portal Nilai Mahasiswa</h2>

    <!-- Form Login -->
    <div v-if="!student" class="login-card">
      <h3>Login Mahasiswa</h3>
      <form @submit.prevent="login">
        <div class="form-group">
          <label>NIM</label>
          <input v-model="nim" type="text" required />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="password" type="password" required />
        </div>
        <button type="submit" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Masuk' }}
        </button>
        <p v-if="error" class="error-msg">{{ error }}</p>
      </form>
      <p class="hint">
        * Hubungi admin / dosen untuk mendapatkan NIM & password portal nilai.
      </p>
    </div>

    <!-- Tabel Nilai -->
    <div v-else class="nilai-card">
      <div class="header">
        <div>
          <h3>Detail Nilai</h3>
          <p><strong>NIM:</strong> {{ student.nim }}</p>
          <p><strong>Nama:</strong> {{ student.nama }}</p>
          <p><strong>Kelas:</strong> {{ student.kelas || '-' }}</p>
        </div>
        <button @click="logout" class="logout-btn">Keluar</button>
      </div>

      <table class="nilai-table">
        <thead>
          <tr>
            <th>Komponen</th>
            <th>Nilai</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Nilai Sikap</td>
            <td>{{ formatNilai(student.nilai_sikap) }}</td>
          </tr>
          <tr>
            <td>UTS</td>
            <td>{{ formatNilai(student.nilai_uts) }}</td>
          </tr>
          <tr>
            <td>UAS</td>
            <td>{{ formatNilai(student.nilai_uas) }}</td>
          </tr>
          <tr>
            <td>Tugas Akhir</td>
            <td>{{ formatNilai(student.nilai_tugas_akhir) }}</td>
          </tr>
          <tr>
            <td>Partisipatif</td>
            <td>{{ formatNilai(student.nilai_partisipatif) }}</td>
          </tr>
          <tr class="total-row">
            <td>Nilai Akhir</td>
            <td>{{ formatNilai(student.nilai_akhir) }}</td>
          </tr>
          <tr class="total-row">
            <td>Nilai Huruf</td>
            <td>{{ student.nilai_huruf || '-' }}</td>
          </tr>
        </tbody>
      </table>

      <p class="note">
        * Jika ada ketidaksesuaian nilai, segera hubungi dosen pengampu.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const nim = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const student = ref(null);

const login = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await axios.post('/api/portal/login', {
      nim: nim.value,
      password: password.value,
    });
    student.value = response.data;
  } catch (err) {
    console.error(err);
    error.value = err.response?.data?.message || 'Gagal login. Periksa NIM dan password.';
  } finally {
    loading.value = false;
  }
};

const logout = () => {
  student.value = null;
  password.value = '';
};

const formatNilai = (n) => {
  if (n === null || n === undefined) return '-';
  return Number(n).toFixed(2);
};
</script>

<style scoped>
.portal-container {
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

h2 {
  text-align: center;
  margin-bottom: 24px;
  color: #333;
}

.login-card,
.nilai-card {
  background: #ffffff;
  border-radius: 8px;
  padding: 20px 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-group {
  margin-bottom: 16px;
}

label {
  display: block;
  margin-bottom: 4px;
  font-weight: 600;
  font-size: 14px;
}

input {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}

button {
  background: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
}

button:disabled {
  background: #a0c4ff;
  cursor: not-allowed;
}

.error-msg {
  margin-top: 10px;
  color: #d93025;
  font-size: 14px;
}

.hint {
  margin-top: 12px;
  font-size: 12px;
  color: #666;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.logout-btn {
  background: #dc3545;
}

.nilai-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.nilai-table th,
.nilai-table td {
  border: 1px solid #ddd;
  padding: 8px 10px;
  font-size: 14px;
}

.nilai-table th {
  background: #f5f5f5;
  text-align: left;
}

.total-row td {
  font-weight: 600;
  background: #f9f9f9;
}

.note {
  margin-top: 12px;
  font-size: 12px;
  color: #666;
}
</style>


