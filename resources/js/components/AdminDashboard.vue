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
        <h1>📊 Admin Dashboard</h1>
        <button @click="logout" class="btn-logout">Keluar</button>
      </div>

      <!-- Tabs Navigation -->
      <div class="tabs-nav">
        <button 
          @click="activeTab = 'publications'" 
          :class="['tab-button', { active: activeTab === 'publications' }]"
        >
          📄 Kelola Publikasi
        </button>
        <button 
          @click="activeTab = 'students'" 
          :class="['tab-button', { active: activeTab === 'students' }]"
        >
          👥 Kelola Nilai Mahasiswa
        </button>
      </div>

      <!-- Tab Content: Publications -->
      <div v-if="activeTab === 'publications'" class="tab-content">
        <div class="section-header">
          <h2>📄 Kelola Publikasi (Jurnal/Konferensi/HaKI)</h2>
          <button @click="toggleCreatePublication" class="btn-create">➕ Tambah Publikasi</button>
        </div>

        <!-- Search Publications -->
        <div class="search-section">
          <input
            v-model="publicationSearch"
            type="text"
            placeholder="🔍 Cari publikasi berdasarkan judul..."
            class="search-input"
          />
          <span class="count-badge">Total: {{ filteredPublications.length }} publikasi</span>
        </div>

        <!-- Create/Edit Publication Modal -->
        <div v-if="showPublicationForm" class="modal-overlay" @click.self="closePublicationForm">
          <div class="modal-content large-modal">
            <div class="modal-header">
              <h2>{{ editingPublication ? '✏️ Edit Publikasi' : '➕ Tambah Publikasi Baru' }}</h2>
              <button @click="closePublicationForm" class="btn-close-modal">✕</button>
            </div>
            <form @submit.prevent="savePublication" class="publication-form">
              <div class="form-group">
                <label>Jenis Publikasi <span style="color: red;">*</span>:</label>
                <select v-model="publicationForm.type" @change="onTypeChange" required>
                  <option value="">Pilih Jenis</option>
                  <option value="jurnal">Jurnal</option>
                  <option value="konferensi">Konferensi</option>
                  <option value="hakki">HaKI</option>
                </select>
              </div>

              <div class="form-group">
                <label>Judul Publikasi <span style="color: red;">*</span>:</label>
                <input v-model="publicationForm.title" type="text" required />
              </div>

              <div class="form-group">
                <label>Penulis <span style="color: red;">*</span> (pisahkan dengan koma):</label>
                <input v-model="publicationAuthorsInput" type="text" placeholder="Nama Penulis 1, Nama Penulis 2" required />
              </div>

              <!-- Field untuk Jurnal -->
              <template v-if="publicationForm.type === 'jurnal'">
                <div class="form-group">
                  <label>Nama Jurnal:</label>
                  <input v-model="publicationForm.journal_name" type="text" />
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Volume:</label>
                    <input v-model="publicationForm.volume" type="text" />
                  </div>
                  <div class="form-group">
                    <label>Issue:</label>
                    <input v-model="publicationForm.issue" type="text" />
                  </div>
                </div>
                <div class="form-group">
                  <label>Halaman:</label>
                  <input v-model="publicationForm.pages" type="text" placeholder="1-10" />
                </div>
              </template>

              <!-- Field untuk Konferensi -->
              <template v-if="publicationForm.type === 'konferensi'">
                <div class="form-group">
                  <label>Nama Konferensi:</label>
                  <input v-model="publicationForm.conference_name" type="text" />
                </div>
                <div class="form-group">
                  <label>Lokasi Konferensi:</label>
                  <input v-model="publicationForm.publisher" type="text" placeholder="Kota, Negara" />
                </div>
              </template>

              <!-- Field untuk HaKI -->
              <template v-if="publicationForm.type === 'hakki'">
                <div class="form-group">
                  <label>Jenis HaKI:</label>
                  <select v-model="publicationForm.hakki_type">
                    <option value="">Pilih Jenis</option>
                    <option value="Paten">Paten</option>
                    <option value="Hak Cipta">Hak Cipta</option>
                    <option value="Merek">Merek</option>
                    <option value="Desain Industri">Desain Industri</option>
                    <option value="Rahasia Dagang">Rahasia Dagang</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Nomor Pendaftaran:</label>
                  <input v-model="publicationForm.doi" type="text" />
                </div>
              </template>

              <!-- Field Umum -->
              <div class="form-group">
                <label>Penerbit:</label>
                <input v-model="publicationForm.publisher" type="text" />
              </div>

              <div class="form-group">
                <label>Tahun:</label>
                <input v-model.number="publicationForm.year" type="number" :min="1900" :max="new Date().getFullYear() + 1" />
              </div>

              <div class="form-group">
                <label>DOI:</label>
                <input v-model="publicationForm.doi" type="text" placeholder="10.xxxx/xxxxx" />
              </div>

              <div class="form-group">
                <label>ISBN:</label>
                <input v-model="publicationForm.isbn" type="text" />
              </div>

              <div class="form-group">
                <label>Status:</label>
                <select v-model="publicationForm.status">
                  <option value="draft">Draft</option>
                  <option value="submitted">Submitted</option>
                  <option value="accepted">Accepted</option>
                  <option value="published">Published</option>
                </select>
              </div>

              <div class="form-group">
                <label>Deskripsi/Abstrak:</label>
                <textarea v-model="publicationForm.description" rows="4"></textarea>
              </div>

              <div class="form-group">
                <label>Link Publikasi:</label>
                <input v-model="publicationForm.link" type="url" placeholder="https://..." />
              </div>

              <div class="form-group">
                <label>Keywords (pisahkan dengan koma):</label>
                <input v-model="publicationTagsInput" type="text" placeholder="keyword1, keyword2" />
              </div>

              <div v-if="publicationError" class="error-message">{{ publicationError }}</div>
              <div class="modal-actions">
                <button type="submit" :disabled="loading" class="btn-save">💾 Simpan</button>
                <button type="button" @click="closePublicationForm" class="btn-cancel">❌ Batal</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Publications Table -->
        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Judul</th>
                <th>Jenis</th>
                <th>Penulis</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loadingPublications">
                <td colspan="6" class="loading-cell">Memuat data...</td>
              </tr>
              <tr v-else-if="filteredPublications.length === 0">
                <td colspan="6" class="empty-cell">Tidak ada publikasi</td>
              </tr>
              <tr v-else v-for="pub in filteredPublications" :key="pub.id">
                <td><strong>{{ pub.title }}</strong></td>
                <td><span class="type-badge" :class="pub.type">{{ getTypeLabel(pub.type) }}</span></td>
                <td>{{ formatAuthors(pub.authors) }}</td>
                <td>{{ pub.year || '-' }}</td>
                <td><span class="status-badge" :class="pub.status">{{ getStatusLabel(pub.status) }}</span></td>
                <td>
                  <div class="action-buttons">
                    <button @click="editPublication(pub)" class="btn-edit">✏️ Edit</button>
                    <button @click="deletePublication(pub.id)" class="btn-delete">🗑️ Hapus</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab Content: Students -->
      <div v-if="activeTab === 'students'" class="tab-content">
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Login state
const isLoggedIn = ref(false);
const loading = ref(false);
const loginError = ref('');
const loginForm = ref({ nim: '', password: '' });

