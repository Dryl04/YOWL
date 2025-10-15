<template>
  <div class="relative min-h-screen flex items-center justify-center bg-green-200 overflow-hidden">

    <div
      class="absolute -top-40 -left-40 w-96 h-96 bg-white/40 rounded-full blur-3xl animate-[pulse_8s_ease-in-out_infinite]">
    </div>
    <div
      class="absolute top-20 right-20 w-72 h-72 bg-white/40 rounded-full blur-3xl animate-[spin_20s_linear_infinite]">
    </div>
    <div
      class="absolute bottom-10 left-2/3 w-100 h-100 bg-white/40 rounded-full blur-3xl animate-[bounce_10s_infinite]">
    </div>

    <section class="relative w-full max-w-6xl p-1">
      <div
        class="backdrop-blur-2xl bg-white/10 rounded-3xl shadow-2xl border border-white/20 flex flex-col md:flex-row overflow-hidden motion-safe:animate-[fadeInUp_1s_ease-out]">

        <div class="hidden md:block md:w-1/2">
          <img src="/jeune.jpg" alt="jeune homme en tenue orange"
            class="h-full w-full object-cover rounded-l-3xl saturate-150 hover:scale-105 transition-transform duration-700 ease-in-out">
        </div>

        <section class="flex-1 p-10 flex flex-col justify-center bg-orange-100 blur-1xl">
          <h2 class="text-4xl font-bold tracking-tight text-orange-900 sm:text-5xl mb-25">YOWL</h2>

          <div class="text-center mb-10">
            <h2 class="text-3xl sm:text-3xl font-semibold text-orange-900"> Réinitialiser le mot de passe </h2>
            <p class="mt-10 text-lg font-semibold text-orange-900"> Entrez votre adresse email et votre nouveau mot de
              passe
            </p>
          </div>

          <form @submit.prevent="sendLink" class="space-y-7">

            <div>
              <label for="resetEmail" class="block text-md font-bold text-orange-900">Email</label>
              <input v-model="resetEmail" type="email" id="resetEmail"
                class="mt-2 block w-full rounded-xl bg-white/60 px-4 py-3 text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition"
                placeholder="exemple@email.com" required />
            </div>


            <button type="submit" :disabled="loading"
              class="w-full rounded-xl bg-gradient-to-r from-yellow-300 via-red-500 to-yellow-300 px-5 py-3 text-white font-semibold shadow-lg hover:opacity-90 focus:ring-2 focus:ring-indigo-300 transform hover:scale-[1.03] transition duration-300">
              <span v-if="loading">Réinitialisation en cours...</span>
              <span v-else>Réinitialiser le mot de passe</span>
            </button>

            <p class="text-sm text-gray-700 text-center mt-4">
              Vous avez déjà un compte ?
              <router-link to="/SignIn" class="text-green-600 font-medium hover:underline">Se connecter
                ici</router-link>
            </p>

            <div v-if="successMessage" class="text-center text-green-700 font-semibold">{{ successMessage }}</div>

            <div v-if="errors.general" class="text-center text-pink-600">{{ errors.general }}</div>

          </form>
        </section>
      </div>

    </section>
  </div>
</template>


<script setup>
import { ref } from "vue";
import api from "@/services/api";
import { useRouter, useRoute } from 'vue-router';
import axios from "axios";

const resetEmail = ref("");
const newPassword = ref("");
const passwordConfirm = ref("");
const errors = ref({});
const loading = ref(false);
const successMessage = ref("");

const router = useRouter();
const route = useRoute();

function sanitizeInput(input) {
  return input.replace(/[<>"'`;(){}]/g, "");
}

async function sendLink() {
  const response = await axios.post("https://api-yowl.coding-academy.website/api/v1/password/reset", {
    "email": resetEmail.value
  });
  /*const response = await axios.post("http://127.0.0.1:8000/api/v1/password/reset", {
    "email": resetEmail.value
  });*/
  console.log(response.data);


}
</script>
