<template>
  <div class="tab-content">
    <div class="section-header">
      <h2>👥 Kelola Nilai Mahasiswa</h2>
      <button @click="toggleCreateStudent" class="btn-create">➕ Tambah Mahasiswa</button>
    </div>

    <!-- Search Students -->
    <div class="search-section">
      <input
        v-model="studentSearch"
        type="text"
        placeholder="🔍 Cari berdasarkan nama atau kelas mahasiswa..."
        class="search-input"
      />
      <span class="count-badge">Total: {{ filteredStudents.length }} mahasiswa</span>
    </div>

    <!-- Create Student Modal -->
    <div v-if="showCreateStudentForm" class="modal-overlay" @click.self="closeCreateStudentForm">
      <div class="modal-content">
        <div class="modal-header">
          <h2>➕ Tambah Mahasiswa Baru</h2>
          <button @click="closeCreateStudentForm" class="btn-close-modal">✕</button>
        </div>
        <form @submit.prevent="createStudent" class="create-form">
          <div class="form-row">
            <div class="form-group">
              <label>NIM <span style="color: red;">*</span>:</label>
              <input v-model="createStudentForm.nim" type="text" required />
            </div>
            <div class="form-group">
              <label>Nama <span style="color: red;">*</span>:</label>
              <input v-model="createStudentForm.nama" type="text" required />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Kelas <span style="color: red;">*</span>:</label>
              <input v-model="createStudentForm.kelas" type="text" required />
            </div>
            <div class="form-group">
              <label>Password <span style="color: red;">*</span>:</label>
              <input v-model="createStudentForm.password" type="text" required />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Nilai Sikap:</label>
              <input v-model.number="createStudentForm.nilai_sikap" type="number" min="0" max="100" />
            </div>
            <div class="form-group">
              <label>Nilai UTS:</label>
              <input v-model.number="createStudentForm.nilai_uts" type="number" min="0" max="100" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Nilai UAS:</label>
              <input v-model.number="createStudentForm.nilai_uas" type="number" min="0" max="100" />
            </div>
            <div class="form-group">
              <label>Nilai Tugas Akhir:</label>
              <input v-model.number="createStudentForm.nilai_tugas_akhir" type="number" min="0" max="100" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Nilai Partisipatif:</label>
              <input v-model.number="createStudentForm.nilai_partisipatif" type="number" min="0" max="100" />
            </div>
          </div>
          <div v-if="createStudentError" class="error-message">{{ createStudentError }}</div>
          <div class="modal-actions">
            <button type="submit" :disabled="loading" class="btn-save">💾 Simpan</button>
            <button type="button" @click="closeCreateStudentForm" class="btn-cancel">❌ Batal</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Students Table -->
    <div class="table-container">
      <div v-if="selectedStudents.length > 0" class="bulk-actions">
        <span class="selected-count">✅ {{ selectedStudents.length }} mahasiswa dipilih</span>
        <button @click="deleteSelectedStudents" class="btn-delete-bulk">🗑️ Hapus Selected</button>
        <button @click="clearSelection" class="btn-cancel">❌ Batal</button>
      </div>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 50px;">
              <input 
                type="checkbox" 
                :checked="isAllSelected" 
                @change="toggleSelectAll"
                class="checkbox-select-all"
              />
            </th>
            <th @click="sortBy('nim')" class="sortable-header">
              NIM
              <span class="sort-icon">
                <span v-if="sortField === 'nim'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nama')" class="sortable-header">
              Nama
              <span class="sort-icon">
                <span v-if="sortField === 'nama'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('kelas')" class="sortable-header">
              Kelas
              <span class="sort-icon">
                <span v-if="sortField === 'kelas'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_sikap')" class="sortable-header">
              Sikap
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_sikap'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_uts')" class="sortable-header">
              UTS
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_uts'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_uas')" class="sortable-header">
              UAS
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_uas'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_tugas_akhir')" class="sortable-header">
              Tugas Akhir
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_tugas_akhir'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_partisipatif')" class="sortable-header">
              Partisipatif
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_partisipatif'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_akhir')" class="sortable-header">
              Nilai Akhir
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_akhir'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th @click="sortBy('nilai_huruf')" class="sortable-header">
              Nilai Huruf
              <span class="sort-icon">
                <span v-if="sortField === 'nilai_huruf'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
                <span v-else class="sort-placeholder">⇅</span>
              </span>
            </th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loadingStudents">
            <td colspan="12" class="loading-cell">Memuat data...</td>
          </tr>
          <tr v-else-if="filteredStudents.length === 0">
            <td colspan="12" class="empty-cell">Tidak ada data mahasiswa</td>
          </tr>
          <tr v-else v-for="student in filteredStudents" :key="student.id" :class="{ 'row-selected': isStudentSelected(student.id) }">
            <td>
              <input 
                type="checkbox" 
                :checked="isStudentSelected(student.id)"
                @change="toggleStudentSelection(student.id)"
                class="checkbox-row"
              />
            </td>
            <td>{{ student.nim }}</td>
            <td>{{ student.nama }}</td>
            <td>{{ student.kelas }}</td>
            <td v-if="editingStudentId !== student.id">{{ student.nilai_sikap ?? '-' }}</td>
            <td v-else>
              <input v-model.number="editStudentForm.nilai_sikap" type="number" min="0" max="100" class="edit-input" />
            </td>
            <td v-if="editingStudentId !== student.id">{{ student.nilai_uts ?? '-' }}</td>
            <td v-else>
              <input v-model.number="editStudentForm.nilai_uts" type="number" min="0" max="100" class="edit-input" />
            </td>
            <td v-if="editingStudentId !== student.id">{{ student.nilai_uas ?? '-' }}</td>
            <td v-else>
              <input v-model.number="editStudentForm.nilai_uas" type="number" min="0" max="100" class="edit-input" />
            </td>
            <td v-if="editingStudentId !== student.id">{{ student.nilai_tugas_akhir ?? '-' }}</td>
            <td v-else>
              <input v-model.number="editStudentForm.nilai_tugas_akhir" type="number" min="0" max="100" class="edit-input" />
            </td>
            <td v-if="editingStudentId !== student.id">{{ student.nilai_partisipatif ?? '-' }}</td>
            <td v-else>
              <input v-model.number="editStudentForm.nilai_partisipatif" type="number" min="0" max="100" class="edit-input" />
            </td>
            <td><strong v-if="student.nama!='Admin'">{{ student.nilai_akhir ?? '-' }}</strong></td>
            <td>
              <strong v-if="student.nama!='Admin'" class="grade-badge" :class="getGradeClass(student.nilai_huruf)">{{ student.nilai_huruf ?? '-' }}</strong>
            </td>
            <td>
              <div v-if="editingStudentId !== student.id" class="action-buttons">
                <button @click="startEditStudent(student)" class="btn-edit">✏️ Edit</button>
              </div>
              <div v-else class="action-buttons">
                <button @click="saveEditStudent(student.id)" class="btn-save">💾 Simpan</button>
                <button @click="cancelEditStudent" class="btn-cancel">❌ Batal</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Students state
