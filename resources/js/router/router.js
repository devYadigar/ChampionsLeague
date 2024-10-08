import { createRouter, createWebHistory } from 'vue-router';
import MainComponent from '../components/MainComponent.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: MainComponent
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;