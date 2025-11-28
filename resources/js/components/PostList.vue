<template>
  <div class="post-container">    
    <!-- Form untuk membuat post baru -->
    <div v-if="isFormVisible" class="post-form">
      <h3>Tambah Post Baru</h3>
      <form @submit.prevent="createPost">
        <div>
          <label>Judul:</label>
          <input v-model="newPost.title" type="text" required />
        </div>
        <div>
          <label>Konten:</label>
          <textarea v-model="newPost.content" required></textarea>
        </div>
        <div>
          <label>Author:</label>
          <input v-model="newPost.author" type="text" required />
        </div>
        <div>
          <label>Tags (pisahkan dengan koma):</label>
          <input v-model="tagsInput" type="text" placeholder="laravel, vue, mongodb" />
        </div>
        <button type="submit" :disabled="loading">Tambah Post</button>
      </form>
    </div>

    <!-- Daftar posts -->
    <div class="posts-list">
        <div class="posts-list-header" style="margin-bottom: 20px;">
            <h3>Daftar Posts</h3>
            <button class="submit-button" @click="toggleForm" v-if="!isFormVisible">Tambah Post Baru</button>
            <button class="submit-button" @click="toggleForm" v-if="isFormVisible">Tutup Form</button>
        </div>
        
      <div v-if="loading" class="loading">Memuat...</div>
      <div v-else-if="posts.length === 0" class="empty">Belum ada posts</div>
      <div v-else class="posts">
        <div v-for="post in posts" :key="post.id" class="post-item">
          <h4>{{ post.title }}</h4>
          <p>{{ post.content }}</p>
          <small>Oleh: {{ post.author }}</small>
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
const newPost = ref({
  title: '',
  content: '',
  author: '',
});
const isFormVisible = ref(false);
const fetchPosts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/posts');
    posts.value = response.data;
  } catch (error) {
    console.error('Error fetching posts:', error);
    alert('Gagal memuat posts');
  } finally {
    loading.value = false;
  }
};

const toggleForm = () => {
  isFormVisible.value = !isFormVisible.value;
};

const createPost = async () => {
  loading.value = true;
  try {
    const postData = {
      ...newPost.value,
      tags: tagsInput.value ? tagsInput.value.split(',').map(tag => tag.trim()) : [],
    };
    
    await axios.post('/api/posts', postData);
    
    // Reset form
    newPost.value = { title: '', content: '', author: '' };
    tagsInput.value = '';
    
    // Refresh posts
    await fetchPosts();
    alert('Post berhasil ditambahkan!');
  } catch (error) {
    console.error('Error creating post:', error);
    alert('Gagal menambahkan post');
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
  
  if (!confirm('Yakin ingin menghapus post ini?')) return;
  
  loading.value = true;
  try {
    await axios.delete(`/api/posts/${id}`);
    await fetchPosts();
    alert('Post berhasil dihapus!');
  } catch (error) {
    console.error('Error deleting post:', error);
    console.error('Post ID:', id);
    alert('Gagal menghapus post: ' + (error.response?.data?.message || error.message));
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
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.post-form {
  background: #f5f5f5;
  padding: 20px;
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
}

.post-form input,
.post-form textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.post-form textarea {
  min-height: 100px;
  resize: vertical;
}

.post-form button {
  background: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.post-form button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.posts-list h3 {
  margin-bottom: 20px;
}

.loading, .empty {
  text-align: center;
  padding: 20px;
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
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.post-item h4 {
  margin: 0 0 10px 0;
  color: #333;
}

.post-item p {
  color: #666;
  margin-bottom: 10px;
}

.post-item small {
  color: #999;
  display: block;
  margin-bottom: 10px;
}

.tags {
  margin: 10px 0;
}

.tag {
  display: inline-block;
  background: #e3f2fd;
  color: #1976d2;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 5px;
}

.delete-btn {
  background: #dc3545;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  margin-top: 10px;
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
}
</style>

