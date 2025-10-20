/**
 * Sync Queue Manager - Handles Offline Action Queue
 * Music Locker - Team NaturalStupidity
 *
 * Queues user actions when offline and syncs them when back online
 */

class SyncQueue {
  constructor() {
    this.queueKey = 'musiclocker_offline_queue';
    this.queue = this.loadQueue();
    this.syncing = false;
    this.syncInProgress = new Set(); // Track actions currently syncing

    // Retry configuration (from Spotify config)
    this.retryConfig = {
      maxRetries: 3,
      backoffMultiplier: 2,
      maxBackoff: 60, // seconds
      retryStatusCodes: [429, 500, 502, 503, 504]
    };

    // Initialize
    this.init();
  }

  /**
   * Initialize sync queue
   */
  init() {
    try {
      // Set up listeners
      this.setupListeners();

      // Auto-sync on initialization if online
      if (navigator.onLine && this.queue.length > 0) {
        console.log(`Sync Queue: ${this.queue.length} pending actions found`);
        setTimeout(() => this.processQueue(), 1000); // Wait 1 second before syncing
      }

      console.log('Sync Queue initialized');
    } catch (error) {
      console.error('Failed to initialize Sync Queue:', error);
    }
  }

  /**
   * Setup event listeners
   */
  setupListeners() {
    // Listen for online event
    window.addEventListener('musiclocker:online', () => {
      console.log('Sync Queue: Network back online, processing queue...');
      this.processQueue();
    });

    // Periodic cleanup of old failed items
    setInterval(() => this.cleanupOldItems(), 5 * 60 * 1000); // Every 5 minutes
  }

  // ============================================
  // QUEUE OPERATIONS
  // ============================================

  /**
   * Load queue from localStorage
   */
  loadQueue() {
    try {
      if (window.MusicLockerOffline) {
        return window.MusicLockerOffline.get(this.queueKey) || [];
      } else {
        const stored = localStorage.getItem('musiclocker_' + this.queueKey);
        return stored ? JSON.parse(stored) : [];
      }
    } catch (error) {
      console.error('Failed to load queue:', error);
      return [];
    }
  }

  /**
   * Save queue to localStorage
   */
  saveQueue() {
    try {
      if (window.MusicLockerOffline) {
        window.MusicLockerOffline.set(this.queueKey, this.queue, null);
      } else {
        localStorage.setItem('musiclocker_' + this.queueKey, JSON.stringify(this.queue));
      }
      return true;
    } catch (error) {
      console.error('Failed to save queue:', error);
      return false;
    }
  }

  /**
   * Add action to queue
   */
  addAction(action, payload, options = {}) {
    const queueItem = {
      id: this.generateId(),
      action: action,
      payload: payload,
      timestamp: Date.now(),
      retries: 0,
      status: 'pending',
      priority: options.priority || 'normal', // high, normal, low
      metadata: options.metadata || {}
    };

    this.queue.push(queueItem);
    this.saveQueue();

    console.log(`Queued action: ${action}`, queueItem);

    // Trigger event
    this.triggerEvent('action-queued', { action: queueItem });

    // Try to sync immediately if online
    if (navigator.onLine) {
      this.processQueue();
    }

    return queueItem.id;
  }

  /**
   * Remove action from queue
   */
  removeAction(id) {
    const index = this.queue.findIndex(item => item.id === id);
    if (index !== -1) {
      const removed = this.queue.splice(index, 1)[0];
      this.saveQueue();
      this.triggerEvent('action-removed', { action: removed });
      return true;
    }
    return false;
  }

  /**
   * Get action by ID
   */
  getAction(id) {
    return this.queue.find(item => item.id === id);
  }

  /**
   * Update action status
   */
  updateAction(id, updates) {
    const action = this.getAction(id);
    if (action) {
      Object.assign(action, updates);
      this.saveQueue();
      return true;
    }
    return false;
  }

  // ============================================
  // SYNC PROCESSING
  // ============================================