// Tab state
const activeTab = ref('publications');

// Publications state
const publications = ref([]);
const loadingPublications = ref(false);
const publicationSearch = ref('');
const showPublicationForm = ref(false);
const editingPublication = ref(null);
const publicationError = ref('');
const publicationForm = ref({
  title: '', type: '', authors: [], journal_name: '', conference_name: '',
  hakki_type: '', publisher: '', year: null, volume: '', issue: '', pages: '',
  doi: '', isbn: '', status: 'draft', description: '', link: '', tags: [],
});
const publicationAuthorsInput = ref('');
const publicationTagsInput = ref('');

// Students state
const students = ref([]);
const loadingStudents = ref(false);
const studentSearch = ref('');
const showCreateStudentForm = ref(false);
const createStudentError = ref('');
const editingStudentId = ref(null);
const selectedStudents = ref([]);
const sortField = ref(null);
const sortDirection = ref('asc'); // 'asc' or 'desc'
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
const filteredPublications = computed(() => {
  if (!publicationSearch.value) return publications.value;
  const query = publicationSearch.value.toLowerCase();
  return publications.value.filter(pub => 
    pub.title.toLowerCase().includes(query)
  );
});

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

// Login functions
const login = async () => {
  loading.value = true;
  loginError.value = '';
  try {
    const response = await axios.post('/api/admin/login', loginForm.value);
    if (response.data.is_admin) {
      isLoggedIn.value = true;
      // Save login state to localStorage
      localStorage.setItem('adminLoggedIn', 'true');
      localStorage.setItem('adminNIM', loginForm.value.nim);
      await Promise.all([fetchPublications(), fetchStudents()]);
    }
  } catch (error) {
    loginError.value = error.response?.data?.message || 'Login gagal';
  } finally {
    loading.value = false;
  }
};

