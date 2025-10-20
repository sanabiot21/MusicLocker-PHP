/**
 * Offline Manager - Centralized LocalStorage Cache Management
 * Music Locker - Team NaturalStupidity
 *
 * Handles all offline caching, storage management, and data persistence
 */

class OfflineManager {
  constructor() {
    this.storagePrefix = 'musiclocker_';
    this.cacheVersion = 2;
    this.maxCacheSize = 5 * 1024 * 1024; // 5MB limit

    // Cache TTL from Spotify config (in milliseconds)
    this.cacheTTL = {
      collection: 24 * 60 * 60 * 1000,      // 24 hours
      spotifySearch: 30 * 60 * 1000,        // 30 minutes
      spotifyTrack: 60 * 60 * 1000,         // 1 hour
      spotifyArtist: 2 * 60 * 60 * 1000,    // 2 hours
      spotifyAlbum: 2 * 60 * 60 * 1000,     // 2 hours
      userPreferences: Infinity              // Never expires
    };

    // Initialize and clean expired cache on load
    this.init();
  }

  /**
   * Initialize offline manager
   */
  init() {
    try {
      // Check localStorage availability
      if (!this.isLocalStorageAvailable()) {
        console.warn('LocalStorage not available - offline features disabled');
        return;
      }

      // Clean expired cache on init
      this.cleanExpiredCache();

      // Monitor storage quota
      this.checkStorageQuota();

      // Set up online/offline event listeners
      this.setupNetworkListeners();

      console.log('Offline Manager initialized - Cache version:', this.cacheVersion);
    } catch (error) {
      console.error('Failed to initialize Offline Manager:', error);
    }
  }

  /**
   * Check if localStorage is available
   */
  isLocalStorageAvailable() {
    try {
      const test = '__storage_test__';
      localStorage.setItem(test, test);
      localStorage.removeItem(test);
      return true;
    } catch (e) {
      return false;
    }
  }

  /**
   * Setup network status listeners
   */
  setupNetworkListeners() {
    window.addEventListener('online', () => {
      console.log('Network: Back online');
      this.triggerEvent('online');
      this.showNetworkStatus('online');
    });

    window.addEventListener('offline', () => {
      console.log('Network: Offline');
      this.triggerEvent('offline');
      this.showNetworkStatus('offline');
    });
  }

