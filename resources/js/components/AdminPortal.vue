<template>
  <div class="admin-container">
    <!-- Login Form -->
    <div v-if="!isLoggedIn" class="login-container">
      <div class="login-box">
        <h2>🔐 Login Admin</h2>
        <form @submit.prevent="login">
          <div class="form-group">
            <label>NIM Admin:</label>
            <input v-model="loginForm.nim" type="text" required />
          </div>
          <div class="form-group">
            <label>Password:</label>
            <input v-model="loginForm.password" type="password" required />
          </div>
          <button type="submit" :disabled="loading" class="btn-login">
            {{ loading ? 'Memproses...' : 'Login' }}
          </button>
          <div v-if="loginError" class="error-message">{{ loginError }}</div>
        </form>
      </div>
    </div>

    <!-- Admin Dashboard -->
    <div v-else class="admin-dashboard">
      <div class="dashboard-header">
        <h1>📊 Admin Portal - Kelola Nilai Mahasiswa</h1>
        <button @click="logout" class="btn-logout">Keluar</button>
      </div>

      <!-- Search Bar -->
      <div class="search-section">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Cari berdasarkan nama mahasiswa..."
          class="search-input"
        />
        <span class="student-count">Total: {{ filteredStudents.length }} mahasiswa</span>
        <button @click="toggleCreateForm" class="btn-create">➕ Tambah Mahasiswa</button>
      </div>

      <!-- Create Form Modal -->
      <div v-if="showCreateForm" class="modal-overlay" @click.self="closeCreateForm">
        <div class="modal-content">
          <div class="modal-header">
            <h2>➕ Tambah Mahasiswa Baru</h2>
            <button @click="closeCreateForm" class="btn-close-modal">✕</button>
          </div>
          <form @submit.prevent="createStudent" class="create-form">
            <div class="form-row">
              <div class="form-group">
                <label>NIM <span style="color: red;">*</span>:</label>
                <input v-model="createForm.nim" type="text" required />
              </div>
              <div class="form-group">
                <label>Nama <span style="color: red;">*</span>:</label>
                <input v-model="createForm.nama" type="text" required />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Kelas <span style="color: red;">*</span>:</label>
                <input v-model="createForm.kelas" type="text" required />
              </div>
              <div class="form-group">
                <label>Password <span style="color: red;">*</span>:</label>
                <input v-model="createForm.password" type="text" required />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Nilai Sikap:</label>
                <input v-model.number="createForm.nilai_sikap" type="number" min="0" max="100" />
              </div>
              <div class="form-group">
                <label>Nilai UTS:</label>
                <input v-model.number="createForm.nilai_uts" type="number" min="0" max="100" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Nilai UAS:</label>
                <input v-model.number="createForm.nilai_uas" type="number" min="0" max="100" />
              </div>
              <div class="form-group">
                <label>Nilai Tugas Akhir:</label>
                <input v-model.number="createForm.nilai_tugas_akhir" type="number" min="0" max="100" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Nilai Partisipatif:</label>
                <input v-model.number="createForm.nilai_partisipatif" type="number" min="0" max="100" />
              </div>
            </div>
            <div v-if="createError" class="error-message">{{ createError }}</div>
            <div class="modal-actions">
              <button type="submit" :disabled="loading" class="btn-save">💾 Simpan</button>
              <button type="button" @click="closeCreateForm" class="btn-cancel">❌ Batal</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Students Table -->
      <div class="table-container">
        <table class="students-table">
          <thead>
            <tr>
              <th>NIM</th>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Sikap</th>
              <th>UTS</th>
              <th>UAS</th>
              <th>Tugas Akhir</th>
              <th>Partisipatif</th>
              <th>Nilai Akhir</th>
              <th>Nilai Huruf</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="11" class="loading-cell">Memuat data...</td>
            </tr>
            <tr v-else-if="filteredStudents.length === 0">
              <td colspan="11" class="empty-cell">Tidak ada data mahasiswa</td>
            </tr>
            <tr v-else v-for="student in filteredStudents" :key="student.id">
              <td>{{ student.nim }}</td>
              <td>{{ student.nama }}</td>
              <td>{{ student.kelas }}</td>
              <td v-if="editingId !== student.id">{{ student.nilai_sikap ?? '-' }}</td>
              <td v-else>
                <input v-model.number="editForm.nilai_sikap" type="number" min="0" max="100" class="edit-input" />
              </td>
              <td v-if="editingId !== student.id">{{ student.nilai_uts ?? '-' }}</td>
              <td v-else>
                <input v-model.number="editForm.nilai_uts" type="number" min="0" max="100" class="edit-input" />
              </td>
              <td v-if="editingId !== student.id">{{ student.nilai_uas ?? '-' }}</td>
              <td v-else>
                <input v-model.number="editForm.nilai_uas" type="number" min="0" max="100" class="edit-input" />
              </td>
              <td v-if="editingId !== student.id">{{ student.nilai_tugas_akhir ?? '-' }}</td>
              <td v-else>
                <input v-model.number="editForm.nilai_tugas_akhir" type="number" min="0" max="100" class="edit-input" />
              </td>
              <td v-if="editingId !== student.id">{{ student.nilai_partisipatif ?? '-' }}</td>
              <td v-else>
                <input v-model.number="editForm.nilai_partisipatif" type="number" min="0" max="100" class="edit-input" />
              </td>
              <td><strong>{{ student.nilai_akhir ?? '-' }}</strong></td>
              <td><strong class="grade-badge" :class="getGradeClass(student.nilai_huruf)">{{ student.nilai_huruf ?? '-' }}</strong></td>
              <td>
                <div v-if="editingId !== student.id" class="action-buttons">
                  <button @click="startEdit(student)" class="btn-edit">✏️ Edit</button>
                </div>
                <div v-else class="action-buttons">
                  <button @click="saveEdit(student.id)" class="btn-save">💾 Simpan</button>
                  <button @click="cancelEdit" class="btn-cancel">❌ Batal</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const isLoggedIn = ref(false);
