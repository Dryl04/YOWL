let yowlButton = null;
let yowlPanel = null;
let isPanelOpen = false;
let commentedElements = new Map(); 
document.addEventListener('DOMContentLoaded', initYowl);
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initYowl);
} else {
  initYowl();
}

function initYowl() {
  injectStyles();
  injectButton();
  setupMessageListener();
  loadCommentedElements();
}

function injectStyles() {
  if (document.getElementById('yowl-styles')) return;
  
  const style = document.createElement('style');
  style.id = 'yowl-styles';
  style.textContent = `
    .yowl-commented {
      background: #FFA500 !important;
    }
  `;
  
  document.head.appendChild(style);
}

function injectButton() {
  if (document.getElementById('yowl-btn')) return;
  
  const button = document.createElement('button');
  button.id = 'yowl-btn';
  button.innerHTML = 'YOWL';
  button.title = 'Ouvrir YOWL - Commenter cette page';
  
  Object.assign(button.style, {
        position: 'fixed',
        right: '20px',
        bottom: '20px',
        zIndex: '999999',
        padding: '12px 16px',
        borderRadius: '25px',
        background: '#0f3460',
        color: '#ffffff',
        border: 'none',
        cursor: 'pointer',
        boxShadow: '0 4px 15px rgba(0,0,0,0.2)',
        fontSize: '14px',
        fontWeight: 'bold',
        fontFamily: 'Arial, sans-serif',
        transition: 'all 0.3s ease',
        userSelect: 'none'
    });
  
  button.addEventListener('mouseenter', () => {
    button.style.transform = 'scale(1.05)';
    button.style.background = '#16213e';
    button.style.boxShadow = '0 6px 20px rgba(15, 52, 96, 0.4)';
  });
  
  button.addEventListener('mouseleave', () => {
    button.style.transform = 'scale(1)';
    button.style.background = '#0f3460';
    button.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
  });
  
  button.addEventListener('click', togglePanel);
  
  document.body.appendChild(button);
  yowlButton = button;
}

function togglePanel() {
  if (isPanelOpen) {
    closePanel();
  } else {
    const sel = window.getSelection();
    const selectedText = sel ? sel.toString().trim() : '';
    openPanel(selectedText);
  }
}

function openPanel(selectedText = '') {
  if (yowlPanel) {
    yowlPanel.style.display = 'block';
    isPanelOpen = true;
    return;
  }
  
  let selector = null;
  if (selectedText && window.yowlSelection) {
    const range = window.yowlSelection.getRangeAt(0);
    const element = range.commonAncestorContainer.nodeType === Node.TEXT_NODE 
      ? range.commonAncestorContainer.parentElement 
      : range.commonAncestorContainer;
    selector = generateSimpleSelector(element);
  }
  
  const iframe = document.createElement('iframe');
  iframe.id = 'yowl-panel';
  iframe.src = chrome.runtime.getURL('ui/panel.html') + 
    `?url=${encodeURIComponent(location.href)}&selectedText=${encodeURIComponent(selectedText)}&selector=${encodeURIComponent(selector || '')}`;
  
  Object.assign(iframe.style, {
    position: 'fixed',
    right: '20px',
    bottom: '80px',
    zIndex: '9999999',
    width: '400px',
    height: '500px',
    border: 'none',
    borderRadius: '12px',
    boxShadow: '0 8px 32px rgba(0,0,0,0.3)',
    background: '#ffffff'
  });
  
  document.body.appendChild(iframe);
  yowlPanel = iframe;
  isPanelOpen = true;
  
}

function closePanel() {
  if (yowlPanel) {
    yowlPanel.style.display = 'none';
    isPanelOpen = false;
  }
}


function setupMessageListener() {
  chrome.runtime.onMessage.addListener((message, _sender, _sendResponse) => {
    switch (message.action) {
      case 'openPanel':
        openPanel(message.selectedText);
        break;
        
      case 'closePanel':
        closePanel();
        break;
        
      case 'refreshComments':
        if (yowlPanel && yowlPanel.contentWindow) {
          yowlPanel.contentWindow.postMessage({
            action: 'refreshComments'
          }, '*');
        }
        break;
        
      case 'highlightElement':
        if (message.selector) {
          saveCommentedElement(message.selector, message.commentCount || 1);
        }
        break;
        
      case 'removeHighlight':
        if (message.selector) {
          removeHighlightFromElement(message.selector);
        }
        break;
        
        case 'toggleExtension':
          if (message.enabled === false) {
            document.querySelectorAll('.yowl-commented').forEach(element => {
              element.classList.remove('yowl-commented');
            });
            if (yowlButton) yowlButton.style.display = 'none';
          } else {
            loadCommentedElements();
            if (yowlButton) yowlButton.style.display = 'block';
          }
          break;
    }
  });
}

document.addEventListener('mouseup', (_event) => {
  const selection = window.getSelection();
  const selectedText = selection.toString().trim();
  
  if (selectedText.length > 0) {
    // on stocke
    //  la sélection pour l'utiliser dans le panel
    window.yowlSelectedText = selectedText;
    window.yowlSelection = selection;
  }
});
s
async function loadCommentedElements() {
  try {
    const result = await chrome.storage.local.get(['commentedElements']);
    if (result.commentedElements && result.commentedElements[location.href]) {
      const elements = result.commentedElements[location.href];
      elements.forEach(elementData => {
        highlightCommentedElement(elementData.selector, elementData.commentCount);
      });
    }
  } catch (error) {
    console.error('Erreur lors du chargement des éléments commentés:', error);
  }
}

function generateSimpleSelector(element) {
  if (element.id) {
    return `#${element.id}`;
  }
  
  let selector = element.tagName.toLowerCase();
  if (element.className) {
    const firstClass = element.className.split(' ')[0];
    if (firstClass) {
      selector += '.' + firstClass;
    }
  }
  
  return selector;
}

function highlightCommentedElement(selector, commentCount = 1, isNew = false) {
  try {
    const element = document.querySelector(selector);
    if (element) {
      element.classList.add('yowl-commented');
      
      commentedElements.set(selector, {
        element: element,
        commentCount: commentCount
      });
    }
  } catch (error) {
    console.error('Erreur lors de la surbrillance de l\'élément:', error);
  }
}


async function saveCommentedElement(selector, commentCount = 1) {
  try {
    const result = await chrome.storage.local.get(['commentedElements']);
    const commentedElements = result.commentedElements || {};
    
    if (!commentedElements[location.href]) {
      commentedElements[location.href] = [];
    }
    
    const existingIndex = commentedElements[location.href].findIndex(
      item => item.selector === selector
    );
    
    if (existingIndex >= 0) {
      commentedElements[location.href][existingIndex].commentCount = commentCount;
    } else {
      commentedElements[location.href].push({
        selector: selector,
        commentCount: commentCount,
        timestamp: Date.now()
      });
    }
    
    await chrome.storage.local.set({ commentedElements });
    
    // appliquer la surbrillance pour les nouveaux éléments
    const isNew = existingIndex < 0;
    highlightCommentedElement(selector, commentCount, isNew);
  } catch (error) {
    console.error('Erreur lors de la sauvegarde de l\'élément commenté:', error);
  }
}

function removeHighlightFromElement(selector) {
  try {
    const element = document.querySelector(selector);
    if (element) {
      element.classList.remove('yowl-commented');
      commentedElements.delete(selector);
    }
  } catch (error) {
    console.error('Erreur lors de la suppression de la surbrillance:', error);
  }
}

window.addEventListener('beforeunload', () => {
  if (yowlButton) yowlButton.remove();
  if (yowlPanel) yowlPanel.remove();
});
