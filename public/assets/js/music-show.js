/**
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
     * Toggle favorite status
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
    
    // Delegate to global toast
    function showToast(title, message, type = 'info') {
        if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
            const composed = (title ? `<strong>${title}</strong><br>` : '') + (message || '');
            window.MusicLocker.showToast(composed, type);
        }
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
    
    // Remove legacy alert auto-hide (no in-flow alerts anymore)
    
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