  /**
   * Process entire queue
   */
  async processQueue() {
    if (this.syncing) {
      console.log('Sync already in progress');
      return;
    }

    if (!navigator.onLine) {
      console.log('Cannot sync - offline');
      return;
    }

    if (this.queue.length === 0) {
      console.log('Queue is empty');
      return;
    }

    this.syncing = true;
    this.triggerEvent('sync-started', { count: this.queue.length });

    console.log(`Processing ${this.queue.length} queued actions...`);

    // Sort by priority and timestamp
    const sortedQueue = this.queue.sort((a, b) => {
      const priorityOrder = { high: 1, normal: 2, low: 3 };
      if (priorityOrder[a.priority] !== priorityOrder[b.priority]) {
        return priorityOrder[a.priority] - priorityOrder[b.priority];
      }
      return a.timestamp - b.timestamp;
    });

    let successCount = 0;
    let failCount = 0;

    for (const item of sortedQueue) {
      if (item.status === 'completed') continue;
      if (this.syncInProgress.has(item.id)) continue;

      try {
        this.syncInProgress.add(item.id);
        await this.syncAction(item);
        successCount++;
      } catch (error) {
        console.error(`Failed to sync action ${item.action}:`, error);
        failCount++;
      } finally {
        this.syncInProgress.delete(item.id);
      }

      // Small delay between actions to avoid overwhelming server
      await this.sleep(200);
    }

    this.syncing = false;

    console.log(`Sync complete: ${successCount} succeeded, ${failCount} failed`);

    this.triggerEvent('sync-completed', { success: successCount, failed: failCount });

    // Show notification
    if (window.MusicLocker && window.MusicLocker.showToast) {
      if (successCount > 0) {
        window.MusicLocker.showToast(
          `Synced ${successCount} offline action${successCount > 1 ? 's' : ''}`,
          'success'
        );
      }
      if (failCount > 0) {
        window.MusicLocker.showToast(
          `${failCount} action${failCount > 1 ? 's' : ''} failed to sync`,
          'error'
        );
      }
    }
  }

  /**
   * Sync individual action
   */
  async syncAction(item) {
    try {
      let result;

      switch (item.action) {
        case 'toggle_favorite':
          result = await this.syncToggleFavorite(item.payload);
          break;

        case 'update_rating':
          result = await this.syncUpdateRating(item.payload);
          break;

        case 'delete_entry':
          result = await this.syncDeleteEntry(item.payload);
          break;

        case 'add_entry':
          result = await this.syncAddEntry(item.payload);
          break;

        case 'update_entry':
          result = await this.syncUpdateEntry(item.payload);
          break;

        case 'add_note':
          result = await this.syncAddNote(item.payload);
          break;

        case 'add_tag':
          result = await this.syncAddTag(item.payload);
          break;

        default:
          console.warn(`Unknown action type: ${item.action}`);
          this.removeAction(item.id);
          return;
      }

      // Mark as completed and remove from queue
      this.updateAction(item.id, { status: 'completed', completedAt: Date.now() });
      this.removeAction(item.id);

      this.triggerEvent('action-synced', { action: item, result: result });

      return result;
    } catch (error) {
      // Handle retry logic
      item.retries++;

      if (item.retries >= this.retryConfig.maxRetries) {
        // Max retries reached, mark as failed
        this.updateAction(item.id, {
          status: 'failed',
          error: error.message,
          failedAt: Date.now()
        });

        console.error(`Action ${item.action} failed after ${item.retries} retries:`, error);
      } else {
        // Schedule retry with exponential backoff
        const backoffDelay = Math.min(
          Math.pow(this.retryConfig.backoffMultiplier, item.retries) * 1000,
          this.retryConfig.maxBackoff * 1000
        );

        console.log(`Retry ${item.retries}/${this.retryConfig.maxRetries} for ${item.action} in ${backoffDelay}ms`);

        this.updateAction(item.id, {
          status: 'pending',
          nextRetry: Date.now() + backoffDelay
        });
      }

      throw error;
    }
  }

  // ============================================
  // SYNC HANDLERS FOR EACH ACTION TYPE
  // ============================================

