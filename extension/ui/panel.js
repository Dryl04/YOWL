class YowlPanel {
    constructor() {
        this.currentUrl = '';
        this.selectedText = '';
        this.comments = [];
        this.isLoggedIn = false;
        this.user = null;
        
        this.init();
    }
    
    init() {
        this.parseUrlParams();
        this.setupEventListeners();
        this.loadUserInfo();
        this.loadComments();
        this.updateUI();
    }
    
    parseUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        this.currentUrl = decodeURIComponent(urlParams.get('url') || '');
        this.selectedText = decodeURIComponent(urlParams.get('selectedText') || '');
        this.selector = decodeURIComponent(urlParams.get('selector') || '');
        
        const urlElement = document.getElementById('current-url');
        if (urlElement) {
            urlElement.textContent = this.currentUrl;
        }
    }
    
    setupEventListeners() {
        const closeBtn = document.getElementById('close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closePanel());
        }
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });
        const submitBtn = document.getElementById('submit-comment');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => this.submitComment());
        }
        const cancelBtn = document.getElementById('cancel-comment');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelComment());
        }
        window.addEventListener('message', (event) => {
            if (event.data.action === 'refreshComments') {
                this.loadComments();
            }
        });
    }
    
    switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.remove('active');
        });
        document.getElementById(`${tabName}-tab`).classList.add('active');
        if (tabName === 'add') {
            this.prepareAddComment();
        }
    }
    
    prepareAddComment() {
        const commentText = document.getElementById('comment-text');
        if (this.selectedText && commentText) {
            commentText.value = `À propos de "${this.selectedText}": `;
            commentText.focus();
        }
        const scopeSelect = document.getElementById('comment-scope');
        if (scopeSelect) {
            if (this.selectedText) {
                scopeSelect.value = 'selection';
            } else {
                scopeSelect.value = 'page';
            }
        }
    }
    
    async loadUserInfo() {
        try {
            const response = await this.sendMessage({ action: 'getUserInfo' });
            if (response.ok) {
                this.isLoggedIn = response.isLoggedIn;
                this.user = response.user;
                this.updateUserStatus();
            }
        } catch (error) {
            console.error('Erreur lors du chargement des infos utilisateur:', error);
        }
    }
    
    updateUserStatus() {
        const userStatus = document.getElementById('user-status');
        if (userStatus) {
            if (this.isLoggedIn && this.user) {
                userStatus.textContent = `Connecté: ${this.user.name || this.user.email}`;
                userStatus.style.color = '#4caf50';
            } else {
                userStatus.textContent = 'Non connecté';
                userStatus.style.color = '#f44336';
            }
        }
    }
    
    async loadComments() {
        try {
            this.showStatus('Chargement des commentaires...');
            
            const response = await this.sendMessage({
                action: 'fetchComments',
                url: this.currentUrl
            });
            
            if (response.ok) {
                this.comments = response.data || [];
                this.displayComments();
                this.showStatus(`${this.comments.length} commentaire(s) trouvé(s)`);
            } else {
                this.showError('Erreur lors du chargement des commentaires: ' + response.error);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des commentaires:', error);
            this.showError('Erreur de connexion');
        }
    }
    
    displayComments() {
        const commentsList = document.getElementById('comments-list');
        if (!commentsList) return;
        
        if (this.comments.length === 0) {
            commentsList.innerHTML = `
                <div class="empty-state">
                    <h3>Aucun commentaire</h3>
                    <p>Soyez le premier à commenter cette page !</p>
                </div>
            `;
            return;
        }
        
        commentsList.innerHTML = this.comments.map(post => `
            <div class="comment-item">
                <div class="comment-header">
                    <span class="comment-author">${post.user ? post.user.name : 'Utilisateur'}</span>
                    <span class="comment-date">${this.formatDate(post.created_at)}</span>
                </div>
                <div class="comment-text">${this.escapeHtml(post.description)}</div>
                <div class="comment-meta">
                    <span class="comment-scope">Post</span>
                    ${post.link ? `<span>🔗 <a href="${post.link}" target="_blank" style="color: #667eea;">Lien</a></span>` : ''}
                    <span>👍 ${post.likes || 0}</span>
                    <span>📤 ${post.shares || 0}</span>
                </div>
                ${post.commentaires && post.commentaires.length > 0 ? `
                    <div class="post-comments">
                        <h5>Commentaires :</h5>
                        ${post.commentaires.map(comment => `
                            <div class="sub-comment">
                                <strong>${comment.user ? comment.user.name : 'Anonyme'}</strong>: 
                                ${this.escapeHtml(comment.contenu)}
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        `).join('');
    }
    
    async submitComment() {
        const text = document.getElementById('comment-text').value.trim();
        const scope = document.getElementById('comment-scope').value;
        
        if (!text) {
            this.showError('Veuillez saisir un commentaire');
            return;
        }
        
        const submitBtn = document.getElementById('submit-comment');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Publication...';
        
        try {
            const selector = this.selectedText ? this.generateSelector() : null;
            const payload = {
                url: this.currentUrl,
                text: text,
                scope: scope,
                selector: selector
            };
            
            const response = await this.sendMessage({
                action: 'postComment',
                payload: payload
            });
            
            if (response.ok) {
                this.showStatus('Commentaire publié avec succès !');
                this.clearForm();
                this.loadComments();
                this.switchTab('comments');
            } else {
                this.showError('Erreur lors de la publication: ' + response.error);
            }
        } catch (error) {
            console.error('Erreur lors de la publication:', error);
            this.showError('Erreur de connexion');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Publier';
        }
    }
    
    cancelComment() {
        this.clearForm();
        this.switchTab('comments');
    }
    
    clearForm() {
        const textEl = document.getElementById('comment-text');
        const scopeEl = document.getElementById('comment-scope');
        if (textEl) {
            textEl.value = '';
            textEl.blur();
        }
        if (scopeEl) {
            scopeEl.value = 'page';
        }
        this.selectedText = '';
    }
    
    generateSelector() {
        if (this.selector) {
            return this.selector;
        }
        if (this.selectedText) {
            return `selection:${this.selectedText.substring(0, 50)}`;
        }
        return null;
    }
    
    closePanel() {
        window.parent.postMessage({ action: 'closePanel' }, '*');
    }
    
    showStatus(message) {
        const statusElement = document.getElementById('status-message');
        if (statusElement) {
            statusElement.textContent = message;
            statusElement.style.color = '#666';
        }
    }
    
    showError(message) {
        const statusElement = document.getElementById('status-message');
        if (statusElement) {
            statusElement.textContent = message;
            statusElement.style.color = '#f44336';
        }
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) { 
            return 'À l\'instant';
        } else if (diff < 3600000) { 
            return `Il y a ${Math.floor(diff / 60000)} min`;
        } else if (diff < 86400000) { 
            return `Il y a ${Math.floor(diff / 3600000)}h`;
        } else {
            return date.toLocaleDateString('fr-FR');
        }
    }
    
    getScopeLabel(scope) {
        const labels = {
            'page': 'Page',
            'selection': 'Sélection',
            'site': 'Site'
        };
        return labels[scope] || scope;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    sendMessage(message) {
        return new Promise((resolve) => {
            chrome.runtime.sendMessage(message, (response) => {
                resolve(response || { ok: false, error: 'Pas de réponse' });
            });
        });
    }
}

// on initialise le panel quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    new YowlPanel();
});