const logout = () => {
  isLoggedIn.value = false;
  publications.value = [];
  students.value = [];
  publicationSearch.value = '';
  studentSearch.value = '';
  editingStudentId.value = null;
  selectedStudents.value = [];
  sortField.value = null;
  sortDirection.value = 'asc';
  // Clear login state from localStorage
  localStorage.removeItem('adminLoggedIn');
  localStorage.removeItem('adminNIM');
};

// Publication functions
const fetchPublications = async () => {
  loadingPublications.value = true;
  try {
    const response = await axios.get('/api/posts');
    publications.value = response.data;
  } catch (error) {
    console.error('Error fetching publications:', error);
    alert('Gagal memuat publikasi');
  } finally {
    loadingPublications.value = false;
  }
};

const toggleCreatePublication = () => {
  editingPublication.value = null;
  resetPublicationForm();
  showPublicationForm.value = true;
};

const closePublicationForm = () => {
  showPublicationForm.value = false;
  editingPublication.value = null;
  resetPublicationForm();
};

const resetPublicationForm = () => {
  publicationForm.value = {
    title: '', type: '', authors: [], journal_name: '', conference_name: '',
    hakki_type: '', publisher: '', year: null, volume: '', issue: '', pages: '',
    doi: '', isbn: '', status: 'draft', description: '', link: '', tags: [],
  };
  publicationAuthorsInput.value = '';
  publicationTagsInput.value = '';
  publicationError.value = '';
};

const onTypeChange = () => {
  if (publicationForm.value.type !== 'jurnal') {
    publicationForm.value.journal_name = '';
    publicationForm.value.volume = '';
    publicationForm.value.issue = '';
  }
  if (publicationForm.value.type !== 'konferensi') {
    publicationForm.value.conference_name = '';
  }
  if (publicationForm.value.type !== 'hakki') {
    publicationForm.value.hakki_type = '';
  }
};

const savePublication = async () => {
  loading.value = true;
  publicationError.value = '';
  try {
    const data = {
      ...publicationForm.value,
      authors: publicationAuthorsInput.value ? publicationAuthorsInput.value.split(',').map(a => a.trim()) : [],
      tags: publicationTagsInput.value ? publicationTagsInput.value.split(',').map(t => t.trim()) : [],
    };
    
    if (editingPublication.value) {
      await axios.put(`/api/posts/${editingPublication.value.id}`, data);
      alert('Publikasi berhasil diupdate!');
    } else {
      await axios.post('/api/posts', data);
      alert('Publikasi berhasil ditambahkan!');
    }
    
    await fetchPublications();
    closePublicationForm();
  } catch (error) {
    publicationError.value = error.response?.data?.message || 'Gagal menyimpan publikasi';
  } finally {
    loading.value = false;
  }
};

const editPublication = (pub) => {
  editingPublication.value = pub;
  publicationForm.value = { ...pub };
  publicationAuthorsInput.value = pub.authors ? pub.authors.join(', ') : '';
  publicationTagsInput.value = pub.tags ? pub.tags.join(', ') : '';
  showPublicationForm.value = true;
};

