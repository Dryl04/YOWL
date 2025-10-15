<template>
  <article class="bg-white rounded-2xl border border-gray-100 p-3 sm:p-4 md:p-6">
    <div class="flex items-start justify-between mb-3 sm:mb-4">
      <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
        <img
          :src="
            post.author.photo ||
            'https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg'
          "
          alt="Avatar"
          class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover flex-shrink-0"
        />
        <div class="min-w-0 flex-1">
          <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">
            {{ post.author.name }}
          </h3>
          <p class="text-xs sm:text-sm text-gray-500">{{ formattedDate }}</p>
        </div>
      </div>
      <div class="relative flex-shrink-0">
        <button
          @click="showMenu = !showMenu"
          class="text-gray-400 hover:text-gray-600 hover:cursor-pointer p-1"
        >
          <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
            <path
              d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
            ></path>
          </svg>
        </button>

        <div
          v-if="showMenu"
          class="absolute right-0 mt-2 w-44 sm:w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10"
        >
          <button
            v-if="isOwnPost"
            @click="editPost"
            class="w-full text-left px-3 sm:px-4 py-2 hover:bg-gray-50 text-xs sm:text-sm flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              ></path>
            </svg>
            Modifier
          </button>
          <button
            v-if="isOwnPost"
            @click="deletePost"
            class="w-full text-left px-3 sm:px-4 py-2 hover:bg-gray-50 text-xs sm:text-sm text-red-600 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              ></path>
            </svg>
            Supprimer
          </button>
          <button
            @click="reportPost"
            class="w-full text-left px-3 sm:px-4 py-2 hover:bg-gray-50 text-xs sm:text-sm flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"
              ></path>
            </svg>
            Signaler
          </button>
        </div>
      </div>
    </div>

    <p class="text-gray-700 mb-3 sm:mb-4 leading-relaxed text-sm sm:text-base break-words">
      {{ post.content.description }}
    </p>

    <p
      v-if="post.link"
      class="text-blue-500 mb-3 sm:mb-4 leading-relaxed font-semibold hover:underline hover:cursor-pointer text-sm sm:text-base break-all"
    >
      <span class="text-black">URL : </span>
      <a :href="post.link" target="_blank" rel="noopener noreferrer">{{ post.link }}</a>
    </p>

    <div v-if="post.content.image" class="rounded-xl overflow-hidden mb-3 sm:mb-4">
      <img
        :src="post.content.image"
        alt="Post image"
        class="w-full h-48 sm:h-64 md:h-80 object-cover"
      />
    </div>

    <div
      class="flex flex-wrap items-center justify-between pt-3 sm:pt-4 border-t border-gray-100 gap-2"
    >
      <button
        @click="toggleLike"
        :class="[
          'flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg transition-all duration-200 hover:cursor-pointer',
          isLiked ? 'text-red-500 bg-red-50' : 'text-gray-500 hover:text-red-500 hover:bg-gray-50',
        ]"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          :fill="isLiked ? 'currentColor' : 'none'"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-5 h-5 sm:w-6 sm:h-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
          />
        </svg>
        <span class="text-xs sm:text-sm font-medium">{{ post.likes }}</span>
      </button>

      <button
        @click="showComments = !showComments"
        class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-gray-50 transition-all duration-200 hover:cursor-pointer"
      >
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
          ></path>
        </svg>
        <span class="text-xs sm:text-sm font-medium">{{ post.comments.length }}</span>
      </button>

      <button
        @click="sharePost"
        class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-gray-50 transition-all duration-200 hover:cursor-pointer"
      >
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
          ></path>
        </svg>
        <span class="text-xs sm:text-sm font-medium">{{ post.shares }}</span>
      </button>

      <button
        @click="toggleBookmark"
        :class="[
          'px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg transition-all duration-200 hover:cursor-pointer',
          isBookmarked
            ? 'text-purple-600 bg-purple-50'
            : 'text-gray-400 hover:text-purple-600 hover:bg-gray-50',
        ]"
      >
        <svg
          class="w-4 h-4 sm:w-5 sm:h-5"
          :fill="isBookmarked ? 'currentColor' : 'none'"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
          ></path>
        </svg>
      </button>
    </div>

    <!-- Commentaires -->
    <div v-if="showComments" class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100">
      <div v-if="post.comments.length > 0" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
        <div v-for="comment in post.comments" :key="comment.id" class="flex gap-2 sm:gap-3">
          <img
            :src="
              comment.user?.photo ||
              'https://static.vecteezy.com/system/resources/previews/046/010/545/non_2x/user-icon-simple-design-free-vector.jpg'
            "
            alt="Avatar"
            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover flex-shrink-0"
          />
          <div class="flex-1 bg-gray-50 rounded-lg px-3 sm:px-4 py-2 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-1">
              <p class="font-semibold text-xs sm:text-sm truncate">
                {{ comment.user?.name || "Utilisateur" }}
              </p>
              <span class="text-xs text-gray-500">{{ formatCommentDate(comment.created_at) }}</span>
            </div>
            <p class="text-xs sm:text-sm text-gray-700 break-words">{{ comment.contenu }}</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleCommentPost">
        <div class="flex items-center gap-2 sm:gap-3">
          <img
            :src="UserStore.currentUserAvatar"
            alt="Avatar"
            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover flex-shrink-0"
          />
          <div class="flex-1 relative">
            <input
              type="text"
              v-model="comment"
              placeholder="Ajouter un commentaire..."
              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 pr-20 sm:pr-24 rounded-full bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500 text-xs sm:text-sm"
            />
            <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-3 sm:gap-5">
              <button
                type="button"
                @click="showEmojiPicker = !showEmojiPicker"
                class="text-gray-400 hover:text-purple-600 hover:cursor-pointer"
              >
                <svg
                  class="w-4 h-4 sm:w-5 sm:h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  ></path>
                </svg>
              </button>

              <button
                type="submit"
                class="text-purple-600 hover:text-purple-700 hover:cursor-pointer"
              >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Emoji -->
        <div v-if="showEmojiPicker" class="mt-2 p-3 bg-gray-50 rounded-lg flex gap-2 flex-wrap">
          <button
            v-for="emoji in emojis"
            :key="emoji"
            type="button"
            @click="addEmoji(emoji)"
            class="text-xl hover:scale-125 transition"
          >
            {{ emoji }}
          </button>
        </div>
      </form>
    </div>

    <!-- Modale d'édition de post -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50"
      @click.self="showEditModal = false"
    >
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold">Modifier le post</h2>
          <button
            @click="showEditModal = false"
            class="text-gray-400 hover:text-gray-600 hover:cursor-pointer"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>

        <textarea
          v-model="editedDescription"
          rows="5"
          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none mb-3"
        ></textarea>

        <input
          v-model="editedLink"
          type="url"
          placeholder="Lien"
          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 mb-4"
        />

        <!-- Gestion d'image -->
        <div v-if="editedImagePreview" class="mb-4 relative">
          <img :src="editedImagePreview" class="w-full rounded-lg max-h-80 object-cover" />
          <button
            @click="removeEditImage"
            type="button"
            class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 hover:cursor-pointer transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>

        <label
          class="flex items-center gap-2 text-gray-600 hover:text-orange-600 cursor-pointer mb-4"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            ></path>
          </svg>
          <span class="text-sm font-medium">{{
            editedImagePreview ? "Changer l'image" : "Ajouter une image"
          }}</span>
          <input type="file" accept="image/*" @change="handleEditImageUpload" class="hidden" />
        </label>

        <div class="flex justify-end gap-3">
          <button
            @click="showEditModal = false"
            :disabled="isUpdating"
            class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Annuler
          </button>
          <button
            @click="saveEdit"
            :disabled="isUpdating || !editedDescription.trim() || !editedLink.trim()"
            :class="[
              'px-6 py-2.5 rounded-lg transition font-medium',
              isUpdating || !editedDescription.trim() || !editedLink.trim()
                ? 'bg-gradient-to-r from-orange-300 to-red-300 text-white cursor-not-allowed'
                : 'bg-gradient-to-r from-orange-500 to-red-500 text-white hover:shadow-lg hover:cursor-pointer'
            ]"
          >
            <span v-if="isUpdating" class="flex items-center justify-center gap-2">
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
              Modification en cours...
            </span>
            <span v-else>Enregistrer</span>
          </button>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { ref, computed, toRaw, onMounted } from "vue";
