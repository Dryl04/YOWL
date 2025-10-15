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

      <div class="flex-1 flex flex-col bg-gray-50 lg:ml-64 pt-16 pb-20 lg:pb-0">
        <div class="flex-1 flex bg-gray-50">
          <main class="flex-1 overflow-y-auto max-w-6xl mx-auto">
            <div class="max-w-7xl mx-auto py-4 px-2">
              <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 pt-4">Tableau de bord</h1>
              </div>

              <div v-if="loading" class="flex items-center justify-center py-12">
                <p class="ml-3 text-gray-600">Patientez...</p>
              </div>

              <div v-else class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <StatCard
                    title="Utilisateurs totaux"
                    :value="stats.users.length"
                    subtitle="Membres inscrits"
                    icon="users"
                  />

                  <StatCard
                    title="Posts publiés"
                    :value="stats.posts.length"
                    subtitle="Contenu partagé"
                    icon="posts"
                  />

                  <StatCard
                    title="Commentaires"
                    :value="stats.comments.length"
                    subtitle="Interactions"
                    icon="comments"
                  />

                  <StatCard
                    title="Relations"
                    :value="stats.relation.length"
                    subtitle="Connexions d'amitié"
                    icon="friends"
                  />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisateurs actifs</h3>
                    <div class="space-y-4">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Utilisateurs actifs</span>
                        <span class="font-semibold text-green-600">{{ activeUsersCount }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Utilisateurs inactifs</span>
                        <span class="font-semibold text-red-600">{{ inactiveUsersCount }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Administrateurs</span>
                        <span class="font-semibold text-blue-600">{{ adminUsersCount }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Engagement</h3>
                    <div class="space-y-4">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Likes totaux</span>
                        <span class="font-semibold text-purple-600">{{ totalLikes }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Partages totaux</span>
                        <span class="font-semibold text-orange-600">{{ totalShares }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Moyenne likes/post</span>
                        <span class="font-semibold text-indigo-600">{{ averageLikesPerPost }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                      <h3 class="text-lg font-semibold text-gray-900">Top utilisateurs</h3>
                      <button
                        @click="navigateTo('/admin/users')"
                        class="text-sm text-orange-600 font-medium transition-colors cursor-pointer"
                      >
                        Voir tout
                      </button>
                    </div>
                    <div class="space-y-3">
                      <div
                        v-for="(user, index) in topUsers"
                        :key="user.id"
                        class="flex items-center justify-between"
                      >
                        <div class="flex items-center space-x-3">
                          <span class="text-sm font-medium text-gray-500">{{ index + 1 }}</span>
                          <img
                            :src="
                              user.photo ||
                              'https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg'
                            "
                            :alt="user.name"
                            class="w-8 h-8 rounded-full object-cover"
                          />
                          <span class="text-sm font-medium text-gray-900">{{ user.name }}</span>
                        </div>
                        <span class="text-sm text-gray-600">{{ user.posts_count }} posts</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                      <h3 class="text-lg font-semibold text-gray-900">Posts récents</h3>
                      <button
                        @click="navigateTo('/')"
                        class="text-sm text-orange-600 font-medium transition-colors cursor-pointer"
                      >
                        Voir le feed
                      </button>
                    </div>
                    <div class="space-y-3">
                      <div
                        v-for="post in recentPosts"
                        :key="post.id"
                        class="flex items-center justify-between"
                      >
                        <div class="flex-1 min-w-0">
                          <p class="text-sm text-gray-900 truncate">{{ post.description }}</p>
                          <p class="text-xs text-gray-500">{{ formatDate(post.created_at) }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                          <span class="text-xs text-gray-500">{{ post.likes }} ❤️</span>
                          <span class="text-xs text-gray-500">{{ post.shares }} 🔄</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>

    <MobileNavigation />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import SideBarLeft from "@/components/SidebarLeft.vue";
import Header from "@/components/HeaderHome.vue";
import StatCard from "@/components/StatCard.vue";
import MobileNavigation from "@/components/MobileNavigation.vue";
import api from "@/services/api";

const router = useRouter();

// les état locaux
const loading = ref(false);
const stats = ref({
  users: [],
  posts: [],
  comments: [],
  relation: [],
});

const navigateTo = (path) => {
  router.push(path);
};

const activeUsersCount = computed(() => {
  return stats.value.users.filter((user) => user.is_active).length;
});

const inactiveUsersCount = computed(() => {
  return stats.value.users.filter((user) => !user.is_active).length;
});

const adminUsersCount = computed(() => {
  return stats.value.users.filter((user) => user.role === "admin").length;
});

const totalLikes = computed(() => {
  return stats.value.posts.reduce((total, post) => total + (post.likes || 0), 0);
});

const totalShares = computed(() => {
  return stats.value.posts.reduce((total, post) => total + (post.shares || 0), 0);
});

const averageLikesPerPost = computed(() => {
  if (stats.value.posts.length === 0) return 0;
  return Math.round(totalLikes.value / stats.value.posts.length);
});

const topUsers = computed(() => {
  const userPostCounts = {};
  stats.value.posts.forEach((post) => {
    userPostCounts[post.user_id] = (userPostCounts[post.user_id] || 0) + 1;
  });

  const usersWithPosts = stats.value.users.map((user) => ({
    ...user,
    posts_count: userPostCounts[user.id] || 0,
  }));

  return usersWithPosts.sort((a, b) => b.posts_count - a.posts_count).slice(0, 5);
});

const recentPosts = computed(() => {
  return [...stats.value.posts]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5);
});

// Méthodes
const fetchStats = async () => {
  loading.value = true;
  try {
    const response = await api.get("/admin/stats");
    stats.value = response.data;
    // console.log('statistiques:', response.data)
  } catch (error) {
    console.error("Erreur lors du chargement des statistiques:", error);
    if (error.response?.status === 401) {
      alert("Vous devez être administrateur.");
    }
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

onMounted(async () => {
  await fetchStats();
});
</script>
