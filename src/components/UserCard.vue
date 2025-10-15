<template>
  <article class="bg-white rounded-2xl p-3 sm:p-4 md:p-6 transition">
    <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
      <!-- Avatar et infos -->
      <div class="flex items-center gap-3 sm:gap-4 flex-1 w-full min-w-0">
        <div class="relative flex-shrink-0">
          <img
            :src="
              user.photo ||
              'https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg'
            "
            alt="Avatar"
            class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full object-cover"
          />
        </div>

        <div class="flex-1 min-w-0">
          <h3 class="font-semibold text-gray-900 text-sm sm:text-base md:text-lg truncate">
            {{ user.name }}
          </h3>
          <p class="text-xs sm:text-sm text-gray-500 truncate">{{ user.work || "Membre" }}</p>
        </div>
      </div>

      <!-- Boutons d'action -->
      <div class="flex flex-wrap sm:flex-nowrap gap-2 w-full sm:w-auto">
        <template v-if="type === 'friend'">
          <button
            @click="viewProfile"
            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition font-medium text-xs sm:text-sm hover:cursor-pointer whitespace-nowrap"
          >
            Voir le profil
          </button>
          <button
            @click="removeFriend"
            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium text-xs sm:text-sm hover:cursor-pointer"
          >
            Retirer
          </button>
        </template>

        <template v-if="type === 'request'">
          <button
            @click="acceptRequest"
            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:shadow-lg transition font-medium text-xs sm:text-sm hover:cursor-pointer"
          >
            Accepter
          </button>
          <button
            @click="rejectRequest"
            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium text-xs sm:text-sm hover:cursor-pointer"
          >
            Refuser
          </button>
        </template>

        <template v-if="type === 'user'">
          <button
            @click="sendRequest"
            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:shadow-lg transition font-medium text-xs sm:text-sm hover:cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed min-w-0"
            :disabled="userStore.sentRequests.some((req) => req._id === user._id) || loading"
          >
            <span v-if="loading" class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24">
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
            </span>
            <span
              v-else-if="userStore.sentRequests.some((req) => req._id === user._id)"
              class="truncate"
            >
              Envoyée
            </span>
            <span v-else>Inviter</span>
          </button>
          <button
            @click="viewProfile"
            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition font-medium text-xs sm:text-sm hover:cursor-pointer whitespace-nowrap"
          >
            <span class="hidden sm:inline">Voir le profil</span>
            <span class="sm:hidden">Profil</span>
          </button>
        </template>
      </div>
    </div>
  </article>
</template>

<script setup>
import { useUserStore } from "@/stores/UserStore";
import { ref, onMounted } from "vue";
const loading = ref(false);
const userStore = useUserStore();

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  type: {
    type: String,
    required: true,
    validator: (value) => ["friend", "request", "user"].includes(value),
  },
  requestId: {
    type: String,
    default: null,
  },
});

function viewProfile() {
  if (window.viewFriendProfile) {
    window.viewFriendProfile(props.user);
  }
}

function removeFriend() {
  if (confirm(`Êtes-vous sûr de vouloir retirer ${props.friend.name} de vos amis ?`)) {
    userStore.removeFriend(props.friend._id);
  }
}

function acceptRequest() {
  userStore.acceptFriendRequest(props.requestId);
}

function rejectRequest() {
  userStore.rejectFriendRequest(props.requestId);
}
async function sendRequest() {
  try {
    loading.value = true;
    await userStore.sendFriendRequest(props.user._id);
    await userStore.fetchSentRequests();
  } catch (error) {
    console.error("Error sending friend request:", error);
  } finally {
    loading.value = false;
  }
}
onMounted(async () => {
  await userStore.fetchUsers();
  await userStore.fetchSentRequests();
});
</script>
