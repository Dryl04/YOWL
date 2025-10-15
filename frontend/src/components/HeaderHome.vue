<template>
  <header class="bg-white border-b border-gray-200">
    <div class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 flex justify-between items-center gap-2 sm:gap-4">
      <!-- <div class="hidden lg:flex items-center">
        <h2
          class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent"
        >
          YOWL
        </h2>
      </div> -->

      <!-- Barre de recherche desktop -->
      <div class="hidden lg:flex flex-1 max-w-xl mx-auto">
        <div class="relative w-full">
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="Rechercher des sujets, personnes ..."
            class="w-full pl-4 pr-10 py-2.5 rounded-full bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:bg-white focus:border-none text-sm"
          />
          <svg
            class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 hover:cursor-pointer"
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

          <!-- Résultats de recherche -->
          <div
            v-if="searchQuery && searchResults.length > 0"
            class="absolute top-full mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto z-50"
          >
            <div class="p-2">
              <p class="text-xs text-gray-500 px-3 py-2">Résultats</p>
              <button
                v-for="result in searchResults"
                :key="result.id"
                @click="selectSearchResult(result)"
                class="w-full text-left px-3 py-2 rounded hover:bg-gray-50 flex items-center gap-3"
              >
                <img
                  v-if="result.type === 'user'"
                  :src="result.photo"
                  class="w-8 h-8 rounded-full"
                />
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-sm truncate">{{ result.name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ result.subtitle }}</p>
                </div>
              </button>
            </div>
          </div>
          <div
            v-else-if="searchQuery && searchResults.length === 0"
            class="absolute top-full mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50"
          >
            <p class="text-sm text-gray-500 text-center">Aucun résultat trouvé</p>
          </div>
        </div>
      </div>

      <!-- Message vérif -->
      <div
        v-if="
          userStore.currentUser?.isVerified === false && userStore.currentUser?.isAdmin === false
        "
        class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg whitespace-nowrap"
      >
        <svg
          class="w-5 h-5 text-red-500 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          ></path>
        </svg>
        <span class="text-red-600 font-medium">Non vérifié.</span>
        <button
          @click="userStore.sendVerificationEmail"
          class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-colors hover:cursor-pointer"
        >
          Vérifier
        </button>
      </div>

      <div class="lg:hidden flex items-center">
        <h2
          class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent"
        >
          YOWL
        </h2>
      </div>
      <!-- Message vérif mobile -->
      <div
        v-if="
          userStore.currentUser?.isVerified === false && userStore.currentUser?.isAdmin === false
        "
        class="lg:hidden flex items-center gap-1 px-2 py-1 bg-red-50 border border-red-200 rounded-lg"
      >
        <svg
          class="w-3 h-3 text-red-500 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          ></path>
        </svg>
        <span class="text-red-600 font-medium text-[12px] whitespace-nowrap">Non vérifié.</span>
        <button
          @click="userStore.sendVerificationEmail"
          class="text-blue-600 hover:text-blue-800 hover:underline font-medium text-[12px] transition-colors hover:cursor-pointer whitespace-nowrap"
        >
          Vérifier
        </button>
      </div>

      <button
        @click="showPostModal = true"
        class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-3 sm:px-4 py-2 rounded-full font-medium flex items-center gap-1 sm:gap-2 hover:shadow-lg transition hover:cursor-pointer text-sm sm:text-base whitespace-nowrap"
      >
        <span class="hidden sm:inline">Poster</span>
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4v16m8-8H4"
          ></path>
        </svg>
      </button>

      <button
        @click="showMobileSearch = true"
        class="lg:hidden p-2 text-gray-600 hover:text-orange-600 transition"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          ></path>
        </svg>
      </button>

      <button
        @click="handleLogout"
        class="lg:hidden p-2 text-gray-600 hover:text-red-600 transition"
        title="Déconnexion"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
          ></path>
        </svg>
      </button>
    </div>

    <!-- Modal de recherche mobile -->
    <div v-if="showMobileSearch" class="fixed inset-0 bg-white z-50 lg:hidden">
      <div class="flex flex-col h-full">
        <div class="flex items-center gap-3 p-4 border-b border-gray-200">
          <button @click="closeMobileSearch" class="p-2 text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              ></path>
            </svg>
          </button>
          <div class="relative flex-1">
            <input
              ref="mobileSearchInput"
              v-model="searchQuery"
              @input="handleSearch"
              type="text"
              placeholder="Rechercher des sujets, personnes ..."
              class="w-full pl-4 pr-10 py-2.5 rounded-full bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:bg-white text-sm"
            />
            <svg
              v-if="!searchQuery"
              class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2"
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
            <button
              v-else
              @click="clearMobileSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                ></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Résultats de recherche mobile -->
        <div class="flex-1 overflow-y-auto">
          <div v-if="searchQuery && searchResults.length > 0" class="p-4">
            <p class="text-xs text-gray-500 mb-3">{{ searchResults.length }} résultat(s)</p>
            <div class="space-y-2">
              <button
                v-for="result in searchResults"
                :key="result.id"
                @click="selectSearchResultMobile(result)"
                class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 flex items-center gap-3 border border-gray-100"
              >
                <img
                  v-if="result.type === 'user'"
                  :src="result.photo"
                  class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                />
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-sm truncate">{{ result.name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ result.subtitle }}</p>
                </div>
                <svg
                  class="w-5 h-5 text-gray-400 flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  ></path>
                </svg>
              </button>
            </div>
          </div>
          <div v-else-if="searchQuery && searchResults.length === 0" class="p-8 text-center">
            <svg
              class="w-16 h-16 text-gray-300 mx-auto mb-4"
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
            <p class="text-sm text-gray-500">Aucun résultat trouvé</p>
            <p class="text-xs text-gray-400 mt-1">Essayez avec d'autres mots-clés</p>
          </div>
          <div v-else class="p-8 text-center">
            <svg
              class="w-16 h-16 text-gray-300 mx-auto mb-4"
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
            <p class="text-sm text-gray-500">Rechercher des personnes ou des posts</p>
            <p class="text-xs text-gray-400 mt-1">Commencez à taper pour voir les résultats</p>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showPostModal"
      class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-2 sm:p-4"
      @click.self="showPostModal = false"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-2 sm:mx-4 max-h-[90vh] overflow-y-auto"
      >
        <div
          class="p-4 sm:p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10"
        >
          <h2 class="text-lg sm:text-xl font-bold">Créer un post</h2>
          <button
            @click="showPostModal = false"
            class="text-gray-400 hover:text-gray-600 hover:cursor-pointer"
          >
            <svg
              class="w-5 h-5 sm:w-6 sm:h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>

        <div class="p-4 sm:p-6">
          <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
            <div
              class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-orange-400 to-red-400 flex items-center justify-center flex-shrink-0"
            >
              <div v-if="userStore.currentUserAvatar">
                <img
                  :src="userStore.currentUserAvatar"
                  alt="Avatar"
                  class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover"
                />
              </div>
              <div v-else>
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  ></path>
                </svg>
              </div>
            </div>

            <div class="min-w-0 flex-1">
              <p class="font-semibold text-sm sm:text-base truncate">
                {{ userStore.currentUserName }}
              </p>
              <p class="text-xs sm:text-sm text-gray-500 truncate">
                {{ userStore.currentUser?.work }}
              </p>
            </div>
          </div>

          <textarea
            v-model="newPost.description"
            placeholder="Donnez votre avis ..."
            rows="5"
            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none"
          ></textarea>

          <input
            v-model="newPost.link"
            type="url"
            placeholder="Ajouter un lien"
            class="w-full px-3 sm:px-4 py-2 sm:py-3 mt-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500"
            required
          />

          <div v-if="newPost.imagePreview" class="mt-3 sm:mt-4 relative">
            <img :src="newPost.imagePreview" class="w-full rounded-lg" />
            <button
              @click="removeImage"
              class="absolute top-2 right-2 bg-red-500 text-white p-1.5 sm:p-2 rounded-full hover:bg-red-600 hover:cursor-pointer"
            >
              <svg
                class="w-4 h-4 sm:w-5 sm:h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                ></path>
              </svg>
            </button>
          </div>

          <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200 gap-3 sm:gap-0"
          >
            <label
              class="flex items-center gap-2 text-gray-600 hover:text-orange-600 cursor-pointer"
            >
              <svg
                class="w-5 h-5 sm:w-6 sm:h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                ></path>
              </svg>
              <span class="text-xs sm:text-sm font-medium">Ajouter une image</span>
              <input type="file" accept="image/*" @change="handleImageUpload" class="hidden" />
            </label>

            <button
              @click="createPost"
              :disabled="!newPost.description.trim()"
              :class="[
                'w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg font-medium transition text-sm sm:text-base',
                newPost.description.trim() && newPost.link.trim()
                  ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white hover:shadow-lg hover:cursor-pointer'
                  : 'bg-gray-200 text-gray-400 cursor-not-allowed',
              ]"
            >
              <span v-if="isUploading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
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
                Publication en cours...
              </span>
              <span v-else> Publier </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useUserStore } from "@/stores/UserStore";
