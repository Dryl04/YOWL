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
        class="backdrop-blur-xl bg-white/10 rounded-3xl border border-white/20 flex flex-col md:flex-row overflow-hidden motion-safe:animate-[fadeInUp_1s_ease-out]"
      >
        <div class="hidden md:block md:w-1/2 lg:w-[45%]">
          <img
            src="/femmes.jpg"
            alt="deux femmes joyeuses en orange"
            class="h-full w-full object-cover rounded-l-3xl saturate-150 hover:scale-105 transition-transform duration-700 ease-in-out"
          />
        </div>

        <div
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
              Rejoins la communauté !
            </h4>
            <p class="text-gray-600 text-sm sm:text-base md:text-lg px-2">
              Accède à des milliers de commentaires sur différents sujets
            </p>
          </div>

          <form @submit.prevent="Inscription" class="space-y-3 sm:space-y-4">
            <div class="space-y-1 sm:space-y-2">
              <label for="nom" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Nom</label
              >
              <input
                v-model="nom"
                type="text"
                id="nom"
                placeholder="Votre nom"
                class="w-full rounded-xl bg-white/80 backdrop-blur-sm px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 placeholder-gray-500 border border-gray-200/50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-300"
                required
              />
              <p class="text-xs text-gray-500">
                Lettres, espaces, apostrophes et tirets uniquement
              </p>
              <p v-if="errors.nom" class="text-red-500 text-xs sm:text-sm">{{ errors.nom }}</p>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label for="prénom" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Prénom</label
              >
              <input
                v-model="prénom"
                type="text"
                id="prénom"
                placeholder="Votre prénom"
                class="w-full rounded-xl bg-white/80 backdrop-blur-sm px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 placeholder-gray-500 border border-gray-200/50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-300"
                required
              />
              <p class="text-xs text-gray-500">
                Lettres, espaces, apostrophes et tirets uniquement
              </p>
              <p v-if="errors.prénom" class="text-red-500 text-xs sm:text-sm">
                {{ errors.prénom }}
              </p>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label
                for="dateNaissance"
                class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Date de naissance</label
              >
              <input
                v-model="dateNaissance"
                type="date"
                id="dateNaissance"
                class="w-full rounded-xl bg-white/80 backdrop-blur-sm px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 border border-gray-200/50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-300"
                required
              />
              <p class="text-xs text-gray-500">Âge requis : entre 13 et 35 ans</p>
              <p v-if="errors.dateNaissance" class="text-red-500 text-xs sm:text-sm">
                {{ errors.dateNaissance }}
              </p>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label for="telephone" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Téléphone</label
              >
              <input
                v-model="telephone"
                type="tel"
                id="telephone"
                placeholder="+229 01-02-03-04-05"
                class="w-full rounded-xl bg-white/80 backdrop-blur-sm px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 placeholder-gray-500 border border-gray-200/50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-300"
                required
              />
              <p class="text-xs text-gray-500">Format international recommandé</p>
              <p v-if="errors.telephone" class="text-red-500 text-xs sm:text-sm">
                {{ errors.telephone }}
              </p>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label for="email" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Email</label
              >
              <div class="relative">
                <input
                  v-model="email"
                  type="email"
                  id="email"
                  placeholder="unexemple@email.com"
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
              <p class="text-xs text-gray-500">Format email valide requis</p>
              <p v-if="errors.email" class="text-red-500 text-xs sm:text-sm">{{ errors.email }}</p>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label for="password" class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Mot de passe</label
              >
              <div class="relative">
                <input
                  v-model="password"
                  type="password"
                  id="password"
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
              <p class="text-xs text-gray-500">Minimum 8 caractères</p>
              <p v-if="errors.password" class="text-red-500 text-xs sm:text-sm">
                {{ errors.password }}
              </p>
            </div>

            <div class="space-y-1 sm:space-y-2">
              <label
                for="passwordConfirm"
                class="block text-xs sm:text-sm font-semibold text-gray-700"
                >Confirmation du mot de passe</label
              >
              <div class="relative">
                <input
                  v-model="passwordConfirm"
                  type="password"
                  id="passwordConfirm"
                  placeholder="Confirmez votre mot de passe"
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
              <p class="text-xs text-gray-500">Doit correspondre au mot de passe</p>
              <p v-if="errors.passwordConfirm" class="text-red-500 text-xs sm:text-sm">
                {{ errors.passwordConfirm }}
              </p>
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
                Création en cours...
              </span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                  ></path>
                </svg>
                Créer un compte
              </span>
            </button>

            <p class="text-xs sm:text-sm text-gray-600 text-center">
              Vous avez déjà un compte ?
              <router-link
                to="/SignIn"
                class="text-orange-600 font-semibold hover:text-orange-700 hover:underline transition-colors"
              >
                Se connecter ici
              </router-link>
            </p>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "@/services/api";
