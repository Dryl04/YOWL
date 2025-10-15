// configuration de l'API
const API_BASE_URL = 'https://api-yowl.coding-academy.website/api/v1';

// pour écouter les messages des content scripts et popup
chrome.runtime.onMessage.addListener((message, _sender, sendResponse) => {
  console.log('Background received message:', message);
  
  switch (message.action) {
    case 'fetchComments':
      fetchComments(message.url, sendResponse);
      return true;
      
    case 'postComment':
      postComment(message.payload, sendResponse);
      return true;
      
    case 'login':
      login(message.credentials, sendResponse);
      return true;
      
    case 'register':
      register(message.credentials, sendResponse);
      return true;
      
    case 'logout':
      logout(sendResponse);
      return true;
      
    case 'getUserInfo':
      getUserInfo(sendResponse);
      return true;
      
    default:
      sendResponse({ ok: false, error: 'Action inconnue' });
  }
});

async function fetchComments(url, sendResponse) {
  try {
    const { token } = await chrome.storage.local.get(['token']);

    const response = await fetch(`${API_BASE_URL}/posts`, {
      headers: {
        'Authorization': token ? `Bearer ${token}` : '',
        'Content-Type': 'application/json'
      }
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = await response.json();
    
    if (result.success) {
      const filteredPosts = result.data.filter(post => 
        post.link === url || 
        post.description.includes(url) ||
        post.description.includes(new URL(url).hostname)
      );
      
      sendResponse({ ok: true, data: filteredPosts });
    } else {
      throw new Error(result.message || 'Erreur API');
    }
  } catch (error) {
    console.error('Erreur lors de la récupération des commentaires:', error);
    sendResponse({ ok: false, error: error.message });
  }
}

async function postComment(payload, sendResponse) {
  try {
    const { token, user } = await chrome.storage.local.get(['token', 'user']);
    
    if (!token || !user) {
      throw new Error('Vous devez être connecté pour publier un commentaire');
    }
    
    const postData = {
      description: `[${payload.scope}] ${payload.text}`,
      link: payload.url,
      user_id: user.id
    };
    
    const response = await fetch(`${API_BASE_URL}/posts`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(postData)
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = await response.json();
    
    if (result.success) {
      if (payload.scope === 'selection' && payload.selector) {
        await saveCommentedElementForHighlight(payload.url, payload.selector);
      }
      
      sendResponse({ ok: true, data: result.data });
    } else {
      throw new Error(result.message || 'Erreur lors de la création du post');
    }
  } catch (error) {
    console.error('Erreur lors de la publication du commentaire:', error);
    sendResponse({ ok: false, error: error.message });
  }
}

// connexion utilisateur
async function login(credentials, sendResponse) {
  try {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(credentials)
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = await response.json();
    
    if (result.success) {
      await chrome.storage.local.set({
        token: result.data.token,
        user: result.data.user
      });
      
      sendResponse({ ok: true, data: result.data });
    } else {
      throw new Error(result.message || 'Erreur de connexion');
    }
  } catch (error) {
    console.error('Erreur lors de la connexion:', error);
    sendResponse({ ok: false, error: error.message });
  }
}

// inscription utilisateur
async function register(credentials, sendResponse) {
  try {
    const response = await fetch(`${API_BASE_URL}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(credentials)
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = await response.json();
    
    if (result.success) {
      await chrome.storage.local.set({
        token: result.data.token,
        user: result.data.user
      });
      
      sendResponse({ ok: true, data: result.data });
    } else {
      throw new Error(result.message || 'Erreur lors de l\'inscription');
    }
  } catch (error) {
    console.error('Erreur lors de l\'inscription:', error);
    sendResponse({ ok: false, error: error.message });
  }
}

// déconnexion
async function logout(sendResponse) {
  try {
    await chrome.storage.local.clear();
    sendResponse({ ok: true });
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error);
    sendResponse({ ok: false, error: error.message });
  }
}

// récupérer les infos utilisateur
async function getUserInfo(sendResponse) {
  try {
    const { user, token } = await chrome.storage.local.get(['user', 'token']);
    sendResponse({ ok: true, user, isLoggedIn: !!token });
  } catch (error) {
    console.error('Erreur lors de la récupération des infos utilisateur:', error);
    sendResponse({ ok: false, error: error.message });
  }
}

// sauvegarder un élément commenté pour la surbrillance
async function saveCommentedElementForHighlight(url, selector) {
  try {
    const result = await chrome.storage.local.get(['commentedElements']);
    const commentedElements = result.commentedElements || {};
    
    if (!commentedElements[url]) {
      commentedElements[url] = [];
    }
    
    const existingIndex = commentedElements[url].findIndex(
      item => item.selector === selector
    );
    
    if (existingIndex >= 0) {
      commentedElements[url][existingIndex].commentCount += 1;
    } else {
      commentedElements[url].push({
        selector: selector,
        commentCount: 1,
        timestamp: Date.now()
      });
    }
    
    await chrome.storage.local.set({ commentedElements });
    
    try {
      const tabs = await chrome.tabs.query({ url: url });
      if (tabs.length > 0) {
        chrome.tabs.sendMessage(tabs[0].id, {
          action: 'highlightElement',
          selector: selector,
          commentCount: commentedElements[url].find(item => item.selector === selector)?.commentCount || 1
        });
      }
    } catch (error) {
      console.error('Erreur lors de la notification du content script:', error);
    }
  } catch (error) {
    console.error('Erreur lors de la sauvegarde de l\'élément commenté:', error);
  }
}

// installation de l'extension
chrome.runtime.onInstalled.addListener(() => {
  // console.log('extension installée');
  
  // créer le menu contextuel
  chrome.contextMenus.create({
    id: 'yowl-comment',
    title: 'Commenter avec YOWL',
    contexts: ['selection']
  });
});

// gestion du menu contextuel
chrome.contextMenus.onClicked.addListener((info, tab) => {
  if (info.menuItemId === 'yowl-comment') {
    chrome.tabs.sendMessage(tab.id, {
      action: 'openPanel',
      selectedText: info.selectionText
    });
  }
});