import { usePostStore } from "@/stores/PostsStore";
import { useUserStore } from "@/stores/UserStore";
import { uploadImage } from "@/services/imageUpload";

const PostStore = usePostStore();
const UserStore = useUserStore();

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const comment = ref("");
const showMenu = ref(false);
const showComments = ref(false);
const showEmojiPicker = ref(false);
const showEditModal = ref(false);
const editedDescription = ref("");
const editedLink = ref("");
const editedImage = ref(null);
const editedImagePreview = ref(null);
const isUpdating = ref(false);

const likedPosts = JSON.parse(localStorage.getItem("likedPosts") || "{}");
const isLiked = ref(likedPosts[props.post._id] || false);
const isBookmarked = ref(false);

function userLiked() {
  const likersList = toRaw(props.post.likes_users);
  if (props.post.likes_users && UserStore.currentUserId) {
    const valTab = Object.values(likersList);
    const userId = Object?.values(UserStore?.currentUserId);

    if (valTab.includes(parseInt(userId))) {
      isLiked.value = true;
    }
  }
}

userLiked();

const emojis = ["😊", "😂", "❤️", "👍", "🎉", "🔥", "👏", "💯"];

const isOwnPost = computed(() => {
  return props.post.author.name === UserStore.currentUserName;
});

const formattedDate = computed(() => {
  if (!props.post.createdAt) return "";
  const date = new Date(props.post.createdAt);
  const options = {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: false,
    timeZone: "Africa/Porto-Novo",
  };
  return new Intl.DateTimeFormat("fr-FR", options).format(date);
});

