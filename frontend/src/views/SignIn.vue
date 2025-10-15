<template>
  <div
    class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-green-200 via-blue-100 to-purple-200 overflow-hidden"
  >
    <div
      class="absolute -top-40 -left-40 w-64 h-64 sm:w-96 sm:h-96 bg-gradient-to-r from-yellow-300/30 to-orange-400/30 rounded-full blur-3xl animate-[pulse_8s_ease-in-out_infinite]"
    ></div>
    <div
      class="absolute top-20 -right-20 sm:right-20 w-48 h-48 sm:w-72 sm:h-72 bg-gradient-to-r from-pink-300/30 to-red-400/30 rounded-full blur-3xl animate-[spin_20s_linear_infinite]"
    ></div>
    <div
      class="absolute bottom-10 left-1/2 sm:left-2/3 w-64 h-64 sm:w-100 sm:h-100 bg-gradient-to-r from-blue-300/30 to-purple-400/30 rounded-full blur-3xl animate-[bounce_10s_infinite]"
    ></div>
    <div
      class="absolute top-1/2 left-1/4 w-48 h-48 sm:w-64 sm:h-64 bg-gradient-to-r from-green-300/20 to-teal-400/20 rounded-full blur-2xl animate-[float_6s_ease-in-out_infinite]"
    ></div>

    <section class="relative w-full max-w-4xl p-2 sm:p-4">
      <div
        class="backdrop-blur-2xl bg-white/10 rounded-3xl border border-white/20 flex flex-col md:flex-row overflow-hidden motion-safe:animate-[fadeInUp_1s_ease-out]"
      >
        <div class="hidden md:block md:w-1/2">
          <img
            src="/jeune.jpg"
            alt="jeune homme en tenue orange"
            class="h-full w-full object-cover rounded-l-3xl saturate-150 hover:scale-105 transition-transform duration-700 ease-in-out"
          />
        </div>

        <section
          class="flex-1 p-4 sm:p-6 md:p-8 lg:p-12 flex flex-col justify-center bg-gradient-to-br from-orange-50/90 to-yellow-50/90 backdrop-blur-sm"
        >
          <div class="text-center mb-4 sm:mb-6 md:mb-8">
            <h2
              class="text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-2 sm:mb-4"
            >
              YOWL
            </h2>
          </div>

          <div class="text-center mb-4 sm:mb-6 md:mb-8">
            <h4 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2 sm:mb-3">
              Bienvenue !
            </h4>
            <p class="text-gray-600 text-sm sm:text-base md:text-lg px-2">
              Rejoins la communauté pour partager ton vibe
            </p>
          </div>

          <form @submit.prevent="SeConnecter" method="POST" class="space-y-4 sm:space-y-6">
            <div class="space-y-1 sm:space-y-2">
              <label for="email" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Email</label
              >
              <div class="relative">
                <input
                  v-model="email"
                  type="email"
                  name="email"
                  id="email"
                  autocomplete="email"
                  placeholder="Votre email"
                  class="w-full rounded-xl bg-white/80 backdrop-blur-sm px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 placeholder-gray-500 border border-gray-200/50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-300"
                  required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg
                    class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                    ></path>
                  </svg>
                </div>
              </div>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label for="password" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Mot de passe</label
              >
              <div class="relative">
                <input
                  v-model="password"
                  type="password"
                  name="password"
                  id="password"
                  autocomplete="current-password"
                  placeholder="Votre mot de passe"
                  class="w-full rounded-xl bg-white/80 backdrop-blur-sm px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 placeholder-gray-500 border border-gray-200/50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-300"
                  required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg
                    class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                    ></path>
                  </svg>
                </div>
              </div>
            </div>

            <div
              class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0"
            >
              <div class="flex items-center gap-2 sm:gap-3">
                <input
                  id="remember"
                  v-model="remember"
                  name="remember"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 focus:ring-2"
                />
                <label for="remember" class="text-xs sm:text-sm text-gray-600 font-medium"
                  >Se souvenir de moi</label
                >
              </div>
              <router-link
                to="/resetPassword"
                class="text-xs sm:text-sm text-orange-600 font-medium hover:text-orange-700 hover:underline transition-colors"
              >
                Mot de passe oublié ?
              </router-link>
            </div>

            <div
              v-if="errors.general"
              class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-600 text-xs sm:text-sm text-center"
            >
              {{ errors.general }}
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base text-white font-semibold focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center justify-center gap-2">
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
                Connexion en cours...
              </span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                  ></path>
                </svg>
                Se connecter
              </span>
            </button>

            <p class="text-xs sm:text-sm text-gray-600 text-center">
              Vous n'avez pas de compte ?
              <router-link
                to="SignUp"
                class="text-orange-600 font-semibold hover:text-orange-700 hover:underline transition-colors"
              >
                Créer un compte ici
              </router-link>
            </p>
          </form>
        </section>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import router from "@/router";

import { useUserStore } from "@/stores/UserStore";
const email = ref("");
const userStore = useUserStore();
const password = ref("");
const remember = ref(false);
const errors = ref({});
const loading = ref(false);

function sanitizeInput(input) {
  return input.replace(/[<>"'`;(){}]/g, "");
}

function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

async function SeConnecter() {
  errors.value = {};
  loading.value = true;

  const cleanEmail = sanitizeInput(email.value.trim());
  const cleanPassword = sanitizeInput(password.value.trim());

  if (!validateEmail(cleanEmail)) {
    errors.value.email = "Email invalide.";
  }
  if (cleanPassword.length < 8) {
    errors.value.password = "Le mot de passe doit contenir au moins 8 caractères.";
  }

  if (Object.keys(errors.value).length > 0) {
    loading.value = false;
    return;
  }

  try {
    const response = await api.post("/login", {
      email: cleanEmail,
      password: cleanPassword,
    });

    if (response.data?.success && response.data?.data?.token) {
      if (remember.value) {
        localStorage.setItem("authToken", response.data.data.token);
        localStorage.setItem("rememberEmail", cleanEmail);
        localStorage.setItem("rememberChecked", true);
      } else {
        localStorage.setItem("authToken", response.data.data.token);
        localStorage.removeItem("rememberEmail");
        localStorage.removeItem("rememberChecked");
      }

      userStore.isAuthenticated = true;
      router.push("/");
    } else if (response.data?.message) {
      errors.value.general = response.data.message;
    } else {
      throw new Error("Réponse du serveur invalide.");
    }
  } catch (error) {
    console.error("Erreur de connexion :", error);

    if (error.response) {
      const status = error.response.status;
      if (status === 401) {
        errors.value.general = "Email ou mot de passe incorrect.";
      } else if (status === 404) {
        errors.value.general = "Compte introuvable. Veuillez vous inscrire.";
      } else {
        errors.value.general = error.response.data?.message || "Erreur serveur.";
      }
    } else {
      errors.value.general = "Impossible de se connecter au serveur.";
    }
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  const savedEmail = localStorage.getItem("rememberEmail");
  const wasChecked = localStorage.getItem("rememberChecked");

  if (savedEmail && wasChecked) {
    email.value = savedEmail;
    remember.value = true;
  }
});
</script>

<style scoped>
@keyframes float {
  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(5deg);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.motion-safe\:animate-\[fadeInUp_1s_ease-out\] {
  animation: fadeInUp 1s ease-out;
}
</style>
