<template>
  <div class="post-container">    
    <!-- Form untuk membuat publikasi baru -->
    <div v-if="isFormVisible" class="post-form">
      <h3>Tambah Publikasi Baru</h3>
      <form @submit.prevent="createPost">
        <div>
          <label>Jenis Publikasi <span style="color: red;">*</span>:</label>
          <select v-model="newPost.type" @change="onTypeChange" required>
            <option value="">Pilih Jenis</option>
            <option value="jurnal">Jurnal</option>
            <option value="konferensi">Konferensi</option>
            <option value="hakki">HaKI</option>
          </select>
        </div>

        <div>
          <label>Judul Publikasi <span style="color: red;">*</span>:</label>
          <input v-model="newPost.title" type="text" required />
        </div>

        <div>
          <label>Penulis <span style="color: red;">*</span> (pisahkan dengan koma):</label>
          <input v-model="authorsInput" type="text" placeholder="Nama Penulis 1, Nama Penulis 2" required />
          <small style="color: #666;">Contoh: John Doe, Jane Smith</small>
        </div>

        <!-- Field untuk Jurnal -->
        <template v-if="newPost.type === 'jurnal'">
          <div>
            <label>Nama Jurnal:</label>
            <input v-model="newPost.journal_name" type="text" />
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div>
              <label>Volume:</label>
              <input v-model="newPost.volume" type="text" />
            </div>
            <div>
              <label>Issue:</label>
              <input v-model="newPost.issue" type="text" />
            </div>
          </div>
          <div>
            <label>Halaman:</label>
            <input v-model="newPost.pages" type="text" placeholder="1-10" />
          </div>
        </template>

        <!-- Field untuk Konferensi -->
        <template v-if="newPost.type === 'konferensi'">
          <div>
            <label>Nama Konferensi:</label>
            <input v-model="newPost.conference_name" type="text" />
          </div>
          <div>
            <label>Lokasi Konferensi:</label>
            <input v-model="newPost.publisher" type="text" placeholder="Kota, Negara" />
          </div>
        </template>

        <!-- Field untuk HaKI -->
        <template v-if="newPost.type === 'hakki'">
          <div>
            <label>Jenis HaKI:</label>
            <select v-model="newPost.hakki_type">
              <option value="">Pilih Jenis</option>
              <option value="Paten">Paten</option>
              <option value="Hak Cipta">Hak Cipta</option>
              <option value="Merek">Merek</option>
              <option value="Desain Industri">Desain Industri</option>
              <option value="Rahasia Dagang">Rahasia Dagang</option>
            </select>
          </div>
          <div>
            <label>Nomor Pendaftaran:</label>
            <input v-model="newPost.doi" type="text" />
          </div>
        </template>

        <!-- Field Umum -->
        <div>
          <label>Penerbit:</label>
          <input v-model="newPost.publisher" type="text" />
        </div>

        <div>
          <label>Tahun:</label>
          <input v-model="newPost.year" type="number" :min="1900" :max="new Date().getFullYear() + 1" />
        </div>

        <div>
          <label>DOI:</label>
          <input v-model="newPost.doi" type="text" placeholder="10.xxxx/xxxxx" />
        </div>

        <div>
          <label>ISBN:</label>
          <input v-model="newPost.isbn" type="text" />
        </div>

        <div>
          <label>Status:</label>
          <select v-model="newPost.status">
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="accepted">Accepted</option>
            <option value="published">Published</option>
          </select>
        </div>

        <div>
          <label>Deskripsi/Abstrak:</label>
          <textarea v-model="newPost.description" rows="4"></textarea>
        </div>

        <div>
          <label>Link Publikasi:</label>
          <input v-model="newPost.link" type="url" placeholder="https://..." />
        </div>

        <div>
          <label>Keywords (pisahkan dengan koma):</label>
          <input v-model="tagsInput" type="text" placeholder="keyword1, keyword2, keyword3" />
        </div>

        <button type="submit" :disabled="loading">Tambah Publikasi</button>
      </form>
    </div>

    <!-- Daftar publikasi -->
    <div class="posts-list">
      <div class="posts-list-header">
        <h3>Daftar Publikasi</h3>
        <button class="submit-button" @click="toggleForm" v-if="!isFormVisible">Tambah Publikasi Baru</button>
        <button class="submit-button" @click="toggleForm" v-if="isFormVisible">Tutup Form</button>
      </div>
      
      <div v-if="loading" class="loading">Memuat...</div>
      <div v-else-if="posts.length === 0" class="empty">Belum ada publikasi</div>
      <div v-else class="posts">
        <div v-for="post in posts" :key="post.id" class="post-item">
          <div class="post-header">
            <span class="type-badge" :class="post.type">{{ getTypeLabel(post.type) }}</span>
            <span class="status-badge" :class="post.status">{{ getStatusLabel(post.status) }}</span>
          </div>
          
          <h4>{{ post.title }}</h4>
          
          <div class="post-meta">
            <p><strong>Penulis:</strong> {{ formatAuthors(post.authors) }}</p>
            
            <template v-if="post.type === 'jurnal'">
              <p v-if="post.journal_name"><strong>Jurnal:</strong> {{ post.journal_name }}</p>
              <p v-if="post.volume || post.issue">
                <strong>Volume/Issue:</strong> 
                <span v-if="post.volume">Vol. {{ post.volume }}</span>
                <span v-if="post.volume && post.issue">, </span>
                <span v-if="post.issue">No. {{ post.issue }}</span>
              </p>
              <p v-if="post.pages"><strong>Halaman:</strong> {{ post.pages }}</p>
            </template>
            
            <template v-if="post.type === 'konferensi'">
              <p v-if="post.conference_name"><strong>Konferensi:</strong> {{ post.conference_name }}</p>
              <p v-if="post.publisher"><strong>Lokasi:</strong> {{ post.publisher }}</p>
            </template>
            
            <template v-if="post.type === 'hakki'">
              <p v-if="post.hakki_type"><strong>Jenis HaKI:</strong> {{ post.hakki_type }}</p>
              <p v-if="post.doi"><strong>Nomor Pendaftaran:</strong> {{ post.doi }}</p>
            </template>
            
            <p v-if="post.publisher && post.type !== 'konferensi'"><strong>Penerbit:</strong> {{ post.publisher }}</p>
            <p v-if="post.year"><strong>Tahun:</strong> {{ post.year }}</p>
            <p v-if="post.doi && post.type !== 'hakki'"><strong>DOI:</strong> {{ post.doi }}</p>
            <p v-if="post.isbn"><strong>ISBN:</strong> {{ post.isbn }}</p>
          </div>
          
          <div v-if="post.description" class="post-description">
            <p><strong>Abstrak:</strong> {{ post.description }}</p>
          </div>
          
          <div v-if="post.link" class="post-link">
            <a :href="post.link" target="_blank" rel="noopener noreferrer">🔗 Lihat Publikasi</a>
          </div>
          
          <div v-if="post.tags && post.tags.length > 0" class="tags">
            <span v-for="tag in post.tags" :key="tag" class="tag">{{ tag }}</span>
          </div>
          
          <button @click="deletePost(post.id)" class="delete-btn">Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const posts = ref([]);
