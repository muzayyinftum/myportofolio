<template>
  <div class="tab-content">
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Publications state
const publications = ref([]);
const loadingPublications = ref(false);
const publicationSearch = ref('');
const showPublicationForm = ref(false);
const editingPublication = ref(null);
const publicationError = ref('');
const loading = ref(false);
const publicationForm = ref({
  title: '', type: '', authors: [], journal_name: '', conference_name: '',
  hakki_type: '', publisher: '', year: null, volume: '', issue: '', pages: '',
  doi: '', isbn: '', status: 'draft', description: '', link: '', tags: [],
});
const publicationAuthorsInput = ref('');
const publicationTagsInput = ref('');

// Computed
const filteredPublications = computed(() => {
  if (!publicationSearch.value) return publications.value;
  const query = publicationSearch.value.toLowerCase();
  return publications.value.filter(pub => 
    pub.title.toLowerCase().includes(query)
  );
});

// Functions
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

onMounted(() => {
  fetchPublications();
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

.data-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
}

.data-table tbody tr:hover {
  background: #f8f9fa;
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

.publication-form {
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