import { usePostStore } from "@/stores/PostsStore";
import { useRouter } from "vue-router";
import { uploadImage } from "@/services/imageUpload";

const isUploading = ref(false);

const userStore = useUserStore();
const postStore = usePostStore();
const router = useRouter();

const searchQuery = ref("");
const searchResults = ref([]);
const showMobileSearch = ref(false);
const mobileSearchInput = ref(null);

const showPostModal = ref(false);

const newPost = ref({
  description: "",
  link: "",
  image: null,
  imagePreview: null,
});

function handleSearch() {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    postStore.clearSearchQuery();
    return;
  }

  const query = searchQuery.value.toLowerCase();

  const friendResults = userStore.friends
    .filter((f) => f.name.toLowerCase().includes(query))
    .map((f) => ({
      id: f._id,
      type: "user",
      name: f.name,
      subtitle: f.work,
      photo: f.photo,
    }));

  const postResults = postStore.posts
    .filter(
      (p) =>
        p.content.description.toLowerCase().includes(query) ||
        (p.link && p.link.toLowerCase().includes(query)) ||
        p.author.name.toLowerCase().includes(query)
    )
    .map((p) => ({
      id: p._id,
      type: "post",
      name: p.content.description.substring(0, 50) + "...",
      subtitle: `Par ${p.author.name}`,
      photo: p.author.photo,
    }));

  searchResults.value = [...friendResults, ...postResults].slice(0, 8);
}