const loading = ref(false);
const tagsInput = ref('');
const authorsInput = ref('');
const isFormVisible = ref(false);

const newPost = ref({
  title: '',
  type: '',
  authors: [],
  journal_name: '',
  conference_name: '',
  hakki_type: '',
  publisher: '',
  year: null,
  volume: '',
  issue: '',
  pages: '',
  doi: '',
  isbn: '',
  status: 'draft',
  description: '',
  link: '',
});

const onTypeChange = () => {
  // Reset field khusus saat ganti jenis
  if (newPost.value.type !== 'jurnal') {
    newPost.value.journal_name = '';
    newPost.value.volume = '';
    newPost.value.issue = '';
  }
  if (newPost.value.type !== 'konferensi') {
    newPost.value.conference_name = '';
  }
  if (newPost.value.type !== 'hakki') {
    newPost.value.hakki_type = '';
  }
};

const formatAuthors = (authors) => {
  if (!authors || !Array.isArray(authors)) return '-';
  return authors.join(', ');
};

const getTypeLabel = (type) => {
  const labels = {
    jurnal: '📄 Jurnal',
    konferensi: '🎤 Konferensi',
    hakki: '⚖️ HaKI'
  };
  return labels[type] || type;
};

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Draft',
    submitted: 'Submitted',
    accepted: 'Accepted',
    published: 'Published'
  };
  return labels[status] || status;
};

