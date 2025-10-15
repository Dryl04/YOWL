<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
      <div
        class="hidden lg:block w-64 bg-white border-r border-gray-200 h-screen fixed left-0 top-0 z-20"
      >
        <SideBarLeft />
      </div>

      <div class="fixed top-0 left-0 lg:left-64 right-0 z-40">
        <Header />
      </div>

      <main class="flex-1 overflow-y-auto pt-16 sm:pt-20 pb-20 lg:pb-0 lg:ml-64">
        <div class="max-w-6xl mx-auto py-2 sm:py-4 px-2 sm:px-4">
          <div class="p-3 sm:p-4 md:p-6 mb-4 sm:mb-6">
            <div
              class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2"
            >
              <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Vos amis</h1>
              <span class="text-xs sm:text-sm text-gray-500">
                {{ userStore.friendsCount }} ami{{ userStore.friendsCount > 1 ? "s" : "" }}
              </span>
            </div>

            <div class="relative mb-4 sm:mb-6">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher un ami..."
                class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:bg-white focus:border-none text-xs sm:text-sm"
              />
              <svg
                class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                ></path>
              </svg>
            </div>

            <div class="flex gap-2 sm:gap-4 border-b border-gray-200 overflow-x-auto">
              <button
                @click="activeTab = 'allUsers'"
                :class="[
                  'px-3 sm:px-4 py-2 font-medium transition border-b-2 hover:cursor-pointer whitespace-nowrap text-xs sm:text-sm',
                  activeTab === 'allUsers'
                    ? 'text-orange-400 border-orange-400'
                    : 'text-gray-500 border-transparent hover:text-gray-700',
                ]"
              >
                Autres utilisateurs
              </button>
              <button
                @click="activeTab = 'all'"
                :class="[
                  'px-3 sm:px-4 py-2 font-medium transition border-b-2 hover:cursor-pointer whitespace-nowrap text-xs sm:text-sm',
                  activeTab === 'all'
                    ? 'text-orange-400 border-orange-400'
                    : 'text-gray-500 border-transparent hover:text-gray-700',
                ]"
              >
                Tous les amis
              </button>
              <button
                @click="activeTab = 'requests'"
                :class="[
                  'px-3 sm:px-4 py-2 font-medium transition border-b-2 flex items-center gap-2 hover:cursor-pointer whitespace-nowrap text-xs sm:text-sm',
                  activeTab === 'requests'
                    ? 'text-orange-400 border-orange-400'
                    : 'text-gray-500 border-transparent hover:text-gray-700',
                ]"
              >
                Demandes
                <span
                  v-if="userStore.pendingRequestsCount > 0"
                  class="bg-red-500 text-white text-xs font-bold px-1.5 sm:px-2 py-0.5 rounded-full"
                >
                  {{ userStore.pendingRequestsCount }}
                </span>
              </button>
            </div>
          </div>

          <div class="space-y-3 sm:space-y-4 px-2 sm:px-0">
            <template v-if="activeTab === 'allUsers'">
              <div
                v-if="userStore.searchUsers(searchQuery).length === 0"
                class="text-center py-12 text-gray-500 bg-white rounded-2xl"
              >
                <p class="text-lg">Aucun Utilisateur trouvé</p>
                <p class="text-sm mt-2">
                  Invitez des amis à s'inscrire pour agrandir votre réseau !
                </p>
              </div>

              <UserCard
                v-for="user in userStore.searchUsers(searchQuery)"
                :key="user._id"
                :user="user"
                type="user"
              />
            </template>
            <template v-if="activeTab === 'all'">
              <div
                v-if="userStore.searchFriends(searchQuery).length === 0"
                class="text-center py-12 text-gray-500 bg-white rounded-2xl"
              >
                <p class="text-lg">Aucun ami trouvé</p>
                <p class="text-sm mt-2">
                  Commencez à ajouter des amis pour agrandir votre réseau !
                </p>
              </div>

              <FriendCard
                v-for="friend in userStore.searchFriends(searchQuery)"
                :key="friend._id"
                :friend="friend"
                type="friend"
              />
            </template>

            <template v-if="activeTab === 'requests'">
              <div
                v-if="userStore.friendRequests.length === 0"
                class="text-center py-12 text-gray-500 bg-white rounded-2xl"
              >
                <p class="text-lg">Aucune demande en attente</p>
              </div>

              <FriendCard
                v-for="request in userStore.friendRequests"
                :key="request._id"
                :friend="request.user"
                :request-id="request._id.toString()"
                type="request"
              />
            </template>
          </div>
        </div>
      </main>
    </div>

    <!-- Modal Profil -->
    <div
      v-if="selectedProfile"
      class="fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50"
      @click.self="selectedProfile = null"
    >
      <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="relative">
          <button
            @click="selectedProfile = null"
            class="absolute top-4 right-4 bg-red-500 rounded-full p-2 hover:bg-red-400 border-gray-600 shadow-xl hover:cursor-pointer"
          >
            <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>

        <div class="pt-15 px-6 pb-6">
          <div class="flex items-end gap-4 -mt-12 mb-4">
            <img
              :src="selectedProfile.photo"
              class="w-32 h-32 rounded-full border-4 border-white object-cover"
            />
            <div class="flex-1 pb-2">
              <h2 class="text-2xl font-bold text-gray-900">{{ selectedProfile.name }}</h2>
              <p class="text-gray-600">{{ selectedProfile.work }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="text-center">
              <p class="text-2xl font-bold text-orange-400">
                {{ selectedProfile.stats?.posts || 0 }}
              </p>
              <p class="text-sm text-gray-500">Post(s)</p>
            </div>
          </div>

          <div v-if="selectedProfile.bio" class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-2">À propos</h3>
            <p class="text-gray-600">{{ selectedProfile.bio }}</p>
          </div>

          <div v-if="selectedProfile.email" class="mb-4">
            <p class="text-sm text-gray-500">
              <span class="font-semibold text-black text-lg">Email :</span>
              {{ selectedProfile.email }}
            </p>
          </div>

          <div v-if="selectedProfile.telephone" class="mb-6">
            <p class="text-sm text-gray-500">
              <span class="font-medium">Téléphone:</span> {{ selectedProfile.telephone }}
            </p>
          </div>

          <div class="flex gap-3">
            <button
              v-if="!userStore.isFriend(selectedProfile._id)"
              @click="sendFriendRequest(selectedProfile._id)"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg transition font-medium hover:cursor-pointer"
            >
              Ajouter comme ami
            </button>
            <button
              v-else
              @click="removeFriend(selectedProfile._id)"
              class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium"
            >
              Retirer des amis
            </button>
          </div>
        </div>
      </div>
    </div>

    <MobileNavigation />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import SideBarLeft from "@/components/SidebarLeft.vue";
import Header from "@/components/HeaderHome.vue";
import FriendCard from "@/components/FriendCard.vue";
import MobileNavigation from "@/components/MobileNavigation.vue";
import { useUserStore } from "@/stores/UserStore";
import UserCard from "@/components/UserCard.vue";

const userStore = useUserStore();
const searchQuery = ref("");
const activeTab = ref("all");
const selectedProfile = ref(null);

function sendFriendRequest(userId) {
  userStore.sendFriendRequest(userId);
  selectedProfile.value = null;
  alert("Demande d'ami envoyée !");
}

function removeFriend(friendId) {
  if (confirm("Êtes-vous sûr de vouloir retirer cet ami ?")) {
    userStore.removeFriend(friendId);
    selectedProfile.value = null;
  }
}

function viewFriendProfile(friend) {
  selectedProfile.value = {
    ...friend,
    stats: friend.stats,
    bio: friend.bio || "Aucune biographie disponible",
  };
}

if (typeof window !== "undefined") {
  window.viewFriendProfile = viewFriendProfile;
}

onMounted(async () => {
  await userStore.fetchUsers();
  await userStore.loadFriends();
  await userStore.loadFriendRequests();
});
</script>
