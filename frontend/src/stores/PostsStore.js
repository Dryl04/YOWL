import { defineStore } from "pinia";
import api from "@/services/api";

export const usePostStore = defineStore("posts", {
  state: () => ({
    posts: [],
    selectedPost: null,
    hashtagFilter: null,
    searchQuery: null,
    isLoading: false,
    error: null,
    isAdmin: false //admin

  }),

  getters: {
    sortedPosts: (state) => {
      let filtered = [...state.posts];

      if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(post =>
          post.content.description.toLowerCase().includes(query) ||
          (post.link && post.link.toLowerCase().includes(query)) ||
          post.author.name.toLowerCase().includes(query)
        );
      }

      if (state.hashtagFilter) {
        filtered = filtered.filter(post => {
          const hashtags = extractHashtags(post.content.description);
          return hashtags.includes(state.hashtagFilter);
        });
      }

      return filtered.sort((a, b) =>
        new Date(b.createdAt) - new Date(a.createdAt)
      );
    },

    postsCount: (state) => state.posts.length,

    filteredPostsCount: (state) => {
      if (!state.hashtagFilter) return state.posts.length;

      return state.posts.filter(post => {
        const hashtags = extractHashtags(post.content.description);
        return hashtags.includes(state.hashtagFilter);
      }).length;
    }
  },

  actions: {
    async fetchPosts() {
      this.isLoading = true;
      this.error = null;
      try {
        const { data } = await api.get('/posts');
        this.posts = data.data.map(post => ({
          _id: post.id,
          author: {
            name: post.user.name,
            work: post.user.work || 'Membre',
            photo: post.user.photo
          },
          content: {
            description: post.description,
            image: post.image
          },
          link: post.link,
          likes: post.likes,
          likes_users: post.likes_users,
          comments: post.commentaires || [],
          shares: post.shares,
          createdAt: new Date(post.created_at)
        }));
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error('Erreur lors du chargement des posts:', error);
      } finally {
        this.isLoading = false;
      }
    },

    async addPost(postData) {
      this.isLoading = true;
      this.error = null;
      try {
        const payload = {
          description: postData.content.description,
          user_id: postData.author.userId
        };

        if (postData.content.image && !postData.content.image.startsWith('data:')) {
          payload.image = postData.content.image;
        }

        if (postData.link) {
          const url = postData.link.startsWith('http') ? postData.link : `https://${postData.link}`;
          payload.link = url;
        }

        const { data } = await api.post('/posts', payload);

        const newPost = {
          _id: data.data.id,
          author: postData.author,
          content: {
            description: data.data.description,
            image: data.data.image
          },
          link: data.data.link,
          likes: 0,
          comments: [],
          shares: 0,
          createdAt: new Date()
        };

        this.posts.unshift(newPost);
        return newPost;
      } catch (error) {
        console.error('Erreur:', error.response?.data);
        this.error = error.response?.data?.message || error.message;
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async updatePost(postId, updatedData) {
      this.isLoading = true;
      this.error = null;
      try {
        await api.put(`/post/${postId}`, {
          description: updatedData.content?.description,
          image: updatedData.content?.image,
          link: updatedData.link
        });

        const index = this.posts.findIndex(p => p._id === postId);
        if (index !== -1) {
          this.posts[index] = { ...this.posts[index], ...updatedData };
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error('Erreur lors de la mise à jour du post:', error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async deletePost(postId) {
      this.isLoading = true;
      this.error = null;
      try {
        await api.delete(`/post/${postId}`);
        this.posts = this.posts.filter(p => p._id !== postId);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error('Erreur lors de la suppression du post:', error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async likePost(postId) {
      try {
        const { data } = await api.get(`/post/${postId}/like`);
        const post = this.posts.find(p => p._id === postId);
        if (post) {
          if(data.success === false && data.message === "Vous avez déjà liker ce post") return;
          else{

            post.likes = data.data.likes;
          }
        }
      } catch (error) {
        console.error('Erreur lors du like:', error);
        throw error;
      }
    },

    async unlikePost(postId) {
      try {
        const { data } = await api.get(`/post/${postId}/unlike`);
        const post = this.posts.find(p => p._id === postId);
        if (post) {
          post.likes = data.data.likes;
        }
      } catch (error) {
        console.error('Erreur lors du unlike:', error);
        throw error;
      }
    },

    async sharePost(postId) {
      try {
        const { data } = await api.get(`/post/${postId}/share`);
        const post = this.posts.find(p => p._id === postId);
        if (post) {
          post.shares = data.data.shares;
        }
      } catch (error) {
        console.error('Erreur lors du partage:', error);
        throw error;
      }
    },

    selectPost(post) {
      this.selectedPost = post;
    },

    async addComment(postId, commentData) {
      try {
        const { data } = await api.post(`/posts/${postId}/comments`, {
          contenu: commentData.content,
          user_id: commentData.author.id
        });

      const post = this.posts.find(p => p._id === postId);
        if (post) {
          post.comments.push({
            id: data.comment.id,
            contenu: data.comment.contenu,
            status: data.comment.status,
            created_at: data.comment.created_at,
            user: {
              id: commentData.author.id,
              name: `${commentData.author.firstName} ${commentData.author.lastName}`,
              photo: commentData.author.photo
            }
          });
    }
  } catch (error) {
    console.log('erreur : ', error);

    console.warn('API commentaires indisponible, ajout local');
    const post = this.posts.find(p => p._id === postId);
    if (post) {
      post.comments.push({
        id: Date.now().toString(),
        contenu: commentData.content,
        status: 'active',
        created_at: new Date().toISOString(),
        user: {
          id: commentData.author.id,
          name: `${commentData.author.firstName} ${commentData.author.lastName}`,
          photo: commentData.author.photo
        }
      });
    }
  }
},

    async deleteComment(commentId) {
      try {
        await api.delete(`/comments/${commentId}`);

        this.posts.forEach(post => {
          post.comments = post.comments.filter(c => c.id !== commentId);
        });
      } catch (error) {
        console.error('Erreur lors de la suppression du commentaire:', error);
        throw error;
      }
    },

    setHashtagFilter(hashtag) {
      this.hashtagFilter = hashtag;
    },

    clearHashtagFilter() {
      this.hashtagFilter = null;
    },

    setSearchQuery(query) {
      this.searchQuery = query;
    },

    clearSearchQuery() {
      this.searchQuery = null;
    },

    async admindeletePost(postId) {
      this.isAdmin === true;
      this.isLoading = true;
      this.error = null;

      if (!this.isAdmin())
        throw new Error('Permission refusée : accès admin requis.');

      else {
        try {
          await api.delete(`/post/${postId}`);
          this.posts = this.posts.filter(p => p._id !== postId);
        } catch (error) {
          this.error = error.response?.data?.message || error.message;
          console.error('Erreur lors de la suppression du post:', error);
          throw error;
        } finally {
          this.isLoading = false;
        }
      }
    },

    async adminDeleteComment(commentId) {
      this.isAdmin === true;
      this.isLoading = true;
      this.error = null;

      if (!this.isAdmin())
        throw new Error('Permission refusée : accès admin requis.');

      else {
        try {
          await api.delete(`/comments/${commentId}`);

          this.posts.forEach(post => {
            post.comments = post.comments.filter(c => c.id !== commentId);
          });
        }
        catch (error) {
          console.error('Erreur lors de la suppression du commentaire:', error);
          throw error;
        }
      }
    },

    async adminUpdatePost(postId, updatedData) {
      this.isAdmin === true;
      this.isLoading = true;
      this.error = null;
      try {
        await api.put(`/post/${postId}`, {
          description: updatedData.content?.description,
          image: updatedData.content?.image,
          link: updatedData.link
        });

        const index = this.posts.findIndex(p => p._id === postId);
        if (index !== -1) {
          this.posts[index] = { ...this.posts[index], ...updatedData };
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error('Erreur lors de la mise à jour du post:', error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
  },
    persist: {
    key: "posts",
    paths: ["posts"],
  },
});

function extractHashtags(text) {
  const regex = /#[\wÀ-ÿ]+/g;
  const matches = text.match(regex);
  return matches ? matches.map(tag => tag.toLowerCase()) : [];
}