function formatCommentDate(date) {
  const now = new Date();
  const diff = now - new Date(date);
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return "À l'instant";
  if (minutes < 60) return `Il y a ${minutes} min`;
  if (hours < 24) return `Il y a ${hours}h`;
  if (days < 7) return `Il y a ${days}j`;
  return new Intl.DateTimeFormat("fr-FR", { day: "numeric", month: "short" }).format(
    new Date(date)
  );
}

function toggleLike() {
  isLiked.value = !isLiked.value;
  if (isLiked.value) {
    PostStore.likePost(props.post._id);
  } else {
    PostStore.unlikePost(props.post._id);
  }
}

function toggleBookmark() {
  isBookmarked.value = !isBookmarked.value;
  console.log(isBookmarked.value ? "Post sauvegardé" : "Post retiré des favoris");
}

async function sharePost() {
  try {
    await PostStore.sharePost(props.post._id);
  } catch {
    const post = PostStore.posts.find((p) => p._id === props.post._id);
    if (post) {
      post.shares = (post.shares || 0) + 1;
    }
  }
}

function editPost() {
  editedDescription.value = props.post.content.description;
  editedLink.value = props.post.link || "";
  editedImagePreview.value = props.post.content.image || null;
  editedImage.value = null;
  showEditModal.value = true;
  showMenu.value = false;
}

async function validateLink(url) {
  let testUrl = url.trim();
  if (!testUrl.startsWith("http://") && !testUrl.startsWith("https://")) {
    testUrl = "https://" + testUrl;
  }

  try {
    await fetch(testUrl, {
      method: "HEAD",
      mode: "no-cors",
    });
    return true;
  } catch {
    return false;
  }
}

async function saveEdit() {
  if (!editedLink.value.trim()) {
    alert("Le lien est obligatoire");
    return;
  }

  isUpdating.value = true;

  try {
    const isLinkValid = await validateLink(editedLink.value);
    if (!isLinkValid) {
      alert("Le lien fourni semble invalide ou inaccessible.");
      isUpdating.value = false;
      return;
    }

    let imageUrl = null;
    if (editedImage.value) {
      imageUrl = await uploadImage(editedImage.value);
    } else if (editedImagePreview.value) {
      imageUrl = editedImagePreview.value;
    }

    let finalLink = editedLink.value.trim();
    if (!finalLink.startsWith("http://") && !finalLink.startsWith("https://")) {
      finalLink = "https://" + finalLink;
    }

    await PostStore.updatePost(props.post._id, {
      content: {
        description: editedDescription.value,
        image: imageUrl,
      },
      link: finalLink,
    });
    showEditModal.value = false;
  } catch (error) {
    console.log("erreur :", error);
    alert("Erreur lors de la modification du post");
  } finally {
    isUpdating.value = false;
  }
}

function deletePost() {
  if (confirm("Êtes-vous sûr de vouloir supprimer ce post ?")) {
    PostStore.deletePost(props.post._id);
    showMenu.value = false;
  }
}

function reportPost() {
  alert("Ce post a été signalé. Notre équipe va l'examiner.");
  showMenu.value = false;
}

function handleCommentPost() {
  if (comment.value.trim()) {
    const newComment = {
      author: {
        id: UserStore.currentUser?.id,
        firstName: UserStore.currentUser?.firstName,
        lastName: UserStore.currentUser?.lastName,
        photo: UserStore.currentUserAvatar,
      },
      content: comment.value,
    };

    PostStore.addComment(props.post._id, newComment);
    comment.value = "";
    showEmojiPicker.value = false;
  }
}

function addEmoji(emoji) {
  comment.value += emoji;
}

function handleEditImageUpload(event) {
  const file = event.target.files[0];
  if (file) {
    editedImage.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      editedImagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function removeEditImage() {
  editedImage.value = null;
  editedImagePreview.value = null;
}

onMounted(async () => {
  await PostStore.fetchPosts();
  await UserStore.fetchUsers();
});
</script>
