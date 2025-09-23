/**
 * Music Show JavaScript
 * Music Locker - Team NaturalStupidity
 * 
 * Handles favorite toggling and delete confirmation for individual track view
 */

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Favorite toggle functionality
    const favoriteButton = document.querySelector('.favorite-btn');
    if (favoriteButton) {
        favoriteButton.addEventListener('click', function(e) {
            e.preventDefault();
            toggleFavorite(this);
        });
    }
    
    // Delete button functionality
    const deleteButton = document.querySelector('.delete-btn');
    if (deleteButton) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteForm = document.getElementById('deleteForm');
        const deleteTitle = document.getElementById('deleteTitle');
        
        deleteButton.addEventListener('click', function() {
            const entryId = this.dataset.entryId;
            const title = this.dataset.title;
            
            deleteTitle.textContent = title;
            deleteForm.action = `/music/${entryId}/delete`;
            
            deleteModal.show();
        });
    }
    
    /**
     * Toggle favorite status via AJAX
     */
    async function toggleFavorite(button) {
        const entryId = button.dataset.entryId;
        const isFavorite = button.dataset.isFavorite === '1';
        const heartIcon = button.querySelector('i');
        
        // Disable button during request with loading animation
        button.disabled = true;
        button.style.opacity = '0.6';
        
        // Add loading animation
        const originalIcon = heartIcon.className;
        heartIcon.className = 'bi bi-arrow-repeat spin fs-4';
        
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
                
                // Update heart icon with enhanced neon glow effect
                if (data.is_favorite) {
                    heartIcon.className = 'bi bi-heart-fill text-danger fs-4';
                    heartIcon.style.textShadow = '0 0 15px #ff0040, 0 0 30px #ff0040, 0 0 45px #ff0040';
                    button.title = 'Remove from favorites';
                    
                    // Celebratory animation
                    button.style.transform = 'scale(1.3)';
                    setTimeout(() => {
                        button.style.transform = 'scale(1)';
                    }, 300);
                    
                    showToast('Added to Favorites!', 'This track is now in your favorites.', 'success');
                } else {
                    heartIcon.className = 'bi bi-heart fs-4';
                    heartIcon.style.textShadow = 'none';
                    button.title = 'Add to favorites';
                    
                    showToast('Removed from Favorites', 'This track was removed from your favorites.', 'info');
                }
                
                // Pulse animation
                button.style.animation = 'pulse 0.5s ease-in-out';
                setTimeout(() => {
                    button.style.animation = '';
                }, 500);
                
            } else {
                // Restore original icon
                heartIcon.className = originalIcon;
                showToast('Error', data.error || 'Failed to update favorite status', 'error');
            }
            
        } catch (error) {
            console.error('Favorite toggle error:', error);
            heartIcon.className = originalIcon;
            showToast('Network Error', 'Could not connect to server. Please try again.', 'error');
        } finally {
            // Re-enable button
            button.disabled = false;
            button.style.opacity = '1';
            button.style.transition = 'all 0.3s ease';
        }
    }
    
    /**
     * Show toast notification with enhanced styling
     */
    function showToast(title, message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const iconMap = {
            'success': 'bi-check-circle-fill text-success',
            'error': 'bi-exclamation-triangle-fill text-danger',
            'warning': 'bi-exclamation-circle-fill text-warning',
            'info': 'bi-info-circle-fill text-info'
        };
        
        const bgMap = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        };
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgMap[type] || 'bg-dark'} border-0" 
                 role="alert" style="box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="bi ${iconMap[type] || iconMap.info} me-2 fs-5"></i>
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
            delay: type === 'success' ? 3000 : 4000
        });
        
        toast.show();
        
        // Clean up after toast is hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
    
    /**
     * Enhanced animations and effects
     */
    const style = document.createElement('style');
    style.textContent = `
        .spin {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); box-shadow: 0 0 30px var(--accent-blue); }
            100% { transform: scale(1); }
        }
        
        .favorite-btn:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }
        
        .btn-glow:hover, .btn-outline-danger:hover {
            box-shadow: 0 0 20px currentColor;
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
    `;
    document.head.appendChild(style);
    
    /**
     * Keyboard shortcuts
     */
    document.addEventListener('keydown', function(e) {
        // F key to toggle favorite (when not in input field)
        if (e.key === 'f' && !e.target.matches('input, textarea, select')) {
            e.preventDefault();
            if (favoriteButton) {
                favoriteButton.click();
            }
        }
        
        // E key to edit (when not in input field)
        if (e.key === 'e' && !e.target.matches('input, textarea, select')) {
            const editButton = document.querySelector('a[href*="/edit"]');
            if (editButton) {
                window.location.href = editButton.href;
            }
        }
        
        // Escape to close modal
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modal = bootstrap.Modal.getInstance(openModal);
                if (modal) {
                    modal.hide();
                }
            }
        }
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
    
    /**
     * Add loading states to action buttons
     */
    const actionButtons = document.querySelectorAll('.btn-glow, .btn-outline-danger');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' || this.href) {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-repeat spin me-2"></i>Loading...';
                this.disabled = true;
                
                // Re-enable after a short delay (in case of same-page actions)
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                }, 2000);
            }
        });
    });
    
    console.log('Music Show page loaded - Keyboard shortcuts: F (favorite), E (edit), ESC (close modal)');
});