const deletePublication = async (id) => {
  if (!confirm('Yakin ingin menghapus publikasi ini?')) return;
  loading.value = true;
  try {
    await axios.delete(`/api/posts/${id}`);
    await fetchPublications();
    alert('Publikasi berhasil dihapus!');
  } catch (error) {
    alert('Gagal menghapus publikasi: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

// Student functions
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
    // Unselect all filtered students
    filteredStudents.value.forEach(student => {
      const index = selectedStudents.value.indexOf(student.id);
      if (index > -1) {
        selectedStudents.value.splice(index, 1);
      }
    });
  } else {
    // Select all filtered students
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
    
    // Remove deleted students from the list
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
    // Toggle direction if same field
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    // New field, default to ascending
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

// Helper functions
const formatAuthors = (authors) => {
  if (!authors || !Array.isArray(authors)) return '-';
  return authors.join(', ');
};

const getTypeLabel = (type) => {
  const labels = { jurnal: '📄 Jurnal', konferensi: '🎤 Konferensi', hakki: '⚖️ HaKI' };
  return labels[type] || type;
};

const getStatusLabel = (status) => {
  const labels = { draft: 'Draft', submitted: 'Submitted', accepted: 'Accepted', published: 'Published' };
  return labels[status] || status;
};

const getGradeClass = (grade) => {
  const classes = { 'A': 'grade-a', 'B': 'grade-b', 'C': 'grade-c', 'D': 'grade-d', 'E': 'grade-e' };
  return classes[grade] || '';
};

// Check if user is already logged in on component mount
const checkLoginStatus = async () => {
  const savedLoginState = localStorage.getItem('adminLoggedIn');
  if (savedLoginState === 'true') {
    // User was logged in, restore session
    isLoggedIn.value = true;
    loading.value = true;
    try {
      // Fetch data to restore session
      await Promise.all([fetchPublications(), fetchStudents()]);
    } catch (error) {
      // If fetch fails, user might not be authenticated anymore
      console.error('Error restoring session:', error);
      localStorage.removeItem('adminLoggedIn');
      localStorage.removeItem('adminNIM');
      isLoggedIn.value = false;
    } finally {
      loading.value = false;
    }
  }
};

onMounted(() => {
  checkLoginStatus();
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

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 12px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
}

.form-group textarea {
  min-height: 100px;
  resize: vertical;
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
  max-width: 1600px;
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

/* Tabs Navigation */
.tabs-nav {
  display: flex;
  gap: 10px;
  margin-bottom: 30px;
  background: white;
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tab-button {
  flex: 1;
  padding: 15px 30px;
  border: 2px solid #ddd;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
  transition: all 0.3s;
}

.tab-button:hover {
  background: #f8f9fa;
  border-color: #667eea;
}

.tab-button.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

/* Tab Content */
.tab-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  margin: 0;
  color: #333;
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

/* Table Styles */
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

.type-badge {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.type-badge.jurnal {
  background: #e3f2fd;
  color: #1976d2;
}

.type-badge.konferensi {
  background: #f3e5f5;
  color: #7b1fa2;
}

.type-badge.hakki {
  background: #fff3e0;
  color: #e65100;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.draft {
  background: #f5f5f5;
  color: #666;
}

.status-badge.submitted {
  background: #fff3e0;
  color: #f57c00;
}

.status-badge.accepted {
  background: #e8f5e9;
  color: #388e3c;
}

.status-badge.published {
  background: #c8e6c9;
  color: #2e7d32;
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
.btn-cancel,
.btn-delete {
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

.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-delete:hover {
  background: #c82333;
}

/* Checkbox Styles */
.checkbox-select-all,
.checkbox-row {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #667eea;
}

/* Bulk Actions */
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

.edit-input {
  width: 70px;
  padding: 6px;
  border: 1px solid #ddd;
  border-radius: 4px;
  text-align: center;
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

.large-modal {
  max-width: 900px;
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

.publication-form,
.create-form {
  padding: 30px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.create-form .form-group,
.publication-form .form-group {
  margin-bottom: 20px;
}

.create-form .form-group label,
.publication-form .form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.create-form .form-group input,
.publication-form .form-group input,
.publication-form .form-group select,
.publication-form .form-group textarea {
  width: 100%;
  padding: 10px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}

.create-form .form-group input:focus,
.publication-form .form-group input:focus,
.publication-form .form-group select:focus,
.publication-form .form-group textarea:focus {
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
