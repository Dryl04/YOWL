<template>
  <!-- Navigation mobile en bas -->
  <div
    class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-2 sm:px-4 py-2 z-30 safe-area-bottom"
  >
    <div v-if="userStore.isAdmin" class="flex justify-around items-center max-w-md mx-auto">
      <!-- Navigation Admin -->
      <button
        @click="$router.push('/')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          ></path>
        </svg>
        <span class="text-xs font-medium">Accueil</span>
      </button>
      <button
        @click="$router.push('/friends')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/friends' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          ></path>
        </svg>
        <span class="text-xs">Amis</span>
      </button>
      <button
        @click="$router.push('/profile')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/profile' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          ></path>
        </svg>
        <span class="text-xs">Profil</span>
      </button>
      <button
        @click="$router.push('/admin/users')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/admin/users' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          ></path>
        </svg>
        <span class="text-xs">Users</span>
      </button>
      <button
        @click="$router.push('/admin/stats')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/admin/stats' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
          <path
            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"
          ></path>
        </svg>
        <span class="text-xs">Stats</span>
      </button>
    </div>
    <div v-else class="flex justify-around items-center max-w-md mx-auto">
      <!-- Navigation Utilisateur -->
      <button
        @click="$router.push('/')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          ></path>
        </svg>
        <span class="text-xs font-medium">Accueil</span>
      </button>
      <button
        @click="$router.push('/friends')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/friends' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          ></path>
        </svg>
        <span class="text-xs">Amis</span>
      </button>
      <button
        @click="$router.push('/profile')"
        :class="[
          'flex flex-col items-center gap-1 p-2 transition-colors',
          $route.path === '/profile' ? 'text-purple-600' : 'text-gray-500',
        ]"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          ></path>
        </svg>
        <span class="text-xs">Profil</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from "@/stores/UserStore";

const userStore = useUserStore();
</script>
