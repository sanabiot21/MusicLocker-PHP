/**
 * Add Music JavaScript
 * Music Locker - Team NaturalStupidity
 * 
 * Handles Spotify search result selection and form auto-population
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addMusicForm');
    const selectButtons = document.querySelectorAll('.select-track');
    
    // Track selection from Spotify results
    selectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const trackData = JSON.parse(this.dataset.track);
            populateForm(trackData);
            scrollToForm();
            highlightForm();
        });
    });
    
    /**
     * Populate form with track data from Spotify
     */
    function populateForm(trackData) {
        // Basic fields
        document.getElementById('title').value = trackData.title || '';
        document.getElementById('artist').value = trackData.artist || '';
        document.getElementById('album').value = trackData.album || '';
        document.getElementById('genre').value = trackData.genre || '';
        document.getElementById('release_year').value = trackData.release_year || '';
        document.getElementById('duration').value = trackData.duration || '';
        
        // Spotify metadata (hidden fields)
        document.getElementById('spotify_id').value = trackData.spotify_id || '';
        document.getElementById('spotify_url').value = trackData.spotify_url || '';
        document.getElementById('album_art_url').value = trackData.album_art_url || '';
        
        // Focus on first empty field or personal rating
        const personalRating = document.getElementById('personal_rating');
        personalRating.focus();
    }
    
    /**
     * Scroll to form smoothly
     */
    function scrollToForm() {
        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
    
    /**
     * Highlight form with neon glow effect
     */
    function highlightForm() {
        const formCard = form.closest('.feature-card');
        
        // Add glow effect
        formCard.style.boxShadow = '0 0 30px var(--accent-blue), 0 0 60px var(--accent-blue)';
        formCard.style.transform = 'scale(1.02)';
        formCard.style.transition = 'all 0.3s ease';
        
        // Remove glow after 2 seconds
        setTimeout(() => {
            formCard.style.boxShadow = '';
            formCard.style.transform = 'scale(1)';
        }, 2000);
        
        // Show success toast
        showToast('Selected!', 'Track information populated. Add your personal rating and notes.', 'success');
    }
    
    /**
     * Form validation and enhancement
     */
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const artist = document.getElementById('artist').value.trim();
        
        if (!title || !artist) {
            e.preventDefault();
            showToast('Required Fields', 'Please fill in both title and artist fields.', 'warning');
            
            // Highlight empty required fields
            if (!title) highlightField(document.getElementById('title'));
            if (!artist) highlightField(document.getElementById('artist'));
            
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin me-2"></i>Adding to Collection...';
        
        // If form validation passes, let it submit normally
        // The loading state will be cleared by page navigation
    });
    
    /**
     * Highlight field with error state
     */
    function highlightField(field) {
        field.classList.add('is-invalid');
        field.style.boxShadow = '0 0 10px #dc3545';
        
        // Remove highlight after user starts typing
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            this.style.boxShadow = '';
        }, { once: true });
        
        // Focus the field
        field.focus();
    }
    
    /**
     * Show toast notification
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
     * Add spinning animation for loading states
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
        
        .select-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px var(--accent-blue);
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.25);
        }
    `;
    document.head.appendChild(style);
    
    /**
     * Auto-resize search results on mobile
     */
    function adjustSearchResults() {
        const searchResults = document.querySelectorAll('.col-md-6');
        
        if (window.innerWidth < 768) {
            searchResults.forEach(col => {
                col.className = 'col-12 mb-3';
            });
        }
    }
    
    // Adjust on load and resize
    adjustSearchResults();
    window.addEventListener('resize', adjustSearchResults);
    
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