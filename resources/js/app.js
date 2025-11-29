import './bootstrap';

import { createApp } from 'vue';
import App from './components/App.vue';
import PostList from './components/PostList.vue';
import PortalNilai from './components/PortalNilai.vue';
import AdminPortal from './components/AdminPortal.vue';

// Mount App component (default)
const appElement = document.getElementById('app');
if (appElement) {
    createApp(App).mount('#app');
}

// Mount PostList component (for MongoDB demo)
const postListElement = document.getElementById('post-list');
if (postListElement) {
    createApp(PostList).mount('#post-list');
}

// Mount Portal Nilai
const portalNilaiElement = document.getElementById('portal-nilai-root');
if (portalNilaiElement) {
    createApp(PortalNilai).mount('#portal-nilai-root');
}

// Mount Admin Portal
const adminRootElement = document.getElementById('admin-root');
if (adminRootElement) {
    createApp(AdminPortal).mount('#admin-root');
}
