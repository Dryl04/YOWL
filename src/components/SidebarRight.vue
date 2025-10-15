<template>
  <div class="h-screen bg-white border-l border-gray-200 p-4 sm:p-6 overflow-y-auto">
    <div class="mb-4 sm:mb-6">
      <h2 class="flex text-lg sm:text-xl font-bold text-gray-900 justify-center">Tendances</h2>

      <div v-if="popularHashtags.length === 0" class="text-center py-6 sm:py-8 text-gray-500">
        <p class="text-xs sm:text-sm">Aucune tendance pour le moment</p>
      </div>

      <div v-else class="space-y-2 mt-4">
        <button
          v-for="tag in popularHashtags"
          :key="tag.name"
          @click="filterByHashtag(tag.name)"
          class="w-full text-left px-3 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-purple-50 transition group"
        >
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <p
                class="text-orange-400 font-semibold group-hover:text-orange-600 text-sm sm:text-base truncate"
              >
                {{ tag.name }}
              </p>
              <p class="text-xs text-gray-500 mt-1">
                {{ tag.count }} post{{ tag.count > 1 ? "s" : "" }}
              </p>
            </div>
            <svg
              class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-orange-600 transition flex-shrink-0"
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
          </div>
        </button>
      </div>
    </div>

    <!-- Badge filtre actif -->
    <div v-if="activeHashtag" class="mt-4 p-2 sm:p-3 bg-purple-100 rounded-lg">
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 flex-1 min-w-0">
          <svg
            class="w-3 h-3 sm:w-4 sm:h-4 text-purple-600 flex-shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
            ></path>
          </svg>
          <span class="text-xs sm:text-sm font-medium text-purple-900 truncate">
            Filtré : {{ activeHashtag }}
          </span>
        </div>
        <button @click="clearFilter" class="text-purple-600 hover:text-purple-800 flex-shrink-0">
          <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePostStore } from "@/stores/PostsStore";
import { useRouter } from "vue-router";

const postStore = usePostStore();
const router = useRouter();
const activeHashtag = ref(null);

const popularHashtags = computed(() => {
  const hashtagMap = new Map();

  postStore.posts.forEach((post) => {
    const hashtags = extractHashtags(post.content.description);
    const uniqueHashtags = [...new Set(hashtags)];
    uniqueHashtags.forEach((tag) => {
      hashtagMap.set(tag, (hashtagMap.get(tag) || 0) + 1);
    });
  });

  return Array.from(hashtagMap.entries())
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 8);
});

function extractHashtags(text) {
  const regex = /#[\wÀ-ÿ]+/g;
  const matches = text.match(regex);
  return matches ? matches.map((tag) => tag.toLowerCase()) : [];
}

function filterByHashtag(hashtag) {
  activeHashtag.value = hashtag;
  postStore.setHashtagFilter(hashtag);

  if (router.currentRoute.value.path !== "/") {
    router.push("/");
  }
}

function clearFilter() {
  activeHashtag.value = null;
  postStore.clearHashtagFilter();
}
</script>