const fetchPosts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/posts');
    posts.value = response.data;
  } catch (error) {
    console.error('Error fetching posts:', error);
    alert('Gagal memuat publikasi');
  } finally {
    loading.value = false;
  }
};

const toggleForm = () => {
  isFormVisible.value = !isFormVisible.value;
  if (!isFormVisible.value) {
    // Reset form saat tutup
    resetForm();
  }
};

const resetForm = () => {
  newPost.value = {
    title: '',
    type: '',
    authors: [],
    journal_name: '',
    conference_name: '',
    hakki_type: '',
    publisher: '',
    year: null,
    volume: '',
    issue: '',
    pages: '',
    doi: '',
    isbn: '',
    status: 'draft',
    description: '',
    link: '',
  };
  authorsInput.value = '';
  tagsInput.value = '';
};

const createPost = async () => {
  loading.value = true;
  try {
    const postData = {
      ...newPost.value,
      authors: authorsInput.value ? authorsInput.value.split(',').map(author => author.trim()) : [],
      tags: tagsInput.value ? tagsInput.value.split(',').map(tag => tag.trim()) : [],
    };
    
    await axios.post('/api/posts', postData);
    
    resetForm();
    await fetchPosts();
    alert('Publikasi berhasil ditambahkan!');
    isFormVisible.value = false;
  } catch (error) {
    console.error('Error creating post:', error);
    const errorMsg = error.response?.data?.message || error.message || 'Gagal menambahkan publikasi';
    alert('Gagal menambahkan publikasi: ' + errorMsg);
  } finally {
    loading.value = false;
  }
};

const deletePost = async (id) => {
  if (!id) {
    console.error('Post ID tidak ditemukan');
    alert('Error: Post ID tidak ditemukan');
    return;
  }
  
  if (!confirm('Yakin ingin menghapus publikasi ini?')) return;
  
  loading.value = true;
  try {
    await axios.delete(`/api/posts/${id}`);
    await fetchPosts();
    alert('Publikasi berhasil dihapus!');
  } catch (error) {
    console.error('Error deleting post:', error);
    alert('Gagal menghapus publikasi: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPosts();
});
</script>

<style scoped>
.post-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
}

.post-form {
  background: #f5f5f5;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 30px;
}

.post-form div {
  margin-bottom: 15px;
}

.post-form label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
  color: #333;
}

.post-form input,
.post-form textarea,
.post-form select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}

.post-form textarea {
  min-height: 100px;
  resize: vertical;
}

.post-form button {
  background: #007bff;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
  margin-top: 10px;
}

.post-form button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.posts-list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.posts-list-header h3 {
  margin: 0;
  color: #333;
}

.loading, .empty {
  text-align: center;
  padding: 40px;
  color: #666;
}

.posts {
  display: grid;
  gap: 20px;
}

.post-item {
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.post-header {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
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

.post-item h4 {
  margin: 0 0 15px 0;
  color: #333;
  font-size: 20px;
}

.post-meta {
  margin: 15px 0;
  color: #666;
  line-height: 1.8;
}

.post-meta p {
  margin: 5px 0;
}

.post-description {
  margin: 15px 0;
  padding: 15px;
  background: #f9f9f9;
  border-left: 4px solid #007bff;
  border-radius: 4px;
}

.post-description p {
  margin: 0;
  color: #555;
  line-height: 1.6;
}

.post-link {
  margin: 15px 0;
}

.post-link a {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
}

.post-link a:hover {
  text-decoration: underline;
}

.tags {
  margin: 15px 0;
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.tag {
  display: inline-block;
  background: #e3f2fd;
  color: #1976d2;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 12px;
}

.delete-btn {
  background: #dc3545;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  margin-top: 15px;
}

.delete-btn:hover {
  background: #c82333;
}

.submit-button {
  background: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
}

.submit-button:hover {
  background: #0056b3;
}
</style>