import router from "@/router";
import { useUserStore } from "@/stores/UserStore";

const userStore = useUserStore();
const nom = ref("");
const prénom = ref("");
const email = ref("");
const password = ref("");
const passwordConfirm = ref("");
const dateNaissance = ref("");
const telephone = ref("");

const errors = ref({});
const loading = ref(false);

function validateNom(nom) {
  const regex = /^[a-zA-ZÀ-ÿ\s'-]+$/;
  return regex.test(nom);
}

function validatePrénom(prénom) {
  const regex = /^[a-zA-ZÀ-ÿ\s'-]+$/;
  return regex.test(prénom);
}

function sanitizeInput(input) {
  return input.replace(/[<>"'`;(){}]/g, "");
}

function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

async function Inscription() {
  errors.value = {};
  loading.value = true;

  const cleanNom = sanitizeInput(nom.value.trim());
  const cleanPrénom = sanitizeInput(prénom.value.trim());
  const cleanEmail = sanitizeInput(email.value.trim());
  const cleanPassword = sanitizeInput(password.value.trim());
  //const cleanPhone = sanitizeInput(telephone.value.trim());

  // Validation
  if (!validateEmail(cleanEmail)) {
    errors.value.email = "Email invalide.";
  }

  if (cleanPassword.length < 8) {
    errors.value.password = "Le mot de passe doit contenir au moins 8 caractères.";
  }

  if (cleanPassword !== passwordConfirm.value) {
    errors.value.passwordConfirm = "Les mots de passe ne correspondent pas.";
  }

  if (!validateNom(cleanNom)) {
    errors.value.nom = "Nom invalide.";
  }

  if (!validatePrénom(cleanPrénom)) {
    errors.value.prénom = "Prénom invalide.";
  }

  const birth = new Date(dateNaissance.value);
  const today = new Date();
  let age = today.getFullYear() - birth.getFullYear();
  if (today < new Date(birth.setFullYear(today.getFullYear()))) {
    age--;
  }

  if (age < 13 || age > 35 || age == 0) {
    errors.value.dateNaissance = "Vous ne remplissez pas les conditions pour accéder à ce site.";
  }

  if (Object.keys(errors.value).length > 0) {
    loading.value = false;
    return;
  }

  try {
    const response = await api.post("/register", {
      name: `${cleanPrénom} ${cleanNom}`,
      email: cleanEmail,
      password: cleanPassword,
      password_confirmation: cleanPassword,
      dateOfBirth: dateNaissance.value,
      telephone: telephone.value,
      work: "Membre YOWL",
      photo:
        "https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg",
    });

    if (response.data?.success && response.data?.data?.token) {
      localStorage.setItem("authToken", response.data.data.token);
      userStore.isAuthenticated = true;
      router.push("/");
    } else {
      throw new Error("Réponse du serveur invalide.");
    }
  } catch (error) {
    console.error("Erreur d'inscription :", error);

    if (error.response) {
      const status = error.response.status;
      if (status === 409) {
        errors.value.email = "Cet email est déjà utilisé.";
      } else if (status === 422) {
        // Erreurs de validation Laravel
        const validationErrors = error.response.data?.errors;
        if (validationErrors) {
          if (validationErrors.email) errors.value.email = validationErrors.email[0];
          if (validationErrors.password) errors.value.password = validationErrors.password[0];
          if (validationErrors.dateOfBirth)
            errors.value.dateNaissance = validationErrors.dateOfBirth[0];
        } else {
          errors.value.general = error.response.data?.message || "Données invalides.";
        }
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
