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
                <h1 class="text-2xl font-bold text-gray-900 px-2 pt-4">Utilisateurs</h1>
              </div>

              <div class="p-2 sm:p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                  <div
                    class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center w-full sm:w-auto"
                  >
                    <div class="relative w-full sm:w-80">
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Rechercher un utilisateur..."
                        class="pl-10 pr-4 py-2 border border-zinc-200 rounded-lg text-sm focus:ring-1 outline-none w-full"
                      />
                      <svg
                        class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"
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

                    <select
                      v-model="roleFilter"
                      class="px-6 outline-none py-2 border text-sm border-zinc-200 rounded-lg focus:ring-1 focus:border-transparent w-full sm:w-auto"
                    >
                      <option value="">Tous les rôles</option>
                      <option value="admin">Admin</option>
                      <option value="user">Utilisateur</option>
                    </select>
                  </div>

                  <button
                    @click="showCreateModal = true"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors w-full sm:w-auto"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                      ></path>
                    </svg>
                    Nouvel utilisateur
                  </button>
                </div>
              </div>

              <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div v-if="loading" class="p-8 text-center">
                  <p class="mt-2 text-gray-600">Patientez...</p>
                </div>

                <div
                  v-else-if="filteredUsers.length === 0"
                  class="p-6 sm:p-8 text-center flex flex-col items-center text-gray-500"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-12 sm:size-15"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"
                    />
                  </svg>

                  <p class="text-base sm:text-lg font-medium mb-2">Aucun utilisateur trouvé.</p>
                  <p class="text-xs sm:text-sm">
                    Essayez de modifier vos critères de recherche puis réessayer.
                  </p>
                </div>

                <!-- Vue mobile : cards -->
                <div
                  v-if="!loading && filteredUsers.length > 0"
                  class="block lg:hidden divide-y divide-gray-200"
                >
                  <div
                    v-for="user in filteredUsers"
                    :key="user.id"
                    class="p-4 hover:bg-gray-50 transition-colors"
                  >
                    <div class="flex items-start gap-3">
                      <img
                        :src="
                          user.photo ||
                          'https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg'
                        "
                        :alt="user.name"
                        class="h-12 w-12 rounded-full object-cover flex-shrink-0"
                      />
                      <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                          <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                              {{ user.name }}
                            </h3>
                            <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ user.work || "Membre" }}</p>
                          </div>
                          <div class="flex gap-1 flex-shrink-0">
                            <button
                              @click="editUser(user)"
                              class="text-indigo-600 hover:text-indigo-900 p-1.5 rounded-lg hover:bg-indigo-50"
                              title="Modifier"
                            >
                              <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                ></path>
                              </svg>
                            </button>
                            <button
                              @click="deleteUser(user)"
                              class="text-red-600 hover:text-red-900 p-1.5 rounded-lg hover:bg-red-50"
                              title="Supprimer"
                            >
                              <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                ></path>
                              </svg>
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                          <span
                            :class="
                              user.role === 'admin'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-green-100 text-green-800'
                            "
                            class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full"
                          >
                            {{ user.role === "admin" ? "Admin" : "Utilisateur" }}
                          </span>
                          <span
                            class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                          >
                            Actif
                          </span>
                          <span class="text-xs text-gray-500">
                            {{ formatDate(user.created_at) }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vue desktop : tableau -->
                <div
                  v-if="!loading && filteredUsers.length > 0"
                  class="hidden lg:block overflow-x-auto"
                >
                  <table class="min-w-full divide-y divide-gray-50">
                    <thead class="">
                      <tr>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider"
                        >
                          Utilisateur
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider"
                        >
                          Email
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider"
                        >
                          Rôle
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider"
                        >
                          Statut
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider"
                        >
                          Créé le
                        </th>
                        <th
                          class="px-6 py-3 text-right text-xs font-medium text-gray-500 tracking-wider"
                        >
                          Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-gray-50 divide-y divide-gray-200">
                      <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                              <img
                                :src="
                                  user.photo ||
                                  'https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg'
                                "
                                :alt="user.name"
                                class="h-10 w-10 rounded-full object-cover"
                              />
                            </div>
                            <div class="ml-4">
                              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                              <div class="text-sm text-gray-500">{{ user.work || "Membre" }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ user.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="
                              user.role === 'admin'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-green-100 text-green-800'
                            "
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                          >
                            {{ user.role === "admin" ? "Admin" : "Utilisateur" }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                          >
                            Actif
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ formatDate(user.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <div class="flex justify-end gap-2">
                            <button
                              @click="editUser(user)"
                              class="text-indigo-600 hover:text-indigo-900 p-1 rounded"
                              title="Modifier"
                            >
                              <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                ></path>
                              </svg>
                            </button>
                            <button
                              @click="deleteUser(user)"
                              class="text-red-600 hover:text-red-900 p-1 rounded"
                              title="Supprimer"
                            >
                              <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                ></path>
                              </svg>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>

    <!-- Modal de création/édition -->
    <div
      v-if="showCreateModal || showEditModal"
      class="fixed inset-0 overflow-y-auto h-full w-full z-50 bg-black/70 flex items-start justify-center p-2 sm:p-4"
    >
      <div
        class="relative my-4 sm:my-8 mx-auto p-4 sm:p-6 bg-white w-full max-w-md sm:max-w-lg rounded-lg"
      >
        <div class="mt-2 sm:mt-3">
          <div class="flex items-center gap-2 mb-3 sm:mb-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-5 h-5 sm:w-6 sm:h-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"
              />
            </svg>

            <h3 class="text-base sm:text-lg font-medium text-black">
              {{ showCreateModal ? "Créer un utilisateur" : "Modifier l'utilisateur" }}
            </h3>
          </div>
          <form @submit.prevent="submitForm" class="space-y-3 sm:space-y-4">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
              <input
                v-model="formData.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1"
                placeholder="Votre nom complet"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                v-model="formData.email"
                type="email"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1"
                placeholder="votre.email@example.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
              <input
                v-model="formData.password"
                type="password"
                :required="showCreateModal"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1"
                placeholder="••••••••"
              />
            </div>

            <div v-if="formData.password">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Confirmer le mot de passe</label
              >
              <input
                v-model="formData.password_confirmation"
                type="password"
                :required="!!formData.password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1"
                placeholder="••••••••"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
              <select
                v-model="formData.role"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1"
              >
                <option value="user">Utilisateur</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
              <input
                v-model="formData.work"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1"
                placeholder="Développeur"
              />
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors order-2 sm:order-1"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-md disabled:opacity-50 transition-colors order-1 sm:order-2"
              >
                {{ showCreateModal ? "Ajouter" : "Modifier" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <MobileNavigation />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import SideBarLeft from "@/components/SidebarLeft.vue";
import Header from "@/components/HeaderHome.vue";
import MobileNavigation from "@/components/MobileNavigation.vue";
import api from "@/services/api";

const searchQuery = ref("");
const roleFilter = ref("");
const showCreateModal = ref(false);
const showEditModal = ref(false);
const loading = ref(false);
const users = ref([]);
const formData = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "user",
  work: "",
});
const editingUser = ref(null);

// Computed
const filteredUsers = computed(() => {
  let filtered = users.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (user) => user.name.toLowerCase().includes(query) || user.email.toLowerCase().includes(query)
    );
  }

  if (roleFilter.value) {
    filtered = filtered.filter((user) => user.role === roleFilter.value);
  }

  return filtered;
});

// Méthodes
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("fr-FR");
};

const fetchUsers = async () => {
  loading.value = true;
  try {
    const response = await api.get("/admin/users");
    users.value = response.data;
  } catch (error) {
    console.error("Erreur lors du chargement des utilisateurs:", error);
    if (error.response?.status === 401) {
      alert("Vous devez être administrateur pour accéder à cette page.");
    }
  } finally {
    loading.value = false;
  }
};

const editUser = (user) => {
  editingUser.value = user;
  formData.value = {
    name: user.name,
    email: user.email,
    password: "",
    role: user.role || "user",
    work: user.work || "",
  };
  showEditModal.value = true;
};

const deleteUser = async (user) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user.name}" ?`)) {
    try {
      await api.delete(`/users/${user.id}`);
      await fetchUsers();
    } catch (error) {
      console.error("Erreur lors de la suppression:", error);
      alert("Erreur lors de la suppression de l'utilisateur");
    }
  }
};

const submitForm = async () => {
  loading.value = true;
  try {
    let dataToSend = { ...formData.value };

    if (showEditModal.value && !dataToSend.password) {
      delete dataToSend.password;
      delete dataToSend.password_confirmation;
    }

    if (showCreateModal.value && !dataToSend.password_confirmation) {
      dataToSend.password_confirmation = dataToSend.password;
    }

    if (showCreateModal.value) {
      await api.post("/users", dataToSend);
    } else {
      await api.put(`/users/${editingUser.value.id}`, dataToSend);
    }
    closeModal();
    await fetchUsers();
  } catch (error) {
    console.error("Erreur lors de la soumission:", error);
    console.error("Détails de l'erreur:", error.response?.data);

    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      let errorMessage = "Erreurs de validation:\n";
      for (const field in errors) {
        errorMessage += `- ${field}: ${errors[field].join(", ")}\n`;
      }
      alert(errorMessage);
    } else {
      alert("Erreur lors de la sauvegarde");
    }
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  editingUser.value = null;
  formData.value = {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "user",
    work: "",
  };
};

onMounted(async () => {
  await fetchUsers();
});
</script>