const students = ref([]);
const loadingStudents = ref(false);
const studentSearch = ref('');
const showCreateStudentForm = ref(false);
const createStudentError = ref('');
const editingStudentId = ref(null);
const selectedStudents = ref([]);
const sortField = ref(null);
const sortDirection = ref('asc');
const loading = ref(false);
const createStudentForm = ref({
  nim: '', nama: '', kelas: '', password: '',
  nilai_sikap: null, nilai_uts: null, nilai_uas: null,
  nilai_tugas_akhir: null, nilai_partisipatif: null,
});
const editStudentForm = ref({
  nilai_sikap: null, nilai_uts: null, nilai_uas: null,
  nilai_tugas_akhir: null, nilai_partisipatif: null,
});

// Computed
const filteredStudents = computed(() => {
  let result = [...students.value];
  
  // Filter by search (nama or kelas)
  if (studentSearch.value) {
    const query = studentSearch.value.toLowerCase();
    result = result.filter(student =>
      student.nama.toLowerCase().includes(query) ||
      student.kelas.toLowerCase().includes(query)
    );
  }
  
  // Sort
  if (sortField.value) {
    result.sort((a, b) => {
      let aVal = a[sortField.value];
      let bVal = b[sortField.value];
      
      // Handle null/undefined values
      if (aVal == null) aVal = '';
      if (bVal == null) bVal = '';
      
      // Handle numeric values
      if (typeof aVal === 'number' && typeof bVal === 'number') {
        return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
      }
      
      // Handle string values
      aVal = String(aVal).toLowerCase();
      bVal = String(bVal).toLowerCase();
      
      if (sortDirection.value === 'asc') {
        return aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
      } else {
        return aVal > bVal ? -1 : aVal < bVal ? 1 : 0;
      }
    });
  }
  
  return result;
});

