class YowlPopup {
  constructor() {
    this.isLoggedIn = false;
    this.user = null;
    this.settings = {
      extensionEnabled: true,
    };

    this.init();
  }

  init() {
    // console.log('initialisation de la popup YOWL');

    this.setupEventListeners();
    this.loadUserInfo();
    this.loadSettings();
    this.updateUI();
  }

  setupEventListeners() {
    // boutons de connexion
    const loginBtn = document.getElementById("login-btn");
    if (loginBtn) {
      loginBtn.addEventListener("click", () => this.handleLogin());
    }

    const registerBtn = document.getElementById("register-btn");
    if (registerBtn) {
      registerBtn.addEventListener("click", () => this.handleRegister());
    }

    // boutons pour basculer entre connexion et inscription
    const showRegisterBtn = document.getElementById("show-register-btn");
    if (showRegisterBtn) {
      showRegisterBtn.addEventListener("click", () => this.showRegisterForm());
    }

    const showLoginBtn = document.getElementById("show-login-btn");
    if (showLoginBtn) {
      showLoginBtn.addEventListener("click", () => this.showLoginForm());
    }

    // bouton de déconnexion
    const logoutBtn = document.getElementById("logout-btn");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", () => this.handleLogout());
    }

    // toggles
    const extensionToggle = document.getElementById("extension-toggle");
    if (extensionToggle) {
      extensionToggle.addEventListener("click", () => this.toggleExtension());
    }

