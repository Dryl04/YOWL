
import { createRouter, createWebHistory } from "vue-router";
import Home from "@/views/HomeView.vue";
import Friends from "@/views/FriendsView.vue";
import Profile from "@/views/ProfileView.vue"
import AdminUsers from "@/views/AdminUsers.vue"
import AdminStats from "@/views/AdminStats.vue"
import SignUp from '@/views/SignUp.vue'
import SignIn from '@/views/SignIn.vue'
import ResetPassword from '@/views/ResetPassword.vue'
import { useUserStore } from '@/stores/UserStore'
import ResetPasswordConfirm from "@/views/ResetPasswordConfirm.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "Home",
      component: Home,
      meta: { requiresAuth: true }
    },

    {
      path: "/friends",
      name: "Friends",
      component: Friends,
      meta: { requiresAuth: true }
    },

    {
      path: "/profile",
      name: "Profile",
      component: Profile,
      meta: { requiresAuth: true }
    },

    {
      path:'/Signup',
      name:'SignUp',
      component: SignUp,
      meta: { requiresGuest: true }
    },

    {
      path: '/Signin',
      name: 'SignIn',
      component: SignIn,
      meta: { requiresGuest: true }
    },

    {
      path: '/Resetpassword',
      name: 'ResetPassword',
      component: ResetPassword,
      meta: { requiresGuest: true }
    },

    {
      path: '/admin/users',
      name: 'AdminUsers',
      component: AdminUsers,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/stats',
      name: 'AdminStats',
      component: AdminStats,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    { path: '/password/reset', name: 'reset-password', component: ResetPasswordConfirm ,meta: { requiresGuest: true }},
  ],
});

function isAuthenticated() {
  return localStorage.getItem("authToken") !== null || sessionStorage.getItem("authToken") !== null;
}

function isAdmin() {
  const userStore = useUserStore();
  return userStore.currentUser?.role === 'admin';
}

router.beforeEach((to, _from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next("/SignIn");
  } else if (to.meta.requiresAdmin && !isAdmin()) {
    next("/");
  } else if (to.name === "SignIn" && isAuthenticated()) {
    next("/");
  } else {
    next();
  }
});

export default router;