  /**
   * Sync toggle favorite action
   */
  async syncToggleFavorite(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch('/api/music/favorite', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        entry_id: payload.entry_id,
        csrf_token: csrfToken
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (!data.success) {
      throw new Error(data.error || 'Failed to toggle favorite');
    }

    return data;
  }

  /**
   * Sync update rating action
   */
  async syncUpdateRating(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(`/music/${payload.entry_id}/rate`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        rating: payload.rating,
        csrf_token: csrfToken
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  /**
   * Sync delete entry action
   */
  async syncDeleteEntry(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(`/music/${payload.entry_id}/delete`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        csrf_token: csrfToken
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  /**
   * Sync add entry action
   */
  async syncAddEntry(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const formData = new FormData();
    Object.keys(payload).forEach(key => {
      if (payload[key] !== null && payload[key] !== undefined) {
        formData.append(key, payload[key]);
      }
    });
    formData.append('csrf_token', csrfToken);

    const response = await fetch('/music/add', {
      method: 'POST',
      headers: {
        'X-CSRF-Token': csrfToken
      },
      body: formData
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.text(); // Returns HTML redirect or success page
  }

  /**
   * Sync update entry action
   */
  async syncUpdateEntry(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const formData = new FormData();
    Object.keys(payload).forEach(key => {
      if (key !== 'entry_id' && payload[key] !== null) {
        formData.append(key, payload[key]);
      }
    });
    formData.append('csrf_token', csrfToken);

    const response = await fetch(`/music/${payload.entry_id}/edit`, {
      method: 'POST',
      headers: {
        'X-CSRF-Token': csrfToken
      },
      body: formData
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.text();
  }

  /**
   * Sync add note action
   */
  async syncAddNote(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(`/music/${payload.entry_id}/notes`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        note_text: payload.note_text,
        mood: payload.mood,
        csrf_token: csrfToken
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  /**
   * Sync add tag action
   */
  async syncAddTag(payload) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(`/music/${payload.entry_id}/tags`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        tag_id: payload.tag_id,
        csrf_token: csrfToken
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  // ============================================
  // QUEUE MANAGEMENT
  // ============================================

  /**
   * Get pending count
   */
  getPendingCount() {
    return this.queue.filter(item => item.status === 'pending').length;
  }

  /**
   * Get failed count
   */
  getFailedCount() {
    return this.queue.filter(item => item.status === 'failed').length;
  }

  /**
   * Get completed count (still in queue)
   */
  getCompletedCount() {
    return this.queue.filter(item => item.status === 'completed').length;
  }

  /**
   * Clear completed items
   */
  clearCompleted() {
    const before = this.queue.length;
    this.queue = this.queue.filter(item => item.status !== 'completed');
    this.saveQueue();
    const removed = before - this.queue.length;
    console.log(`Cleared ${removed} completed items`);
    return removed;
  }

  /**
   * Clear failed items
   */
  clearFailed() {
    const before = this.queue.length;
    this.queue = this.queue.filter(item => item.status !== 'failed');
    this.saveQueue();
    const removed = before - this.queue.length;
    console.log(`Cleared ${removed} failed items`);
    return removed;
  }

  /**
   * Clear entire queue
   */
  clearAll() {
    const count = this.queue.length;
    this.queue = [];
    this.saveQueue();
    console.log(`Cleared entire queue (${count} items)`);
    this.triggerEvent('queue-cleared');
    return count;
  }

  /**
   * Cleanup old items (over 7 days old)
   */
  cleanupOldItems() {
    const sevenDaysAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
    const before = this.queue.length;

    this.queue = this.queue.filter(item => {
      // Keep pending items regardless of age
      if (item.status === 'pending') return true;

      // Remove old completed/failed items
      return item.timestamp > sevenDaysAgo;
    });

    const removed = before - this.queue.length;
    if (removed > 0) {
      this.saveQueue();
      console.log(`Cleaned up ${removed} old queue items`);
    }
  }

  /**
   * Retry all failed items
   */
  retryFailed() {
    const failedItems = this.queue.filter(item => item.status === 'failed');

    failedItems.forEach(item => {
      this.updateAction(item.id, {
        status: 'pending',
        retries: 0,
        error: null
      });
    });

    console.log(`Reset ${failedItems.length} failed items for retry`);

    if (failedItems.length > 0 && navigator.onLine) {
      this.processQueue();
    }
  }

  // ============================================
  // UTILITY METHODS
  // ============================================

  /**
   * Generate unique ID
   */
  generateId() {
    return 'sync_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  }

  /**
   * Trigger custom event
   */
  triggerEvent(eventName, detail = {}) {
    window.dispatchEvent(new CustomEvent(`musiclocker:sync:${eventName}`, { detail }));
  }

  /**
   * Sleep utility
   */
  sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  /**
   * Get queue summary
   */
  getSummary() {
    return {
      total: this.queue.length,
      pending: this.getPendingCount(),
      failed: this.getFailedCount(),
      completed: this.getCompletedCount(),
      syncing: this.syncing
    };
  }

  /**
   * Export queue (for debugging)
   */
  exportQueue() {
    return JSON.stringify(this.queue, null, 2);
  }
}

// Initialize global instance
window.MusicLockerSyncQueue = new SyncQueue();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = SyncQueue;
}

console.log('Sync Queue Manager loaded');
