import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import "./assets/main.css";
import { useUserStore } from "./stores/UserStore";
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const app = createApp(App);
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate)

app.use(pinia);
app.use(router);

const token = localStorage.getItem("authToken");
if (token) {
  const userStore = useUserStore();
  userStore.fetchCurrentUser();
}

app.mount("#app");
