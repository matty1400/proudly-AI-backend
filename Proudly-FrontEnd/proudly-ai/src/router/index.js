import { createRouter, createWebHashHistory } from 'vue-router'
import Login from '../components/login-page.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/Dashboard',
    name: 'Dashboard-page',
    component: () => import('../components/Dashboard.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/Search',
    name: 'Search-page',
    component: () => import('../components/search-page.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/Result',
    name: 'Result-page',
    component: () => import('../components/result-page.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/Signup',
    name: 'Signup-page',
    component: () => import('../components/signup-page.vue'),
    meta: {
      requiresAuth: false
    }
  }

]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router

router.beforeEach((to,from,next) => {
  const isLoggedIn = sessionStorage.getItem('LoggedInStatus')
  if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn){
    next('/');
  } else {
    next();
  }
});