const loading = ref(false);
const loginError = ref('');
const students = ref([]);
const searchQuery = ref('');
const editingId = ref(null);
const showCreateForm = ref(false);
const createError = ref('');
const editForm = ref({
  nilai_sikap: null,
  nilai_uts: null,
  nilai_uas: null,
  nilai_tugas_akhir: null,
  nilai_partisipatif: null,
});

const createForm = ref({
  nim: '',
  nama: '',
  kelas: '',
  password: '',
  nilai_sikap: null,
  nilai_uts: null,
  nilai_uas: null,
  nilai_tugas_akhir: null,
  nilai_partisipatif: null,
});

const loginForm = ref({
  nim: '',
  password: '',
});

const filteredStudents = computed(() => {
  if (!searchQuery.value) {
    return students.value;
  }
  const query = searchQuery.value.toLowerCase();
  return students.value.filter(student =>
    student.nama.toLowerCase().includes(query)
  );
});

const login = async () => {
  loading.value = true;
  loginError.value = '';
  
  try {
    const response = await axios.post('/api/admin/login', loginForm.value);
    
    if (response.data.is_admin) {
      isLoggedIn.value = true;
      await fetchStudents();
    }
  } catch (error) {
    loginError.value = error.response?.data?.message || 'Login gagal';
  } finally {
    loading.value = false;
  }
};

const logout = () => {
  isLoggedIn.value = false;
  students.value = [];
  searchQuery.value = '';
  editingId.value = null;
};

const fetchStudents = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/students', {
      headers: {
        'X-Admin-Auth': 'hasyahanin2025'
      }
    });
    students.value = response.data;
  } catch (error) {
    console.error('Error fetching students:', error);
    alert('Gagal memuat data mahasiswa');
  } finally {
    loading.value = false;
  }
};

const startEdit = (student) => {
  editingId.value = student.id;
  editForm.value = {
    nilai_sikap: student.nilai_sikap,
    nilai_uts: student.nilai_uts,
    nilai_uas: student.nilai_uas,
    nilai_tugas_akhir: student.nilai_tugas_akhir,
    nilai_partisipatif: student.nilai_partisipatif,
  };
};

const cancelEdit = () => {
  editingId.value = null;
  editForm.value = {
    nilai_sikap: null,
    nilai_uts: null,
    nilai_uas: null,
    nilai_tugas_akhir: null,
    nilai_partisipatif: null,
  };
};

