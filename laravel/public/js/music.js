/**
 * Handles favorite toggling, delete confirmations, and offline caching for music collection
 * Enhanced with centralized OfflineManager
 */

document.addEventListener('DOMContentLoaded', function () {
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');

  // Wait for OfflineManager to be available
  if (typeof window.MusicLockerOffline === 'undefined') {
    console.warn('OfflineManager not loaded, using fallback caching');
  }

  // Enhanced offline cache: Save current collection view to localStorage
  cacheCurrentCollection();

  // Try to load from cache if offline
  if (!navigator.onLine) {
    loadFromCache();
  }

  // Listen for online/offline events
  window.addEventListener('musiclocker:online', handleOnlineStatus);
  window.addEventListener('musiclocker:offline', handleOfflineStatus);

  // Favorite toggle functionality
  const favoriteButtons = document.querySelectorAll('.favorite-btn');
  favoriteButtons.forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      toggleFavorite(this);
    });
  });

  // Delete button functionality
  const deleteButtons = document.querySelectorAll('.delete-btn');
  const deleteModal = new bootstrap.Modal(
    document.getElementById('deleteModal')
  );
  const deleteForm = document.getElementById('deleteForm');
  const deleteTitle = document.getElementById('deleteTitle');

  deleteButtons.forEach((button) => {
    button.addEventListener('click', function () {
      const entryId = this.dataset.entryId;
      const title = this.dataset.title;

      deleteTitle.textContent = title;
      deleteForm.action = `/music/${entryId}`;

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
          'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({
          entry_id: parseInt(entryId),
          csrf_token: csrfToken,
        }),
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
        showToast(
          'Error',
          data.error || 'Failed to update favorite status',
          'error'
        );
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
   * Add glow effects to buttons on hover
   */
  function addGlowEffects() {
    const glowButtons = document.querySelectorAll(
      '.btn-glow, .btn-outline-glow'
    );

    glowButtons.forEach((button) => {
      button.addEventListener('mouseenter', function () {
        this.style.boxShadow =
          '0 0 20px var(--accent-blue), 0 0 40px var(--accent-blue)';
        this.style.transition = 'box-shadow 0.3s ease';
      });

      button.addEventListener('mouseleave', function () {
        this.style.boxShadow = 'none';
      });
    });
  }

  // Initialize glow effects
  addGlowEffects();

  /**
   * Smooth scrolling for anchor links
   */
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start',
        });
      }
    });
  });

  // Remove legacy alert auto-hide (no in-flow alerts anymore)

  /**
   * Cache current collection view to localStorage (enhanced with OfflineManager)
   */
  function cacheCurrentCollection() {
    try {
      const musicGrid = document.querySelector('.music-grid, .row.g-4');
      if (!musicGrid) return;

      const entries = Array.from(
        musicGrid.querySelectorAll('.music-card, .feature-card')
      )
        .map((card) => {
          // Extract comprehensive data from card
          const title = card
            .querySelector('h5, .card-title')
            ?.textContent?.trim();
          const artist = card.querySelector('.text-muted')?.textContent?.trim();
          const img = card.querySelector('img')?.src;

          // Extract entry ID and additional metadata
          const entryId = card.querySelector('[data-entry-id]')?.dataset?.entryId;
          const favoriteBtn = card.querySelector('.favorite-btn');
          const isFavorite = favoriteBtn?.dataset?.isFavorite === '1';

          // Extract rating if available
          const ratingElement = card.querySelector('[data-rating]');
          const rating = ratingElement ? parseInt(ratingElement.dataset.rating) : null;

          return {
            id: entryId,
            title,
            artist,
            img,
            is_favorite: isFavorite,
            rating: rating
          };
        })
        .filter((e) => e.title);

      if (entries.length > 0) {
        // Use OfflineManager if available, fallback to basic localStorage
        if (window.MusicLockerOffline) {
          window.MusicLockerOffline.cacheCollection(entries, {
            filters: getCurrentFilters(),
            sortOrder: getCurrentSortOrder()
          });
          console.log(`Cached ${entries.length} entries using OfflineManager`);
        } else {
          // Fallback to basic localStorage
          localStorage.setItem(
            'musiclocker_collection_cache',
            JSON.stringify({
              entries,
              timestamp: Date.now(),
              url: window.location.href,
            })
          );
        }
      }
    } catch (e) {
      console.warn('Failed to cache collection:', e);
    }
  }

  /**
   * Get current filters from URL params
   */
  function getCurrentFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    return {
      search: urlParams.get('search') || '',
      artist: urlParams.get('artist') || '',
      genre: urlParams.get('genre') || '',
      rating: urlParams.get('rating') || ''
    };
  }

  /**
   * Get current sort order from URL or UI
   */
  function getCurrentSortOrder() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('sort') || 'date_added_desc';
  }

  /**
   * Load collection from cache when offline (enhanced)
   */
  function loadFromCache() {
    try {
      let cached, cacheAge;

      // Try OfflineManager first
      if (window.MusicLockerOffline) {
        const cacheData = window.MusicLockerOffline.getCachedCollection({ includeMetadata: true });
        if (cacheData) {
          cached = cacheData.data;
          cacheAge = cacheData.age;
        }
      } else {
        // Fallback to basic localStorage
        const cachedStr = localStorage.getItem('musiclocker_collection_cache');
        if (cachedStr) {
          const data = JSON.parse(cachedStr);
          cached = data;
          cacheAge = Date.now() - data.timestamp;
        }
      }

      if (!cached) {
        showOfflineBanner('No cached data available');
        return;
      }

      // Check if cache is too old (24 hours)
      if (cacheAge && cacheAge > 24 * 60 * 60 * 1000) {
        showOfflineBanner('Cached data is too old (over 24 hours)');
        return;
      }

      // Show offline indicator with cache age
      const ageMinutes = Math.floor(cacheAge / 60000);
      const ageHours = Math.floor(ageMinutes / 60);

      let ageText;
      if (ageHours > 0) {
        ageText = `${ageHours} hour${ageHours > 1 ? 's' : ''} ago`;
      } else if (ageMinutes > 0) {
        ageText = `${ageMinutes} minute${ageMinutes > 1 ? 's' : ''} ago`;
      } else {
        ageText = 'just now';
      }

      showOfflineBanner(`Showing cached collection (last updated ${ageText})`);

      console.log('Loaded cached collection:', cached.entries?.length || cached.metadata?.totalCount, 'entries');
    } catch (e) {
      console.warn('Failed to load from cache:', e);
    }
  }

  /**
   * Show offline banner
   */
  function showOfflineBanner(message) {
    const container = document.querySelector('.container');
    if (!container) return;

    // Remove existing offline banners
    document.querySelectorAll('.offline-banner').forEach(b => b.remove());

    const banner = document.createElement('div');
    banner.className = 'alert alert-warning mb-3 offline-banner d-flex align-items-center';
    banner.style.borderLeft = '4px solid var(--accent-blue)';
    banner.innerHTML = `
      <i class="bi bi-wifi-off me-2 fs-5"></i>
      <div class="flex-grow-1">
        <strong>Offline Mode</strong><br>
        <small>${message}</small>
      </div>
      <button class="btn btn-sm btn-outline-warning ms-2" onclick="location.reload()">
        <i class="bi bi-arrow-repeat me-1"></i> Retry
      </button>
    `;
    container.insertBefore(banner, container.firstChild);
  }

  /**
   * Handle online status change
   */
  function handleOnlineStatus() {
    console.log('Back online - refreshing data...');

    // Remove offline banners
    document.querySelectorAll('.offline-banner').forEach(b => b.remove());

    // Show online banner briefly
    const container = document.querySelector('.container');
    if (container) {
      const banner = document.createElement('div');
      banner.className = 'alert alert-success mb-3 d-flex align-items-center';
      banner.innerHTML = `
        <i class="bi bi-wifi me-2 fs-5"></i>
        <div>
          <strong>Back Online!</strong><br>
          <small>Refreshing data...</small>
        </div>
      `;
      container.insertBefore(banner, container.firstChild);

      // Auto-remove after 3 seconds
      setTimeout(() => banner.remove(), 3000);
    }

    // Optionally reload the page to get fresh data
    // setTimeout(() => location.reload(), 1000);
  }

  /**
   * Handle offline status change
   */
  function handleOfflineStatus() {
    console.log('Gone offline - using cached data');
    loadFromCache();
  }
});
