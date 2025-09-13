/**
 * LibreLinks JavaScript
 * Minimal client-side functionality
 */

// Utility functions
function $(id) {
    return document.getElementById(id);
}

function $$(selector) {
    return document.querySelectorAll(selector);
}

function createElement(tag, className, innerHTML) {
    const el = document.createElement(tag);
    if (className) el.className = className;
    if (innerHTML) el.innerHTML = innerHTML;
    return el;
}

// API helper
async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(endpoint, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API call failed:', error);
        throw error;
    }
}

// Toast notifications
function showToast(message, type = 'info') {
    const toast = createElement('div', `alert alert-${type}`, message);
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1001;
        max-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Form handling
function handleForm(formId, endpoint, onSuccess) {
    const form = $(formId);
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const result = await apiCall(endpoint, 'POST', data);
            
            if (result.success) {
                if (onSuccess) onSuccess(result);
                showToast(result.message || 'تم بنجاح', 'success');
            } else {
                showToast(result.message || 'حدث خطأ', 'error');
            }
        } catch (error) {
            showToast('حدث خطأ في الاتصال', 'error');
        }
    });
}

// Modal functionality
function openModal(modalId) {
    const modal = $(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = $(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Link management
function addLink() {
    const title = prompt('عنوان الرابط:');
    const url = prompt('عنوان URL:');
    
    if (title && url) {
        apiCall('/api.php?action=add_link', 'POST', { title, url })
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.message, 'error');
                }
            });
    }
}

function deleteLink(linkId) {
    if (confirm('هل أنت متأكد من حذف هذا الرابط؟')) {
        apiCall('/api.php?action=delete_link', 'POST', { linkId })
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.message, 'error');
                }
            });
    }
}

function updateLink(linkId, title, url) {
    apiCall('/api.php?action=update_link', 'POST', { linkId, title, url })
        .then(result => {
            if (result.success) {
                showToast('تم تحديث الرابط', 'success');
            } else {
                showToast(result.message, 'error');
            }
        });
}

// Profile management
function updateProfile() {
    const name = $('profile-name')?.value;
    const bio = $('profile-bio')?.value;
    const handle = $('profile-handle')?.value;
    
    if (name && handle) {
        apiCall('/api.php?action=update_profile', 'POST', { name, bio, handle })
            .then(result => {
                if (result.success) {
                    showToast('تم تحديث الملف الشخصي', 'success');
                } else {
                    showToast(result.message, 'error');
                }
            });
    }
}

// Click tracking
function trackClick(linkId) {
    apiCall('/api.php?action=track_click', 'POST', { linkId });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('تم النسخ إلى الحافظة', 'success');
    }).catch(() => {
        showToast('فشل في النسخ', 'error');
    });
}

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    // Setup form handlers
    handleForm('login-form', '/api.php?action=login', (result) => {
        window.location.href = '/admin';
    });
    
    handleForm('register-form', '/api.php?action=register', (result) => {
        window.location.href = '/admin';
    });
    
    // Setup modal close handlers
    $$('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });
    
    // Setup link click tracking
    $$('[data-link-id]').forEach(link => {
        link.addEventListener('click', () => {
            const linkId = link.getAttribute('data-link-id');
            trackClick(linkId);
        });
    });
    
    // Setup copy profile link
    const copyButton = $('copy-profile-link');
    if (copyButton) {
        copyButton.addEventListener('click', () => {
            const profileUrl = copyButton.getAttribute('data-url');
            copyToClipboard(profileUrl);
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            $$('.modal').forEach(modal => {
                if (modal.style.display === 'flex') {
                    closeModal(modal.id);
                }
            });
        }
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);