const saveEdit = async (studentId) => {
  loading.value = true;
  try {
    const response = await axios.put(`/api/admin/students/${studentId}`, editForm.value, {
      headers: {
        'X-Admin-Auth': 'hasyahanin2025'
      }
    });
    
    // Update data di local state
    const index = students.value.findIndex(s => s.id === studentId);
    if (index !== -1) {
      students.value[index] = response.data.student;
    }
    
    editingId.value = null;
    alert('Nilai berhasil diupdate!');
  } catch (error) {
    console.error('Error updating nilai:', error);
    alert('Gagal mengupdate nilai: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const getGradeClass = (grade) => {
  const classes = {
    'A': 'grade-a',
    'B': 'grade-b',
    'C': 'grade-c',
    'D': 'grade-d',
    'E': 'grade-e',
  };
  return classes[grade] || '';
};

const toggleCreateForm = () => {
  showCreateForm.value = !showCreateForm.value;
  if (!showCreateForm.value) {
    resetCreateForm();
  }
};

const closeCreateForm = () => {
  showCreateForm.value = false;
  resetCreateForm();
};

const resetCreateForm = () => {
  createForm.value = {
    nim: '',
    nama: '',
    kelas: '',
    password: '',
    nilai_sikap: null,
    nilai_uts: null,
    nilai_uas: null,
    nilai_tugas_akhir: null,
    nilai_partisipatif: null,
  };
  createError.value = '';
};

const createStudent = async () => {
  loading.value = true;
  createError.value = '';
  
  try {
    const response = await axios.post('/api/admin/students', createForm.value, {
      headers: {
        'X-Admin-Auth': 'hasyahanin2025'
      }
    });
    
    // Add new student to list
    students.value.push(response.data.student);
    students.value.sort((a, b) => a.nama.localeCompare(b.nama));
    
    closeCreateForm();
    alert('Mahasiswa berhasil ditambahkan!');
  } catch (error) {
    console.error('Error creating student:', error);
    createError.value = error.response?.data?.message || 'Gagal menambahkan mahasiswa';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  // Check if already logged in (optional, bisa ditambahkan session check)
});
</script>

<style scoped>
.admin-container {
  min-height: 100vh;
  background: #f5f5f5;
}

/* Login Styles */
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-box {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  width: 100%;
  max-width: 400px;
}

.login-box h2 {
  margin: 0 0 30px 0;
  text-align: center;
  color: #333;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.form-group input {
  width: 100%;
  padding: 12px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.btn-login {
  width: 100%;
  padding: 12px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-login:hover:not(:disabled) {
  background: #5568d3;
}

.btn-login:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error-message {
  margin-top: 15px;
  padding: 10px;
  background: #fee;
  color: #c33;
  border-radius: 6px;
  text-align: center;
}

/* Dashboard Styles */
.admin-dashboard {
  max-width: 1400px;
  margin: 0 auto;
  padding: 30px;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: white;
  padding: 20px 30px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-header h1 {
  margin: 0;
  color: #333;
  font-size: 28px;
}

.btn-logout {
  padding: 10px 20px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.btn-logout:hover {
  background: #c82333;
}

.search-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.btn-create {
  padding: 10px 20px;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  white-space: nowrap;
}

.btn-create:hover {
  background: #218838;
}

.search-input {
  flex: 1;
  padding: 12px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
}

.student-count {
  font-weight: 600;
  color: #666;
}

.table-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.students-table {
  width: 100%;
  border-collapse: collapse;
}

.students-table thead {
  background: #667eea;
  color: white;
}

.students-table th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  font-size: 14px;
}

.students-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
}

.students-table tbody tr:hover {
  background: #f8f9fa;
}

.loading-cell,
.empty-cell {
  text-align: center;
  padding: 40px;
  color: #666;
}

.edit-input {
  width: 70px;
  padding: 6px;
  border: 1px solid #ddd;
  border-radius: 4px;
  text-align: center;
}

.grade-badge {
  padding: 4px 12px;
  border-radius: 4px;
  font-size: 14px;
}

.grade-a { background: #d4edda; color: #155724; }
.grade-b { background: #d1ecf1; color: #0c5460; }
.grade-c { background: #fff3cd; color: #856404; }
.grade-d { background: #f8d7da; color: #721c24; }
.grade-e { background: #f5c6cb; color: #721c24; }

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-edit,
.btn-save,
.btn-cancel {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
}

.btn-edit {
  background: #ffc107;
  color: #000;
}

.btn-edit:hover {
  background: #e0a800;
}

.btn-save {
  background: #28a745;
  color: white;
}

.btn-save:hover {
  background: #218838;
}

.btn-cancel {
  background: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background: #5a6268;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  border-bottom: 1px solid #eee;
}

.modal-header h2 {
  margin: 0;
  color: #333;
}

.btn-close-modal {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close-modal:hover {
  color: #333;
}

.create-form {
  padding: 30px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.create-form .form-group {
  margin-bottom: 0;
}

.create-form .form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.create-form .form-group input {
  width: 100%;
  padding: 10px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}

.create-form .form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.modal-actions .btn-save,
.modal-actions .btn-cancel {
  padding: 10px 24px;
  font-size: 14px;
}
</style>

