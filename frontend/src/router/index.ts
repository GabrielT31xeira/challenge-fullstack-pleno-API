import { createRouter, createWebHistory } from 'vue-router'
import Login from "@/pages/Login.vue";
import Register from "@/pages/Register.vue";
import Dashboard from "@/pages/Dashboard.vue";
import Cart from "@/pages/auth/Cart.vue";
import {useAuthStore} from "@/stores/auth.ts";
import Order from "@/pages/auth/Order.vue";
import AdminDashboard from "@/pages/admin/AdminDashboard.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
      {
          path: '/',
          component: Dashboard,
          name: 'Dashboard',
      },
      {
          path: '/login',
          component: Login,
          name: 'Login',
      },
      {
          path: '/register',
          component: Register,
          name: 'Register',
      },
      // --- AUTH ---
      {
          path:'/auth/carts',
          component: Cart,
          name: 'Cart',
          meta: {requiresAuth: true},
      },
      {
          path:'/auth/orders',
          component: Order,
          name: 'Order',
          meta: {requiresAuth: true},
      },
      // --- ADMIN ---
      {
          path:'/admin/dashboard',
          component: AdminDashboard,
          name: 'AdminDashboard',
          meta: {
              requiresAuth: true,
              requiresAdmin: true,
          },
      },
  ],
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return next("/login");
    }

    next();
});

export default router
