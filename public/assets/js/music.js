/**
 * Music Collection JavaScript
 * Music Locker - Team NaturalStupidity
 * 
 * Handles favorite toggling and delete confirmations for music collection
 */

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Favorite toggle functionality
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            toggleFavorite(this);
        });
    });
    
    // Delete button functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const deleteTitle = document.getElementById('deleteTitle');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const entryId = this.dataset.entryId;
            const title = this.dataset.title;
            
            deleteTitle.textContent = title;
            deleteForm.action = `/music/${entryId}/delete`;
            
            deleteModal.show();
        });
    });
    
    /**
     * Toggle favorite status via AJAX
     */
    async function toggleFavorite(button) {
        const entryId = button.dataset.entryId;
        const isFavorite = button.dataset.isFavorite === '1';
        const heartIcon = button.querySelector('i');
        
        // Disable button during request
        button.disabled = true;
        button.style.opacity = '0.6';
        
        try {
            const response = await fetch('/api/music/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    entry_id: parseInt(entryId),
                    csrf_token: csrfToken
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update button state
                button.dataset.isFavorite = data.is_favorite ? '1' : '0';
                
                // Update heart icon with neon glow effect
                if (data.is_favorite) {
                    heartIcon.className = 'bi bi-heart-fill text-danger';
                    heartIcon.style.textShadow = '0 0 10px #ff0040, 0 0 20px #ff0040';
                    button.title = 'Remove from favorites';
                } else {
                    heartIcon.className = 'bi bi-heart';
                    heartIcon.style.textShadow = 'none';
                    button.title = 'Add to favorites';
                }
                
                // Success animation
                button.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 150);
                
            } else {
                showToast('Error', data.error || 'Failed to update favorite status', 'error');
            }
            
        } catch (error) {
            console.error('Favorite toggle error:', error);
            showToast('Error', 'Network error. Please try again.', 'error');
        } finally {
            // Re-enable button
            button.disabled = false;
            button.style.opacity = '1';
        }
    }
    
    /**
     * Show toast notification (Bootstrap 5 compatible)
     */
    function showToast(title, message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const iconMap = {
            'success': 'bi-check-circle text-success',
            'error': 'bi-exclamation-triangle text-danger',
            'warning': 'bi-exclamation-circle text-warning',
            'info': 'bi-info-circle text-info'
        };
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-dark border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="bi ${iconMap[type] || iconMap.info} me-2"></i>
                        <div>
                            <strong>${title}</strong><br>
                            ${message}
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        // Create or get toast container
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '1070';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 4000
        });
        
        toast.show();
        
        // Clean up after toast is hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
    
    /**
     * Add glow effects to buttons on hover
     */
    function addGlowEffects() {
        const glowButtons = document.querySelectorAll('.btn-glow, .btn-outline-glow');
        
        glowButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 0 20px var(--accent-blue), 0 0 40px var(--accent-blue)';
                this.style.transition = 'box-shadow 0.3s ease';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
    }
    
    // Initialize glow effects
    addGlowEffects();
    
    /**
     * Smooth scrolling for anchor links
     */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    /**
     * Auto-hide alerts after 5 seconds
     */
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});