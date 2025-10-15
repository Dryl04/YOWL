<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
      <div
        class="hidden lg:block w-64 bg-white border-r border-gray-200 h-screen fixed left-0 top-0 z-20"
      >
        <SideBarLeft @logout="handlelogout" />
      </div>

      <div class="fixed top-0 left-0 lg:left-64 right-0 z-40">
        <Header />
      </div>

      <main class="flex-1 pt-16 sm:pt-20 pb-20 lg:pb-0 overflow-y-auto lg:ml-64">
        <div class="max-w-6xl mx-auto py-2 sm:py-4 px-2 sm:px-4">
          <div class="overflow-hidden w-full">
            <div class="p-3 sm:p-4 border-b border-gray-200">
              <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Profil</h1>
            </div>

            <div class="flex flex-col lg:flex-row">
              <div class="flex-1 p-3 sm:p-4 md:p-6">
                <div v-if="activeTab === 'profile'" class="space-y-4 sm:space-y-6">
                  <div>
                    <div class="mb-4 sm:mb-6">
                      <div
                        class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4"
                      >
                        <img
                          :src="avatarPreview || userStore.currentUserAvatar"
                          alt="Avatar"
                          class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover"
                        />
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                          <label
                            class="px-3 sm:px-4 py-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition font-medium text-xs sm:text-sm hover:cursor-pointer text-center"
                          >
                            Changer la photo
                            <input
                              type="file"
                              accept="image/*"
                              @change="handleAvatarUpload"
                              class="hidden"
                            />
                          </label>
                          <button
                            v-if="avatarPreview"
                            @click="removeAvatar"
                            class="px-3 sm:px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition font-medium text-xs sm:text-sm"
                          >
                            Annuler
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                      <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2"
                          >Prénom</label
                        >
                        <input
                          v-model="editedProfile.firstName"
                          type="text"
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-none"
                        />
                      </div>

                      <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2"
                          >Nom</label
                        >
                        <input
                          v-model="editedProfile.lastName"
                          type="text"
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-none"
                        />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2"
                          >Email</label
                        >
                        <input
                          v-model="editedProfile.email"
                          type="email"
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-none"
                        />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2"
                          >Téléphone</label
                        >
                        <input
                          v-model="editedProfile.telephone"
                          type="tel"
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-none"
                        />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2"
                          >Profession</label
                        >
                        <input
                          v-model="editedProfile.work"
                          type="text"
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-none"
                        />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2"
                          >Biographie</label
                        >
                        <textarea
                          v-model="editedProfile.bio"
                          rows="4"
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none focus:border-none"
                          placeholder="Parlez-nous de vous..."
                        ></textarea>
                      </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-6">
                      <button
                        @click="saveProfile"
                        class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:shadow-lg transition font-medium hover:cursor-pointer text-sm sm:text-base"
                      >
                        <span v-if="isSaving" class="flex items-center justify-center gap-2">
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
                          Enregistrement en cours...
                        </span>
                        <span v-else> Enregistrer les modifications </span>
                      </button>
                      <button
                        @click="cancelProfileEdit"
                        class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium hover:cursor-pointer text-sm sm:text-base"
                      >
                        Annuler
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <MobileNavigation />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import SideBarLeft from "@/components/SidebarLeft.vue";
import Header from "@/components/HeaderHome.vue";
import MobileNavigation from "@/components/MobileNavigation.vue";
import { useUserStore } from "@/stores/UserStore";
import { uploadImage } from "@/services/imageUpload";

const isSaving = ref(false);

const userStore = useUserStore();

const activeTab = ref("profile");
const avatarPreview = ref(null);
const avatarFile = ref(null);

const editedProfile = reactive({
  firstName: userStore.currentUser?.firstName || "",
  lastName: userStore.currentUser?.lastName || "",
  email: userStore.currentUser?.email || "",
  telephone: userStore.currentUser?.telephone || "",
  bio: userStore.currentUser?.bio || "",
  work: userStore.currentUser?.work || "",
});

function handlelogout() {
  userStore.logout();
}

onMounted(async () => {
  await userStore.fetchCurrentUser();
});

function handleAvatarUpload(event) {
  const file = event.target.files[0];
  if (file) {
    avatarFile.value = file;

    const reader = new FileReader();
    reader.onload = (e) => {
      avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function removeAvatar() {
  avatarPreview.value = null;
  avatarFile.value = null;
}

async function saveProfile() {
  isSaving.value = true;

  try {
    const updates = { ...editedProfile };
    if (updates.bio === null || updates.bio === undefined) {
      updates.bio = "";
    }
    if (updates.telephone === null || updates.telephone === undefined) {
      updates.telephone = "";
    }
    if (updates.work === null || updates.work === undefined) {
      updates.work = "";
    }

    updates.bio = String(updates.bio || "");
    updates.telephone = String(updates.telephone || "");
    updates.work = String(updates.work || "");

    if (avatarFile.value) {
      updates.photo = await uploadImage(avatarFile.value);
    }

    await userStore.updateUser(updates);

    avatarPreview.value = null;
    avatarFile.value = null;
    alert("Profil mis à jour avec succès !");
  } catch (error) {
    console.error("Erreur mise à jour:", error);
    alert("Erreur lors de la mise à jour du profil");
  } finally {
    isSaving.value = false;
  }
}

function cancelProfileEdit() {
  editedProfile.firstName = userStore.currentUser?.firstName || "";
  editedProfile.lastName = userStore.currentUser?.lastName || "";
  editedProfile.email = userStore.currentUser?.email || "";
  editedProfile.telephone = userStore.currentUser?.telephone || "";
  editedProfile.bio = userStore.currentUser?.bio || "";
  editedProfile.work = userStore.currentUser?.work || "";
  removeAvatar();
}
</script>