  /**
   * Show network status to user
   */
  showNetworkStatus(status) {
    const message = status === 'online'
      ? 'You are back online! Syncing changes...'
      : 'You are offline. Changes will be saved locally.';

    const type = status === 'online' ? 'success' : 'warning';

    if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
      window.MusicLocker.showToast(message, type);
    }
  }

  /**
   * Trigger custom event
   */
  triggerEvent(eventName) {
    window.dispatchEvent(new CustomEvent(`musiclocker:${eventName}`));
  }

  /**
   * Generic cache set with TTL
   */
  set(key, data, ttl = null) {
    try {
      const fullKey = this.storagePrefix + key;
      const cacheEntry = {
        data: data,
        timestamp: Date.now(),
        ttl: ttl,
        version: this.cacheVersion
      };

      localStorage.setItem(fullKey, JSON.stringify(cacheEntry));
      return true;
    } catch (error) {
      console.error('Cache set error:', error);

      // If quota exceeded, try to clear old cache
      if (error.name === 'QuotaExceededError') {
        this.handleQuotaExceeded();
        // Try again after cleanup
        try {
          localStorage.setItem(this.storagePrefix + key, JSON.stringify({
            data, timestamp: Date.now(), ttl, version: this.cacheVersion
          }));
          return true;
        } catch (retryError) {
          console.error('Cache set failed after cleanup:', retryError);
          return false;
        }
      }
      return false;
    }
  }

  /**
   * Generic cache get with expiry check
   */
  get(key, options = {}) {
    try {
      const fullKey = this.storagePrefix + key;
      const cached = localStorage.getItem(fullKey);

      if (!cached) {
        return null;
      }

      const cacheEntry = JSON.parse(cached);

      // Check version mismatch
      if (cacheEntry.version !== this.cacheVersion) {
        console.warn(`Cache version mismatch for ${key}, clearing...`);
        this.remove(key);
        return null;
      }

      // Check if expired
      if (cacheEntry.ttl !== null && cacheEntry.ttl !== Infinity) {
        const age = Date.now() - cacheEntry.timestamp;
        if (age > cacheEntry.ttl) {
          console.log(`Cache expired for ${key}`);
          this.remove(key);
          return null;
        }
      }

      // Return with metadata if requested
      if (options.includeMetadata) {
        return {
          data: cacheEntry.data,
          timestamp: cacheEntry.timestamp,
          age: Date.now() - cacheEntry.timestamp
        };
      }

      return cacheEntry.data;
    } catch (error) {
      console.error('Cache get error:', error);
      return null;
    }
  }

  /**
   * Remove cache entry
   */
  remove(key) {
    try {
      const fullKey = this.storagePrefix + key;
      localStorage.removeItem(fullKey);
      return true;
    } catch (error) {
      console.error('Cache remove error:', error);
      return false;
    }
  }

  /**
   * Clear all cache
   */
  clearAll() {
    try {
      const keys = Object.keys(localStorage);
      keys.forEach(key => {
        if (key.startsWith(this.storagePrefix)) {
          localStorage.removeItem(key);
        }
      });
      console.log('All cache cleared');
      return true;
    } catch (error) {
      console.error('Cache clear error:', error);
      return false;
    }
  }

  // ============================================
  // MUSIC COLLECTION CACHING
  // ============================================

  /**
   * Cache user's music collection (enhanced)
   */
  cacheCollection(entries, metadata = {}) {
    const cacheData = {
      entries: entries,
      metadata: {
        url: window.location.href,
        totalCount: entries.length,
        filters: metadata.filters || null,
        sortOrder: metadata.sortOrder || null,
        ...metadata
      }
    };

    return this.set('collection_cache', cacheData, this.cacheTTL.collection);
  }

  /**
   * Get cached collection
   */
  getCachedCollection(options = {}) {
    return this.get('collection_cache', options);
  }

  /**
   * Update single entry in collection cache
   */
  updateCachedEntry(entryId, updates) {
    const cached = this.getCachedCollection();
    if (!cached || !cached.entries) return false;

    const entryIndex = cached.entries.findIndex(e => e.id == entryId);
    if (entryIndex === -1) return false;

    // Merge updates
    cached.entries[entryIndex] = {
      ...cached.entries[entryIndex],
      ...updates
    };

    return this.set('collection_cache', cached, this.cacheTTL.collection);
  }

  // ============================================
  // SPOTIFY API CACHING
  // ============================================

  /**
   * Cache Spotify search results
   */
  cacheSpotifySearch(query, results, searchType = 'track') {
    const cacheKey = `spotify_search_${searchType}_${this.hashString(query)}`;
    return this.set(cacheKey, results, this.cacheTTL.spotifySearch);
  }

  /**
   * Get cached Spotify search
   */
  getCachedSpotifySearch(query, searchType = 'track') {
    const cacheKey = `spotify_search_${searchType}_${this.hashString(query)}`;
    return this.get(cacheKey, { includeMetadata: true });
  }

  /**
   * Cache Spotify track details
   */
  cacheSpotifyTrack(spotifyId, trackData) {
    const cacheKey = `spotify_track_${spotifyId}`;
    return this.set(cacheKey, trackData, this.cacheTTL.spotifyTrack);
  }

  /**
   * Get cached Spotify track
   */
  getCachedSpotifyTrack(spotifyId) {
    const cacheKey = `spotify_track_${spotifyId}`;
    return this.get(cacheKey);
  }

  /**
   * Cache Spotify artist details
   */
  cacheSpotifyArtist(artistId, artistData) {
    const cacheKey = `spotify_artist_${artistId}`;
    return this.set(cacheKey, artistData, this.cacheTTL.spotifyArtist);
  }

  /**
   * Get cached Spotify artist
   */
  getCachedSpotifyArtist(artistId) {
    const cacheKey = `spotify_artist_${artistId}`;
    return this.get(cacheKey);
  }

  // ============================================
  // USER PREFERENCES
  // ============================================

  /**
   * Save user preferences
   */
  savePreferences(preferences) {
    return this.set('user_preferences', preferences, this.cacheTTL.userPreferences);
  }

  /**
   * Get user preferences
   */
  getPreferences() {
    return this.get('user_preferences') || {
      filters: {},
      sortOrder: 'date_added_desc',
      viewMode: 'grid',
      itemsPerPage: 20
    };
  }

  /**
   * Update single preference
   */
  updatePreference(key, value) {
    const prefs = this.getPreferences();
    prefs[key] = value;
    return this.savePreferences(prefs);
  }

  // ============================================
  // CACHE MANAGEMENT
  // ============================================

  /**
   * Clean expired cache entries
   */
  cleanExpiredCache() {
    try {
      const keys = Object.keys(localStorage);
      let cleaned = 0;

      keys.forEach(fullKey => {
        if (!fullKey.startsWith(this.storagePrefix)) return;

        try {
          const cached = localStorage.getItem(fullKey);
          if (!cached) return;

          const cacheEntry = JSON.parse(cached);

          // Check if expired
          if (cacheEntry.ttl !== null && cacheEntry.ttl !== Infinity) {
            const age = Date.now() - cacheEntry.timestamp;
            if (age > cacheEntry.ttl) {
              localStorage.removeItem(fullKey);
              cleaned++;
            }
          }

          // Check version mismatch
          if (cacheEntry.version !== this.cacheVersion) {
            localStorage.removeItem(fullKey);
            cleaned++;
          }
        } catch (e) {
          // Invalid cache entry, remove it
          localStorage.removeItem(fullKey);
          cleaned++;
        }
      });

      if (cleaned > 0) {
        console.log(`Cleaned ${cleaned} expired cache entries`);
      }
    } catch (error) {
      console.error('Cache cleanup error:', error);
    }
  }

  /**
   * Handle quota exceeded error
   */
  handleQuotaExceeded() {
    console.warn('Storage quota exceeded, clearing old cache...');

    try {
      // First, try cleaning expired cache
      this.cleanExpiredCache();

      // If still over quota, remove oldest non-critical cache
      const keys = Object.keys(localStorage);
      const cacheEntries = [];

      keys.forEach(fullKey => {
        if (!fullKey.startsWith(this.storagePrefix)) return;
        if (fullKey.includes('user_preferences')) return; // Don't remove preferences
        if (fullKey.includes('offline_queue')) return; // Don't remove queue

        try {
          const cached = localStorage.getItem(fullKey);
          const cacheEntry = JSON.parse(cached);
          cacheEntries.push({
            key: fullKey,
            timestamp: cacheEntry.timestamp
          });
        } catch (e) {
          // Invalid entry, will be cleaned
        }
      });

      // Sort by oldest first
      cacheEntries.sort((a, b) => a.timestamp - b.timestamp);

      // Remove oldest 25%
      const toRemove = Math.ceil(cacheEntries.length * 0.25);
      for (let i = 0; i < toRemove; i++) {
        localStorage.removeItem(cacheEntries[i].key);
      }

      console.log(`Removed ${toRemove} oldest cache entries to free space`);
    } catch (error) {
      console.error('Quota cleanup error:', error);
    }
  }

  /**
   * Get current cache size and statistics
   */
  getCacheStats() {
    try {
      let totalSize = 0;
      let entryCount = 0;
      const breakdown = {
        collection: 0,
        spotify: 0,
        preferences: 0,
        queue: 0,
        other: 0
      };

      const keys = Object.keys(localStorage);
      keys.forEach(fullKey => {
        if (!fullKey.startsWith(this.storagePrefix)) return;

        const value = localStorage.getItem(fullKey);
        const size = new Blob([value]).size;
        totalSize += size;
        entryCount++;

        // Categorize
        if (fullKey.includes('collection')) {
          breakdown.collection += size;
        } else if (fullKey.includes('spotify')) {
          breakdown.spotify += size;
        } else if (fullKey.includes('preferences')) {
          breakdown.preferences += size;
        } else if (fullKey.includes('queue')) {
          breakdown.queue += size;
        } else {
          breakdown.other += size;
        }
      });

      return {
        totalSize: totalSize,
        totalSizeFormatted: this.formatBytes(totalSize),
        entryCount: entryCount,
        maxSize: this.maxCacheSize,
        maxSizeFormatted: this.formatBytes(this.maxCacheSize),
        usagePercent: ((totalSize / this.maxCacheSize) * 100).toFixed(2),
        breakdown: breakdown,
        breakdownFormatted: {
          collection: this.formatBytes(breakdown.collection),
          spotify: this.formatBytes(breakdown.spotify),
          preferences: this.formatBytes(breakdown.preferences),
          queue: this.formatBytes(breakdown.queue),
          other: this.formatBytes(breakdown.other)
        }
      };
    } catch (error) {
      console.error('Cache stats error:', error);
      return null;
    }
  }

  /**
   * Check storage quota
   */
  async checkStorageQuota() {
    if ('storage' in navigator && 'estimate' in navigator.storage) {
      try {
        const estimate = await navigator.storage.estimate();
        const percentUsed = ((estimate.usage / estimate.quota) * 100).toFixed(2);

        console.log(`Storage: ${this.formatBytes(estimate.usage)} / ${this.formatBytes(estimate.quota)} (${percentUsed}%)`);

        // Warn if over 80%
        if (percentUsed > 80) {
          console.warn('Storage quota over 80% - consider clearing cache');
          if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
            window.MusicLocker.showToast(
              'Storage is almost full. Consider clearing old cache in settings.',
              'warning'
            );
          }
        }
      } catch (error) {
        console.error('Storage estimate error:', error);
      }
    }
  }

  // ============================================
  // UTILITY METHODS
  // ============================================

  /**
   * Simple string hash function
   */
  hashString(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
      const char = str.charCodeAt(i);
      hash = ((hash << 5) - hash) + char;
      hash = hash & hash; // Convert to 32-bit integer
    }
    return Math.abs(hash).toString(36);
  }

  /**
   * Format bytes to human readable
   */
  formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  /**
   * Check if currently online
   */
  isOnline() {
    return navigator.onLine;
  }

  /**
   * Check if currently offline
   */
  isOffline() {
    return !navigator.onLine;
  }

  /**
   * Export cache data (for debugging/backup)
   */
  exportCache() {
    try {
      const cacheData = {};
      const keys = Object.keys(localStorage);

      keys.forEach(fullKey => {
        if (fullKey.startsWith(this.storagePrefix)) {
          cacheData[fullKey] = localStorage.getItem(fullKey);
        }
      });

      return JSON.stringify(cacheData, null, 2);
    } catch (error) {
      console.error('Cache export error:', error);
      return null;
    }
  }
}

// Initialize global instance
window.MusicLockerOffline = new OfflineManager();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = OfflineManager;
}

console.log('Offline Manager loaded');