const isAllSelected = computed(() => {
  return filteredStudents.value.length > 0 && 
         filteredStudents.value.every(s => selectedStudents.value.includes(s.id));
});

const isStudentSelected = (studentId) => {
  return selectedStudents.value.includes(studentId);
};

// Functions
const fetchStudents = async () => {
  loadingStudents.value = true;
  try {
    const response = await axios.get('/api/admin/students', {
      headers: { 'X-Admin-Auth': 'hasyahanin2025' }
    });
    students.value = response.data;
  } catch (error) {
    console.error('Error fetching students:', error);
    alert('Gagal memuat data mahasiswa');
  } finally {
    loadingStudents.value = false;
  }
};

const toggleCreateStudent = () => {
  showCreateStudentForm.value = !showCreateStudentForm.value;
  if (!showCreateStudentForm.value) resetCreateStudentForm();
};

const closeCreateStudentForm = () => {
  showCreateStudentForm.value = false;
  resetCreateStudentForm();
};

const resetCreateStudentForm = () => {
  createStudentForm.value = {
    nim: '', nama: '', kelas: '', password: '',
    nilai_sikap: null, nilai_uts: null, nilai_uas: null,
    nilai_tugas_akhir: null, nilai_partisipatif: null,
  };
  createStudentError.value = '';
};

const createStudent = async () => {
  loading.value = true;
  createStudentError.value = '';
  try {
    const response = await axios.post('/api/admin/students', createStudentForm.value, {
      headers: { 'X-Admin-Auth': 'hasyahanin2025' }
    });
    students.value.push(response.data.student);
    students.value.sort((a, b) => a.nama.localeCompare(b.nama));
    closeCreateStudentForm();
    alert('Mahasiswa berhasil ditambahkan!');
  } catch (error) {
    createStudentError.value = error.response?.data?.message || 'Gagal menambahkan mahasiswa';
  } finally {
    loading.value = false;
  }
};

const startEditStudent = (student) => {
  editingStudentId.value = student.id;
  editStudentForm.value = {
    nilai_sikap: student.nilai_sikap,
    nilai_uts: student.nilai_uts,
    nilai_uas: student.nilai_uas,
    nilai_tugas_akhir: student.nilai_tugas_akhir,
    nilai_partisipatif: student.nilai_partisipatif,
  };
};

const cancelEditStudent = () => {
  editingStudentId.value = null;
  editStudentForm.value = {
    nilai_sikap: null, nilai_uts: null, nilai_uas: null,
    nilai_tugas_akhir: null, nilai_partisipatif: null,
  };
};