function selectSearchResult(result) {
  if (result.type === "user") {
    router.push("/friends");
  } else if (result.type === "post") {
    postStore.setSearchQuery(searchQuery.value);
    if (router.currentRoute.value.path !== "/") {
      router.push("/");
    }
  }
  searchQuery.value = "";
  searchResults.value = [];
}

function handleImageUpload(event) {
  const file = event.target.files[0];
  if (file) {
    newPost.value.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      newPost.value.imagePreview = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function removeImage() {
  newPost.value.image = null;
  newPost.value.imagePreview = null;
}

async function createPost() {
  if (!newPost.value.description.trim()) return;

  isUploading.value = true;
  let imageUrl = null;

  try {
    if (newPost.value.image) {
      imageUrl = await uploadImage(newPost.value.image);
    }

    const post = {
      author: {
        name: userStore.currentUserName,
        work: userStore.currentUser?.work || "Membre",
        photo: userStore.currentUserAvatar,
        userId: userStore.currentUser?.id,
      },
      content: {
        description: newPost.value.description,
        image: imageUrl,
      },
      link: newPost.value.link || null,
    };

    await postStore.addPost(post);

    newPost.value = {
      description: "",
      link: "",
      image: null,
      imagePreview: null,
    };
    showPostModal.value = false;
  } catch (error) {
    console.error("Erreur création post:", error);
    alert("Erreur lors de la création du post");
  } finally {
    isUploading.value = false;
  }
}

function handleLogout() {
  userStore.logout();
  router.push("/signin");
}

function closeMobileSearch() {
  showMobileSearch.value = false;
  searchQuery.value = "";
  searchResults.value = [];
}

function clearMobileSearch() {
  searchQuery.value = "";
  searchResults.value = [];
  postStore.clearSearchQuery();
}

function selectSearchResultMobile(result) {
  if (result.type === "user") {
    router.push("/friends");
  } else if (result.type === "post") {
    postStore.setSearchQuery(searchQuery.value);
    if (router.currentRoute.value.path !== "/") {
      router.push("/");
    }
  }
  closeMobileSearch();
}

onMounted(async () => {
  await userStore.fetchCurrentUser();
});
</script>
