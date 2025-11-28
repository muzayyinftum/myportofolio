import './bootstrap';

import { createApp } from 'vue';
import App from './components/App.vue';
import PostList from './components/PostList.vue';

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
