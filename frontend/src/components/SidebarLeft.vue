<template>
  <aside class="h-screen bg-white flex flex-col">
    <div class="p-4.5 border-b border-gray-200">
      <div class="flex items-center justify-center gap-4">
        <img src="/src/assets/Logo YOWL.png" alt="YOWL" class="h-10 w-auto" />
      </div>
    </div>

    <nav class="flex-1 p-4 space-y-2">
      <button
        @click="navigateTo('/')"
        :class="[
          'w-full flex items-center justify-between px-4 py-3 rounded-lg transition hover:cursor-pointer',
          isActive('/') ? 'bg-gray-100' : 'hover:bg-gray-100',
        ]"
      >
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              class="text-black"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
            ></path>
          </svg>
          <span class="text-black font-medium">Fil d'actualité</span>
        </div>
        <span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2 py-1 rounded-full">
          {{ postStore.postsCount }}
        </span>
      </button>

      <button
        @click="navigateTo('/friends')"
        :class="[
          'w-full flex items-center justify-between px-4 py-3 rounded-lg transition hover:cursor-pointer',
          isActive('/friends') ? 'bg-gray-100' : 'hover:bg-gray-100',
        ]"
      >
        <div class="flex items-center gap-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6 text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
            />
          </svg>
          <span class="text-black font-medium">Amis</span>
        </div>
        <div class="flex items-center gap-2">
          <span
            v-if="userStore.pendingRequestsCount > 0"
            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
          >
            {{ userStore.pendingRequestsCount }}
          </span>
          <span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2 py-1 rounded-full">
            {{ userStore.friendsCount }}
          </span>
        </div>
      </button>

      <button
        @click="navigateTo('/profile')"
        :class="[
          'w-full flex items-center gap-3 px-4 py-3 rounded-lg transition hover:cursor-pointer',
          isActive('/profile') ? 'bg-gray-100' : 'hover:bg-gray-100',
        ]"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-5 h-5 text-black"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
          />
        </svg>

        <span class="text-black font-medium">Profil</span>
      </button>

      <button
        v-if="userStore.currentUser?.role === 'admin'"
        @click="navigateTo('/admin/users')"
        :class="[
          'w-full flex items-center gap-3 px-4 py-3 rounded-lg transition hover:cursor-pointer',
          isActive('/admin/users') ? 'bg-gray-100' : 'hover:bg-gray-100',
        ]"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"
          />
        </svg>

        <span class="font-medium">Utilisateurs</span>
      </button>

      <button
        v-if="userStore.currentUser?.role === 'admin'"
        @click="navigateTo('/admin/stats')"
        :class="[
          'w-full flex items-center gap-3 px-4 py-3 rounded-lg transition hover:cursor-pointer',
          isActive('/admin/stats') ? 'bg-gray-100' : 'hover:bg-gray-100',
        ]"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"
          />
        </svg>

        <span class="font-medium">Statistiques</span>
      </button>
    </nav>

    <!-- Profil utilisateur en bas -->
    <div class="p-4 border-t border-gray-200">
      <div class="flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-400 to-red-400 flex items-center justify-center"
        >
          <div v-if="userStore.currentUserAvatar">
            <img
              :src="userStore.currentUserAvatar"
              alt="Avatar"
              class="w-10 h-10 rounded-full object-cover"
            />
          </div>
          <div v-else>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              ></path>
            </svg>
          </div>
        </div>
        <div class="flex-1">
          <p class="text-sm font-semibold text-gray-800">
            {{ userStore.currentUserName }}
          </p>
          <p class="text-xs text-gray-500">
            {{ userStore.currentUser?.work || "Membre" }}
          </p>
        </div>
        <button
          @click="logout"
          class="hover:text-red-500 transition hover:cursor-pointer"
          title="Déconnexion"
        >
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
            ></path>
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useUserStore } from "@/stores/UserStore";
import { usePostStore } from "@/stores/PostsStore";
import { useRouter, useRoute } from "vue-router";
import { defineEmits, onMounted } from "vue";

const emit = defineEmits(["logout"]);

const userStore = useUserStore();
const postStore = usePostStore();
const router = useRouter();
const route = useRoute();

function navigateTo(path) {
  router.push(path);
}

function isActive(path) {
  return route.path === path;
}

// S'assurer que l'utilisateur est chargé au montage du composant
onMounted(async () => {
  if (
    !userStore.currentUser &&
    (localStorage.getItem("authToken") || sessionStorage.getItem("authToken"))
  ) {
    await userStore.fetchCurrentUser();
  }
});

function logout() {
  userStore.logout();
  emit("logout");
}

onMounted(async () => {
  if (userStore.isAuthenticated) {
    await postStore.fetchPosts();
    await userStore.fetchCurrentUser();
  }
});
</script>
