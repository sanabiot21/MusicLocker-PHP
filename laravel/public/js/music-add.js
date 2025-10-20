/**
 * Handles Spotify search result selection and form auto-population
 */

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('addMusicForm');

  document.addEventListener('click', function (e) {
    if (e.target.closest('.select-track')) {
      const button = e.target.closest('.select-track');
      const trackData = JSON.parse(button.dataset.track);
      populateForm(trackData);

      const searchModal = bootstrap.Modal.getInstance(
        document.getElementById('searchModal')
      );
      if (searchModal) {
        searchModal.hide();
      }

      setTimeout(() => {
        scrollToForm();
        highlightForm();
      }, 300);
    }
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
    document.getElementById('release_year').value =
      trackData.release_year || '';
    document.getElementById('duration').value = trackData.duration || '';

    // Spotify metadata (hidden fields)
    document.getElementById('spotify_id').value = trackData.spotify_id || '';
    document.getElementById('spotify_url').value = trackData.spotify_url || '';
    document.getElementById('album_art_url').value =
      trackData.album_art_url || '';

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
      block: 'start',
    });
  }

  /**
   * Highlight form with neon glow effect
   */
  function highlightForm() {
    const formCard = form.closest('.feature-card');

    // Add glow effect
    formCard.style.boxShadow =
      '0 0 30px var(--accent-blue), 0 0 60px var(--accent-blue)';
    formCard.style.transform = 'scale(1.02)';
    formCard.style.transition = 'all 0.3s ease';

    // Remove glow after 2 seconds
    setTimeout(() => {
      formCard.style.boxShadow = '';
      formCard.style.transform = 'scale(1)';
    }, 2000);

    // Show success toast
    showToast(
      'Selected!',
      'Track information populated. Add your personal rating and notes.',
      'success'
    );
  }

  /**
   * Form validation and enhancement
   */
  form.addEventListener('submit', function (e) {
    const title = document.getElementById('title').value.trim();
    const artist = document.getElementById('artist').value.trim();

    if (!title || !artist) {
      e.preventDefault();
      showToast(
        'Required Fields',
        'Please fill in both title and artist fields.',
        'warning'
      );

      // Highlight empty required fields
      if (!title) highlightField(document.getElementById('title'));
      if (!artist) highlightField(document.getElementById('artist'));

      return false;
    }

    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i class="bi bi-arrow-repeat spin me-2"></i>Adding to Collection...';

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
    field.addEventListener(
      'input',
      function () {
        this.classList.remove('is-invalid');
        this.style.boxShadow = '';
      },
      { once: true }
    );

    // Focus the field
    field.focus();
  }

  // Delegate to global toast
  function showToast(title, message, type = 'info') {
    if (
      window.MusicLocker &&
      typeof window.MusicLocker.showToast === 'function'
    ) {
      const composed =
        (title ? `<strong>${title}</strong><br>` : '') + (message || '');
      window.MusicLocker.showToast(composed, type);
    }
  }

  /**
   * Add spinning animation for loading states and enhanced styles
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
        
        .select-track:hover, .select-album:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px var(--accent-blue);
            transition: all 0.2s ease;
        }
        
        .album-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px var(--accent-purple);
            transition: all 0.3s ease;
        }
        
        .track-selection-card {
            transition: all 0.2s ease;
        }
        
        .track-selection-card:hover {
            transform: translateX(5px);
            background-color: rgba(0, 212, 255, 0.1) !important;
            border-left: 3px solid var(--accent-blue);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.25);
        }
    `;
  document.head.appendChild(style);

  /**
   * Enhance modal track cards with hover effects
   */
  document.addEventListener('click', function (e) {
    if (e.target.closest('.track-card')) {
      const card = e.target.closest('.track-card');
      card.style.transform = 'scale(0.98)';
      setTimeout(() => {
        card.style.transform = '';
      }, 150);
    }
  });
});
