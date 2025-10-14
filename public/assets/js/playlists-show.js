/**
 * Handles playlist management and track removal
 */

document.addEventListener('DOMContentLoaded', function () {
  // Delete playlist
  const deletePlaylistBtns = document.querySelectorAll('.delete-playlist-btn');
  deletePlaylistBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      const playlistId = this.dataset.playlistId;
      const playlistName = this.dataset.name;

      document.getElementById('deletePlaylistName').textContent = playlistName;
      document.getElementById('deletePlaylistForm').action =
        '/playlists/' + playlistId + '/delete';

      const modal = new bootstrap.Modal(
        document.getElementById('deletePlaylistModal')
      );
      modal.show();
    });
  });

  // Remove track from playlist
  const removeTrackBtns = document.querySelectorAll('.remove-track-btn');
  removeTrackBtns.forEach((btn) => {
    btn.addEventListener('click', async function () {
      if (!confirm('Remove "' + this.dataset.title + '" from this playlist?')) {
        return;
      }

      const playlistId = this.dataset.playlistId;
      const entryId = this.dataset.entryId;
      const csrfToken = this.dataset.csrf;

      try {
        const response = await fetch('/playlists/remove-track', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            playlist_id: playlistId,
            entry_id: entryId,
            csrf_token: csrfToken,
          }),
        });

        const data = await response.json();

        if (data.success) {
          // Remove the track from the DOM
          this.closest('.list-group-item').remove();

          // Show success message
          alert('Track removed from playlist');

          // Reload page to update counts
          location.reload();
        } else {
          alert('Failed to remove track: ' + (data.error || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Failed to remove track');
      }
    });
  });
});