    // entrée sur les champs de formulaire
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    if (emailInput) {
      emailInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") this.handleLogin();
      });
    }

    if (passwordInput) {
      passwordInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") this.handleLogin();
      });
    }
  }

  async loadUserInfo() {
    try {
      const response = await this.sendMessage({ action: "getUserInfo" });
      if (response.ok) {
        this.isLoggedIn = response.isLoggedIn;
        this.user = response.user;
        this.updateUI();
      }
    } catch (error) {
      console.error("Erreur lors du chargement des infos utilisateur:", error);
    }
  }

  async loadSettings() {
    try {
      const result = await chrome.storage.local.get(["extensionEnabled"]);
      this.settings = {
        extensionEnabled: result.extensionEnabled !== false, // Par défaut true
      };
      this.updateToggles();
    } catch (error) {
      console.error("Erreur lors du chargement des paramètres:", error);
    }
  }

  updateUI() {
    const userSection = document.getElementById("user-section");
    const loginSection = document.getElementById("login-section");

    if (this.isLoggedIn && this.user) {
      userSection.classList.remove("hidden");
      loginSection.classList.add("hidden");

      document.getElementById("user-name").textContent =
        this.user.name || this.user.email;
      document.getElementById("user-email").textContent = this.user.email;
    } else {
      userSection.classList.add("hidden");
      loginSection.classList.remove("hidden");
    }
  }

  updateToggles() {
    const extensionToggle = document.getElementById("extension-toggle");

    if (extensionToggle) {
      extensionToggle.classList.toggle(
        "active",
        this.settings.extensionEnabled
      );
    }
  }

  async handleLogin() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;

    if (!email || !password) {
      this.showStatus("Veuillez remplir tous les champs", "error");
      return;
    }

    try {
      this.showStatus("Connexion en cours...", "success");

      const response = await this.sendMessage({
        action: "login",
        credentials: { email, password },
      });

      if (response.ok) {
        this.isLoggedIn = true;
        this.user = response.data.user;
        this.updateUI();
        this.showStatus("Connexion réussie !", "success");
        document.getElementById("email").value = "";
        document.getElementById("password").value = "";
      } else {
        this.showStatus("Erreur de connexion: " + response.error, "error");
      }
    } catch (error) {
      console.error("Erreur lors de la connexion:", error);
      this.showStatus("Erreur de connexion", "error");
    }
  }

  showRegisterForm() {
    document.getElementById("login-form").classList.add("hidden");
    document.getElementById("register-form").classList.remove("hidden");
    document.getElementById("form-title").textContent = "Inscription";
  }

  showLoginForm() {
    document.getElementById("register-form").classList.add("hidden");
    document.getElementById("login-form").classList.remove("hidden");
    document.getElementById("form-title").textContent = "Connexion";
  }

  async handleRegister() {
    const name = document.getElementById("register-name").value.trim();
    const email = document.getElementById("register-email").value.trim();
    const password = document.getElementById("register-password").value;
    const passwordConfirm = document.getElementById(
      "register-password-confirm"
    ).value;
    const dateOfBirth = document.getElementById("register-birthdate").value;

    if (!name || !email || !password || !passwordConfirm || !dateOfBirth) {
      this.showStatus("Veuillez remplir tous les champs obligatoires", "error");
      return;
    }

    if (password !== passwordConfirm) {
      this.showStatus("Les mots de passe ne correspondent pas", "error");
      return;
    }

    if (password.length < 8) {
      this.showStatus(
        "Le mot de passe doit contenir au moins 8 caractères",
        "error"
      );
      return;
    }

    const birthDate = new Date(dateOfBirth);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();

    if (age < 13 || age > 35) {
      this.showStatus(
        "Vous devez avoir entre 13 et 35 ans pour utiliser YOWL",
        "error"
      );
      return;
    }

    try {
      this.showStatus("Création du compte...", "success");

      const credentials = {
        name,
        email,
        password,
        password_confirmation: passwordConfirm,
        dateOfBirth,
      };

      const response = await this.sendMessage({
        action: "register",
        credentials,
      });

      if (response.ok) {
        this.isLoggedIn = true;
        this.user = response.data.user;
        this.updateUI();
        this.showStatus("Inscription réussie !", "success");

        this.clearRegisterForm();
      } else {
        this.showStatus("Erreur d'inscription: " + response.error, "error");
      }
    } catch (error) {
      console.error("Erreur lors de l'inscription:", error);
      this.showStatus("Erreur lors de l'inscription", "error");
    }
  }

  clearRegisterForm() {
    document.getElementById("register-name").value = "";
    document.getElementById("register-email").value = "";
    document.getElementById("register-password").value = "";
    document.getElementById("register-password-confirm").value = "";
    document.getElementById("register-birthdate").value = "";
  }

  async handleLogout() {
    try {
      const response = await this.sendMessage({ action: "logout" });
      if (response.ok) {
        this.isLoggedIn = false;
        this.user = null;
        this.updateUI();
        this.showStatus("Déconnexion réussie", "success");
      }
    } catch (error) {
      console.error("Erreur lors de la déconnexion:", error);
      this.showStatus("Erreur lors de la déconnexion", "error");
    }
  }

  async toggleExtension() {
    this.settings.extensionEnabled = !this.settings.extensionEnabled;
    await this.saveSettings();
    this.updateToggles();

    const status = this.settings.extensionEnabled ? "activée" : "désactivée";
    this.showStatus(`Extension ${status}`, "success");

    // Notifier le content script du changement
    try {
      const tabs = await chrome.tabs.query({
        active: true,
        currentWindow: true,
      });
      if (tabs[0]) {
        chrome.tabs.sendMessage(tabs[0].id, {
          action: "toggleExtension",
          enabled: this.settings.extensionEnabled,
        });
      }
    } catch (error) {
      console.error("Erreur lors de la notification du content script:", error);
    }
  }

  async saveSettings() {
    try {
      await chrome.storage.local.set({
        extensionEnabled: this.settings.extensionEnabled,
      });
    } catch (error) {
      console.error("Erreur lors de la sauvegarde des paramètres:", error);
    }
  }

  showStatus(message, type = "success") {
    const statusElement = document.getElementById("status-message");
    if (statusElement) {
      statusElement.textContent = message;
      statusElement.className = `status ${type}`;
      statusElement.classList.remove("hidden");

      setTimeout(() => {
        statusElement.classList.add("hidden");
      }, 3000);
    }
  }

  sendMessage(message) {
    return new Promise((resolve) => {
      chrome.runtime.sendMessage(message, (response) => {
        resolve(response || { ok: false, error: "Pas de réponse" });
      });
    });
  }
}

// initialiser la popup quand le DOM est prêt
document.addEventListener("DOMContentLoaded", () => {
  new YowlPopup();
});
