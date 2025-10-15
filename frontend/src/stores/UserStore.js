import { defineStore } from "pinia";
import api from "@/services/api";
import { toRaw } from "vue";
import axios from "axios";

export const useUserStore = defineStore("users", {
  state: () => ({
    users: [],
    currentUser: null,
    isAuthenticated: false,

    friends: [],
    friendRequests: [],
    sentRequests: [],
    isVerified: false,
    isAdmin: false,
    isLoading: false,
    error: null,
  }),

  getters: {
    //admin
    // isAdmin: (state)=> {return state.currentUser?.role === "admin"},

    currentUserName: (state) => {
      return state.currentUser
        ? `${state.currentUser.firstName} ${state.currentUser.lastName}`
        : "Utilisateur";
    },

    currentUserId: (state) => {
      return state.currentUser ? `${state.currentUser.id}` : null;
    },

    currentUserAvatar: (state) => {
      return state.currentUser?.photo;
    },

    friendsCount: (state) => state.friends.length,

    pendingRequestsCount: (state) => state.friendRequests.length,

    searchFriends: (state) => (searchTerm) => {
      if (!searchTerm) return state.friends;
      const term = searchTerm.toLowerCase();
      return state.friends.filter((friend) => friend.name.toLowerCase().includes(term));
    },
    searchUsers: (state) => (searchTerm) => {
      const friends = toRaw(state.friends);
      const valfriends = Object.values(friends);
      const id = [];
      valfriends.forEach((element) => id.unshift(element["_id"]));

      if (!searchTerm) {
        return state.users
          .filter((user) => !id.includes(user._id))
          .filter((user) => user._id !== state.currentUser.id);
      }
      const term = searchTerm.toLowerCase();
      return state.users
        .filter((user) => user.name.toLowerCase().includes(term))
        .filter((user) => !id.includes(user._id))
        .filter((user) => user._id !== state.currentUser.id);
    },

    isFriend: (state) => (userId) => {
      return state.friends.some((friend) => friend._id === userId);
    },

    hasPendingRequest: (state) => (userId) => {
      return (
        state.friendRequests.some((req) => req.user._id === userId) ||
        state.sentRequests.some((req) => req._id === userId)
      );
    },
  },

  actions: {
    initializeAuth() {
      const token = localStorage.getItem("authToken") || sessionStorage.getItem("authToken");
      if (token) {
        this.isAuthenticated = true;
        return true;
      }
      return false;
    },

    async fetchSentRequests() {
      this.isLoading = true;
      this.error = null;
      try {
        const { data } = await api.get("/friends/sending");
        const data2 = toRaw(data);
        const data3 = Object.values(data2["data"]);
        console.log(data3);
        this.sentRequests = data3.map((req) => ({
          _id: req.receiver.id,
          name: req.receiver.name,
          email: req.receiver.email,
          work: req.receiver.work || "Membre",
          photo: req.receiver.photo,
          telephone: req.receiver.telephone,
          bio: req.receiver.bio,
        }));

        console.log(this.sentRequests);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors du chargement des demandes envoyées:", error);
      } finally {
        this.isLoading = false;
      }
    },

    async fetchUsers() {
      this.isLoading = true;
      this.error = null;
      try {
        const { data } = await api.get("/users");
        this.users = data.data.map((users) => ({
          _id: users.id,
          name: users.name,
          work: users.work || "Membre",
          photo: users.photo,
          email: users.email,
          telephone: users.telephone,
          bio: users.bio,
          stats: {
            posts: users.posts?.length || 0,
            friends: data.count || 0,
            likes: 0,
          },
        }));
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors du chargement des utilisateurs:", error);
      } finally {
        this.isLoading = false;
      }
    },
    setCurrentUser(user) {
      this.currentUser = {
        id: user.id,
        firstName: user.firstName || user.name?.split(" ")[0] || "Utilisateur",
        lastName: user.lastName || user.name?.split(" ")[1] || "",
        email: user.email,
        telephone: user.telephone || "",
        work: user.work || "Membre",
        bio: user.bio || "",
        photo: user.photo,
        role: user.role || "Membre",
        isVerified: user.isVerified,
        isAdmin: user.role === "admin" ? true : false,
      };
      this.isAuthenticated = true;
    },

    async fetchCurrentUser() {
      const token = localStorage.getItem("authToken");
      if (token) {
        await api
          .get("/me")
          .then(({ data }) => {
            this.setCurrentUser({
              id: data.data.id,
              name: data.data.name,
              email: data.data.email,
              telephone: data.data.telephone,
              work: data.data.work,
              bio: data.data.bio,
              photo: data.data.photo,
              dateOfBirth: data.data.dateOfBirth,
              role: data.data.role,
              isVerified: data.data.is_active,
            });
            this.isVerified = data.data.is_active;
            this.isAdmin = data.data.role === "admin" ? true : false;
          })
          .catch((error) => {
            console.error("Erreur chargement user:", error);
            localStorage.removeItem("authToken");
          });
      }
    },

    async logout() {
      if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
        localStorage.removeItem("authToken");
        sessionStorage.removeItem("authToken");
        this.currentUser = null;
        this.isAuthenticated = false;
        this.friends = [];
        this.friendRequests = [];
        this.sentRequests = [];

        window.location.href = "/Signin";
      }
    },

    async updateUser(userData) {
      this.isLoading = true;
      this.error = null;
      try {
        const payload = {
          name: `${userData.firstName} ${userData.lastName}`,
          email: userData.email,
          work: userData.work || "",
          telephone: userData.telephone || "",
          bio: userData.bio || "",
        };
        if (!payload.work || payload.work.trim() === "") {
          delete payload.work;
        }
        if (!payload.telephone || payload.telephone.trim() === "") {
          delete payload.telephone;
        }
        if (!payload.bio || payload.bio.trim() === "") {
          delete payload.bio;
        }

        if (userData.photo && !userData.photo.startsWith("data:")) {
          payload.photo = userData.photo;
        }

        const { data } = await api.put(`/users/${this.currentUser.id}`, payload);

        this.currentUser = {
          ...this.currentUser,
          firstName: data.data.name.split(" ")[0],
          lastName: data.data.name.split(" ")[1] || "",
          email: data.data.email,
          work: data.data.work,
          photo: data.data.photo,
          telephone: data.data.telephone || "",
          bio: data.data.bio || "",
        };
      } catch (error) {
        console.error("Erreur complète:", error.response?.data);
        this.error = error.response?.data?.message || error.message;
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async loadFriends() {
      this.isLoading = true;
      this.error = null;
      try {
        const { data } = await api.get("/friends");
        this.friends = data.data.map((friend) => ({
          _id: friend.id,
          name: friend.name,
          work: friend.work || "Membre",
          photo: friend.photo,
          email: friend.email,
          telephone: friend.telephone,
          bio: friend.bio,
          stats: {
            posts: friend.posts?.length || 0,
            friends: data.data.count || 0,
          },
        }));
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors du chargement des amis:", error);
      } finally {
        this.isLoading = false;
      }
    },

    async loadFriendRequests() {
      this.isLoading = true;
      this.error = null;
      try {
        const { data } = await api.get("/friends/pending");
        this.friendRequests = data.data
          .map((req) => {
            return {
              _id: req.id,
              user: {
                _id: req.id,
                name: req.name,
                work: req.work || "Membre",
                photo: req.photo,
                bio: req.bio,
              },
              sentAt: new Date(req.created_at),
            };
          })
          .filter(Boolean);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors du chargement des demandes:", error);
      } finally {
        this.isLoading = false;
      }
    },

    addFriend(friend) {
      if (!this.isFriend(friend._id)) {
        this.friends.push(friend);
      }
    },

    async removeFriend(friendId) {
      this.isLoading = true;
      this.error = null;
      try {
        await api.delete(`/friends/${friendId}`);
        this.friends = this.friends.filter((f) => f._id !== friendId);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors de la suppression de l'ami:", error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async acceptFriendRequest(requestId) {
      this.isLoading = true;
      this.error = null;
      try {
        const request = this.friendRequests.find((r) => r._id === parseInt(requestId));
        if (request) {
          const { data } = await api.post("/friends/request", { friend_id: parseInt(requestId) });
          this.friendRequests = this.friendRequests.filter((r) => r._id !== parseInt(requestId));
          this.friends.push({ ...request.user, _id: data.data.id });
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors de l'acceptation:", error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async rejectFriendRequest(requestId) {
      this.isLoading = true;
      this.error = null;
      try {
        await api.post(`/friends/reject/${requestId}`);
        this.friendRequests = this.friendRequests.filter((r) => r._id !== requestId);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur lors du refus:", error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async sendFriendRequest(userId) {
      this.isLoading = true;
      this.error = null;
      try {
        const { data } = await api.post("/friends/request", { friend_id: userId });
        this.sentRequests.push(data.data);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        if (
          error.response?.data?.message ===
          "Vous avez déjà envoyé une demande d'ami à cet utilisateur."
        ) {
          alert("Demande d'ami déjà envoyée");
        }
        console.error("Erreur lors de l'envoi de la demande:", error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async sendVerificationEmail() {
      this.isLoading = true;
      this.error = null;

      if (!this.currentUser?.email) {
        alert("Email utilisateur non trouvé. Veuillez vous reconnecter.");
        this.isLoading = false;
        return;
      }

      const payload = { email: this.currentUser.email };

      try {
        const token = localStorage.getItem("authToken") || sessionStorage.getItem("authToken");
        const response = await axios.post(
          "https://api-yowl.coding-academy.website/api/resend/email",
          payload,
          {
            headers: {
              "Content-Type": "application/json",
              Accept: "application/json",
              Authorization: token ? `Bearer ${token}` : "",
            },
          }
        );
        console.log("Réponse envoi email:", response.data);
        alert(
          "Mail de vérification envoyé avec succès ! Veuillez vérifier votre boîte de réception."
        );
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Erreur complète lors de l'envoi du mail:", error.response || error);
        const errorMessage =
          error.response?.data?.message ||
          error.response?.data?.error ||
          "Erreur lors de l'envoi du mail de vérification.";
        alert(`Erreur: ${errorMessage}`);
      } finally {
        this.isLoading = false;
      }
    },

    // async isUserVerified() {
    //   this.isLoading = true;
    //   this.error = null;
    //   try {
    //     const { data } = await api.get("/me");
    //     this.isVerified = data.data.is_active;
    //     return this.isVerified;
    //   } catch (error) {
    //     this.error = error.response?.data?.message || error.message;
    //     console.error("Erreur lors de la vérification de l'utilisateur:", error);
    //     return false;
    //   } finally {
    //     this.isLoading = false;
    //   }
    // },
  },
  persist: {
    key: "users",
    paths: [
      "currentUser",
      "sentRequests",
      "isAuthenticated",
      "friends",
      "friendRequests",
      "sentRequests",
      "isVerified",
    ],
    storage: window.localStorage,
  },
});