const saveEditStudent = async (studentId) => {
  loading.value = true;
  try {
    const response = await axios.put(`/api/admin/students/${studentId}`, editStudentForm.value, {
      headers: { 'X-Admin-Auth': 'hasyahanin2025' }
    });
    const index = students.value.findIndex(s => s.id === studentId);
    if (index !== -1) {
      students.value[index] = response.data.student;
    }
    editingStudentId.value = null;
    alert('Nilai berhasil diupdate!');
  } catch (error) {
    alert('Gagal mengupdate nilai: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    filteredStudents.value.forEach(student => {
      const index = selectedStudents.value.indexOf(student.id);
      if (index > -1) {
        selectedStudents.value.splice(index, 1);
      }
    });
  } else {
    filteredStudents.value.forEach(student => {
      if (!selectedStudents.value.includes(student.id)) {
        selectedStudents.value.push(student.id);
      }
    });
  }
};

const toggleStudentSelection = (studentId) => {
  const index = selectedStudents.value.indexOf(studentId);
  if (index > -1) {
    selectedStudents.value.splice(index, 1);
  } else {
    selectedStudents.value.push(studentId);
  }
};

const clearSelection = () => {
  selectedStudents.value = [];
};

const deleteSelectedStudents = async () => {
  if (selectedStudents.value.length === 0) return;
  
  const count = selectedStudents.value.length;
  if (!confirm(`Yakin ingin menghapus ${count} mahasiswa yang dipilih?`)) return;
  
  loading.value = true;
  try {
    await axios.delete('/api/admin/students', {
      data: { ids: selectedStudents.value },
      headers: { 'X-Admin-Auth': 'hasyahanin2025' }
    });
    
    selectedStudents.value.forEach(id => {
      const index = students.value.findIndex(s => s.id === id);
      if (index > -1) {
        students.value.splice(index, 1);
      }
    });
    
    selectedStudents.value = [];
    alert(`${count} mahasiswa berhasil dihapus!`);
  } catch (error) {
    alert('Gagal menghapus mahasiswa: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

const getGradeClass = (grade) => {
  const classes = { 'A': 'grade-a', 'B': 'grade-b', 'C': 'grade-c', 'D': 'grade-d', 'E': 'grade-e' };
  return classes[grade] || '';
};

onMounted(() => {
  fetchStudents();
});
</script>

<style scoped>
.tab-content {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #f0f0f0;
}

.section-header h2 {
  margin: 0;
  font-size: 28px;
  color: #333;
  font-weight: 700;
}

.search-section {
  display: flex;
  gap: 15px;
  align-items: center;
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 6px;
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

.count-badge {
  padding: 8px 16px;
  background: #667eea;
  color: white;
  border-radius: 20px;
  font-weight: 600;
  font-size: 14px;
  white-space: nowrap;
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

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table thead {
  background: #667eea;
  color: white;
}

.data-table th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  font-size: 14px;
}

.sortable-header {
  cursor: pointer;
  user-select: none;
  position: relative;
  transition: background-color 0.2s;
}

.sortable-header:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.sort-icon {
  margin-left: 8px;
  font-size: 12px;
  display: inline-block;
  min-width: 16px;
}

.sort-placeholder {
  opacity: 0.5;
  font-size: 10px;
}

.data-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
}

.data-table tbody tr:hover {
  background: #f8f9fa;
}

.data-table tbody tr.row-selected {
  background: #e3f2fd;
}

.data-table tbody tr.row-selected:hover {
  background: #bbdefb;
}

.loading-cell,
.empty-cell {
  text-align: center;
  padding: 40px;
  color: #666;
}

.checkbox-select-all,
.checkbox-row {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #667eea;
}

.bulk-actions {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  background: #e3f2fd;
  border-radius: 8px;
  margin-bottom: 15px;
  border: 2px solid #667eea;
}

.selected-count {
  font-weight: 600;
  color: #1976d2;
  font-size: 14px;
}

.btn-delete-bulk {
  padding: 10px 20px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
}

.btn-delete-bulk:hover {
  background: #c82333;
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

.edit-input {
  width: 70px;
  padding: 6px;
  border: 1px solid #ddd;
  border-radius: 4px;
  text-align: center;
}

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
  font-size: 24px;
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.error-message {
  margin-top: 15px;
  padding: 10px;
  background: #fee;
  color: #c33;
  border-radius: 6px;
  text-align: center;
}
</style>

