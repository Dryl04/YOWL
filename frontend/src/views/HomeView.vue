<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
      <div
        class="hidden lg:block w-64 bg-white border-r border-gray-200 h-screen fixed left-0 top-0 z-20"
      >
        <SideBarLeft />
      </div>

      <div class="fixed top-0 left-0 lg:left-64 lg:right-80 right-0 z-40">
        <Header />
      </div>

      <div class="flex-1 flex flex-col bg-gray-50 lg:ml-64 lg:mr-80 pt-16 pb-16 lg:pb-0">
        <div class="flex-1 flex bg-gray-50 overflow-hidden">
          <main class="flex-1 overflow-y-auto bg-gray-50 h-full">
            <div class="max-w-2xl mx-auto py-3 sm:py-4 lg:py-6 px-2 sm:px-4 space-y-3 sm:space-y-4">
              <!-- Message recherche active -->
              <div
                v-if="postStore.searchQuery"
                class="bg-white rounded-2xl border border-gray-100 p-3 sm:p-4"
              >
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2 flex-1 min-w-0">
                    <svg
                      class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 flex-shrink-0"
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
                    <p class="text-xs sm:text-sm min-w-0">
                      <span class="text-gray-600">Recherche :</span>
                      <span class="font-semibold text-purple-600 ml-1 break-words"
                        >"{{ postStore.searchQuery }}"</span
                      >
                      <span class="text-gray-500 ml-1 sm:ml-2 whitespace-nowrap"
                        >({{ postStore.sortedPosts.length }} résultat{{
                          postStore.sortedPosts.length > 1 ? "s" : ""
                        }})</span
                      >
                    </p>
                  </div>
                  <button
                    @click="postStore.clearSearchQuery()"
                    class="text-gray-400 hover:text-gray-600 transition flex-shrink-0"
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
              </div>

              <!-- Message filtre #tag actif -->
              <div
                v-if="postStore.hashtagFilter"
                class="bg-white rounded-2xl border border-gray-100 p-3 sm:p-4"
              >
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2 flex-1 min-w-0">
                    <svg
                      class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                      ></path>
                    </svg>
                    <p class="text-xs sm:text-sm min-w-0">
                      <span class="text-gray-600">Posts avec</span>
                      <span class="font-semibold text-purple-600 ml-1 break-words">{{
                        postStore.hashtagFilter
                      }}</span>
                      <span class="text-gray-500 ml-1 sm:ml-2 whitespace-nowrap"
                        >({{ postStore.filteredPostsCount }} résultat{{
                          postStore.filteredPostsCount > 1 ? "s" : ""
                        }})</span
                      >
                    </p>
                  </div>
                  <button
                    @click="postStore.clearHashtagFilter()"
                    class="text-gray-400 hover:text-gray-600 transition flex-shrink-0"
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
              </div>

              <div v-if="loading" class="space-y-3 sm:space-y-4">
                <div
                  v-for="i in 3"
                  :key="i"
                  class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 animate-pulse"
                >
                  <div class="flex items-start gap-2 sm:gap-3 mb-3 sm:mb-4">
                    <div
                      class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-200 rounded-full flex-shrink-0"
                    ></div>
                    <div class="flex-1 min-w-0">
                      <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                      <div class="h-2 sm:h-3 bg-gray-200 rounded w-1/4"></div>
                    </div>
                    <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-200 rounded"></div>
                  </div>
                  <div class="space-y-2 mb-3 sm:mb-4">
                    <div class="h-3 sm:h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-3 sm:h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/2"></div>
                  </div>
                  <div
                    class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100"
                  >
                    <div class="flex gap-2 sm:gap-4">
                      <div class="h-7 sm:h-8 w-14 sm:w-16 bg-gray-200 rounded"></div>
                      <div class="h-7 sm:h-8 w-16 sm:w-20 bg-gray-200 rounded"></div>
                      <div class="h-7 sm:h-8 w-14 sm:w-16 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-7 sm:h-8 w-7 sm:w-8 bg-gray-200 rounded"></div>
                  </div>
                </div>
              </div>

              <div
                v-if="postStore.sortedPosts.length === 0 && postStore.isLoading === false"
                class="text-center py-8 sm:py-12 text-gray-500"
              >
                <p class="text-base sm:text-lg">
                  {{
                    postStore.searchQuery
                      ? "Aucun post trouvé"
                      : postStore.hashtagFilter
                      ? "Aucun post avec ce hashtag"
                      : "Aucun post pour le moment"
                  }}
                </p>
                <p class="text-xs sm:text-sm mt-2">
                  {{
                    postStore.searchQuery
                      ? "Essayez une autre recherche"
                      : postStore.hashtagFilter
                      ? "Essayez un autre hashtag"
                      : "Soyez le premier à partager quelque chose !"
                  }}
                </p>
              </div>

              <TransitionGroup name="post" tag="div" class="space-y-3 sm:space-y-4">
                <PostCard
                  v-for="post in postStore.sortedPosts"
                  :key="post._id"
                  :post="post"
                  class="post-item"
                />
              </TransitionGroup>
            </div>
          </main>

          <div
            class="hidden lg:block w-80 bg-white border-l border-gray-200 h-screen fixed right-0 top-0 z-20"
          >
            <SidebarRight />
          </div>
        </div>
      </div>
    </div>

    <MobileNavigation />
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import SideBarLeft from "@/components/SidebarLeft.vue";
import SidebarRight from "@/components/SidebarRight.vue";
import Header from "@/components/HeaderHome.vue";
import PostCard from "@/components/PostCard.vue";
import MobileNavigation from "@/components/MobileNavigation.vue";
import { usePostStore } from "@/stores/PostsStore";
import { useUserStore } from "@/stores/UserStore";

const postStore = usePostStore();
const userStore = useUserStore();

const loading = ref(true);

onMounted(async () => {
  if (userStore.isAuthenticated) {
    await postStore.fetchPosts();
    await userStore.loadFriends();
    await userStore.loadFriendRequests();
    loading.value = false;
  }
});
</script>

<style scoped>
.post-enter-active,
.post-leave-active {
  transition: all 0.3s ease;
}

.post-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.post-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

.post-move {
  transition: transform 0.3s ease;
}

/* Animation pour les boutons */
.post-item :deep(button) {
  transition: all 0.2s ease;
}

.post-item :deep(button:hover) {
  transform: translateY(-1px);
}

/* Animation pour les likes */
.post-item :deep(button:active) {
  transform: scale(0.95);
}
</style>
