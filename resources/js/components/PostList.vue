<template>
  <div class="post-container">    
    <!-- Daftar publikasi (Read-Only) -->
    <div class="posts-list">
      <div class="posts-list-header">
        <h2>📄 Daftar Publikasi</h2>
        <p style="color: #666; margin: 0;">Halaman ini hanya untuk melihat publikasi. Untuk mengelola publikasi, silakan akses halaman admin.</p>
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

.posts-list-header {
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #eee;
}

.posts-list-header h2 {
  margin: 0 0 10px 0;
  color: #333;
  font-size: 28px;
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

</style>
