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
      <!-- Sidebar -->
      <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
        <div class="sidebar-header">
          <h2 v-if="!sidebarCollapsed">📊 Admin</h2>
          <button @click="toggleSidebar" class="btn-toggle-sidebar">
            {{ sidebarCollapsed ? '☰' : '✕' }}
          </button>
        </div>
        <nav class="sidebar-nav">
          <button 
            @click="activeTab = 'publications'" 
            :class="['nav-item', { active: activeTab === 'publications' }]"
          >
            <span class="nav-icon">📄</span>
            <span v-if="!sidebarCollapsed" class="nav-text">Kelola Publikasi</span>
          </button>
          <button 
            @click="activeTab = 'students'" 
            :class="['nav-item', { active: activeTab === 'students' }]"
          >
            <span class="nav-icon">👥</span>
            <span v-if="!sidebarCollapsed" class="nav-text">Kelola Nilai Mahasiswa</span>
          </button>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar">
          <div class="navbar-left">
            <h1 class="navbar-title">Dashboard Admin</h1>
          </div>
          <div class="navbar-right">
            <div class="user-info">
              <span class="user-name">Admin</span>
              <button @click="logout" class="btn-logout-nav">
                <span>🚪</span>
                <span>Keluar</span>
              </button>
            </div>
          </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
          <!-- Tab Content: Publications -->
          <ManagePublications v-if="activeTab === 'publications'" />
          
          <!-- Tab Content: Students -->
          <ManageStudents v-if="activeTab === 'students'" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ManagePublications from './ManagePublications.vue';
import ManageStudents from './ManageStudents.vue';

// Login state
const isLoggedIn = ref(false);
const loading = ref(false);
const loginError = ref('');
const loginForm = ref({ nim: '', password: '' });

// Tab state
const activeTab = ref('publications');
const sidebarCollapsed = ref(false);

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
    }
  } catch (error) {
    loginError.value = error.response?.data?.message || 'Login gagal';
  } finally {
    loading.value = false;
  }
};

const logout = () => {
  isLoggedIn.value = false;
  // Clear login state from localStorage
  localStorage.removeItem('adminLoggedIn');
  localStorage.removeItem('adminNIM');
};

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Check if user is already logged in on component mount
const checkLoginStatus = () => {
  const savedLoginState = localStorage.getItem('adminLoggedIn');
  if (savedLoginState === 'true') {
    isLoggedIn.value = true;
  }
};

onMounted(() => {
  checkLoginStatus();
});
</script>

<style scoped>
.admin-container {
  width: 100%;
  min-height: 100vh;
  background: #f5f5f5;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

/* Admin Dashboard Layout */
.admin-dashboard {
  display: flex;
  width: 100vw;
  min-height: 100vh;
  background: #f5f7fa;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  position: relative;
}

/* Sidebar Styles */
.sidebar {
  width: 260px;
  min-width: 260px;
  max-width: 260px;
  background: #2196F3;
  color: white;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  transition: width 0.3s ease, min-width 0.3s ease, max-width 0.3s ease;
  position: fixed;
  height: 100vh;
  z-index: 1000;
  overflow-y: auto;
  left: 0;
  top: 0;
  flex-shrink: 0;
}

.sidebar.collapsed {
  width: 70px;
  min-width: 70px;
  max-width: 70px;
}

.sidebar-header {
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}

.sidebar-header h2 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
}

.btn-toggle-sidebar {
  background: rgba(255,255,255,0.2);
  border: none;
  color: white;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 18px;
  transition: background 0.2s;
}

.btn-toggle-sidebar:hover {
  background: rgba(255,255,255,0.3);
}

.sidebar-nav {
  padding: 20px 0;
  flex: 1;
}

.nav-item {
  width: 100%;
  display: flex;
  align-items: center;
  padding: 15px 20px;
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
  font-size: 16px;
  font-weight: 500;
  gap: 12px;
}

.nav-item:hover {
  background: rgba(255,255,255,0.1);
}

.nav-item.active {
  background: rgba(255,255,255,0.2);
  border-left: 4px solid white;
  font-weight: 600;
}

.nav-icon {
  font-size: 20px;
  min-width: 24px;
  display: inline-block;
}

.nav-text {
  white-space: nowrap;
  overflow: hidden;
}

.sidebar.collapsed .nav-text {
  display: none;
}

.sidebar.collapsed .sidebar-header h2 {
  display: none;
}

/* Main Content Area */
.main-content {
  flex: 1;
  margin-left: 260px;
  display: flex;
  flex-direction: column;
  transition: margin-left 0.3s ease;
  min-height: 100vh;
  margin-top: 0;
  margin-right: 0;
  margin-bottom: 0;
  padding: 0;
  width: calc(100vw - 260px);
  box-sizing: border-box;
}

.sidebar.collapsed ~ .main-content {
  margin-left: 70px;
  width: calc(100vw - 70px);
}

/* Navbar Styles */
.navbar {
  background: white;
  padding: 0 60px 0 30px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 100;
  width: 100%;
  box-sizing: border-box;
  margin: 0;
  flex-shrink: 0;
}

.navbar-left {
  display: flex;
  align-items: center;
}

.navbar-title {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #333;
}

.navbar-right {
  display: flex;
  align-items: center;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.user-name {
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.btn-logout-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: background 0.2s;
}

.btn-logout-nav:hover {
  background: #c82333;
}

/* Content Wrapper */
.content-wrapper {
  flex: 1;
  padding: 30px 30px 30px 0px;
  overflow-y: auto;
  overflow-x: hidden;
  min-height: calc(100vh - 70px);
  width: 100%;
  box-sizing: border-box;
  margin: 0;
  background: #f5f7fa;
  max-width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
  .sidebar {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
  }
  
  .sidebar.collapsed {
    width: 0;
    min-width: 0;
    max-width: 0;
    overflow: hidden;
  }
  
  .main-content {
    margin-left: 70px;
    width: calc(100vw - 70px);
  }
  
  .sidebar.collapsed ~ .main-content {
    margin-left: 0;
    width: 100vw;
  }
  
  .navbar {
    padding: 0 15px;
  }
  
  .navbar-title {
    font-size: 18px;
  }
  
  .content-wrapper {
    padding: 15px;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .user-name {
    display: none;
  }
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

/* Tab Content */
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
