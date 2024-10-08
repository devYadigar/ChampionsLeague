/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import { createApp } from 'vue';
import App from './App.vue'; // Import the root component
import router from './router/router'; // Import the Vue Router instance

// Create the Vue application with the root component
const app = createApp(App);

// Use the router instance
app.use(router);

// Mount the Vue application
app.mount('#app');

