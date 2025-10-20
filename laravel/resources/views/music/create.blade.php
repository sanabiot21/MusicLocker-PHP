@extends('layouts.app')

@section('title', 'Add New Music')

@section('content')<!-- Add Music Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-plus-circle me-2"></i>Add Music</h1>
                    <a href="{{ route('music.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Collection
                    </a>
                </div>

                <!-- Add Form -->
                <div class="feature-card">
                    <!-- Header -->
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="bi bi-pencil me-2"></i>Add Music Entry
                            </h5>
                            <button type="button" class="btn btn-outline-glow" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="bi bi-search me-2"></i>Search Online
                            </button>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>Search online to auto-fill track details, or enter information manually below.
                        </p>
                    </div>
                    
                    <form method="POST" action="{{ route('music.store') }}" id="addMusicForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Track Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label for="artist" class="form-label">Artist *</label>
                                <input type="text" class="form-control" id="artist" name="artist" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="album" class="form-label">Album</label>
                                <input type="text" class="form-control" id="album" name="album">
                            </div>
                            <div class="col-md-3">
                                <label for="genre" class="form-label">Genre *</label>
                                <input type="text" class="form-control" id="genre" name="genre" value="Unknown" required>
                            </div>
                            <div class="col-md-3">
                                <label for="release_year" class="form-label">Year</label>
                                <input type="number" class="form-control" id="release_year" name="release_year" min="1900" max="{{ date('Y') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="personal_rating" class="form-label">Personal Rating *</label>
                                <select class="form-select" id="personal_rating" name="personal_rating" required>
                                    <option value="">Select Rating</option>
                                    <option value="1">1 Star</option>
                                    <option value="2">2 Stars</option>
                                    <option value="3" selected>3 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="5">5 Stars</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="duration" class="form-label">Duration (seconds)</label>
                                <input type="number" class="form-control" id="duration" name="duration" min="1">
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite" value="1">
                                    <label class="form-check-label" for="is_favorite">
                                        <i class="bi bi-heart me-1"></i>Add to Favorites
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Tags Selection -->
                            @if($tags && count($tags) > 0)
                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-tags me-1"></i>Tags (Optional)
                                    </label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($tags as $tag)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tags[]" 
                                                       value="{{ $tag->id }}" id="tag_{{ $tag->id }}">
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                    <span class="badge" style="background-color: {{ $tag->color }};">
                                                        {{ $tag->name }}
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Hidden Spotify fields -->
                            <input type="hidden" id="spotify_id" name="spotify_id">
                            <input type="hidden" id="spotify_url" name="spotify_url">
                            <input type="hidden" id="album_art_url" name="album_art_url">
                            
                            <!-- Personal Notes Section -->
                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Personal Notes (Optional)</h6>
                            </div>
                            
                            <div class="col-12">
                                <label for="note_text" class="form-label">Notes & Thoughts</label>
                                <textarea class="form-control" id="note_text" name="note_text" rows="3" 
                                          placeholder="Your thoughts, memories, or why you love this song..."></textarea>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="mood" class="form-label">Mood</label>
                                <input type="text" class="form-control" id="mood" name="mood" 
                                       placeholder="e.g., Happy, Nostalgic">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="memory_context" class="form-label">Memory Context</label>
                                <input type="text" class="form-control" id="memory_context" name="memory_context" 
                                       placeholder="e.g., Summer 2020">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="listening_context" class="form-label">Listening Context</label>
                                <input type="text" class="form-control" id="listening_context" name="listening_context" 
                                       placeholder="e.g., Workout, Study">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('music.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-glow">
                                <i class="bi bi-plus-circle me-2"></i>Add to Collection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Music Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content bg-dark">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="bi bi-search me-2"></i>Search Music Database
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Form -->
                    <form method="GET" id="spotifySearchForm" class="mb-4">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="q" id="searchInput" 
                                   placeholder="Search for songs or albums..." autofocus required>
                            <button type="submit" class="btn btn-glow" id="searchSubmitBtn">
                                <i class="bi bi-search" id="searchIcon"></i>
                                <span id="searchText" class="d-none d-md-inline ms-2">Search</span>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Search by track name, artist, or album title
                        </small>
                    </form>

                    <!-- Loading State -->
                    <div id="searchLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Searching music database...</p>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-search display-1 mb-3"></i>
                            <p>Enter a song, artist, or album name to search</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchForm = document.getElementById('spotifySearchForm');
  const searchSubmitBtn = document.getElementById('searchSubmitBtn');
  const searchResults = document.getElementById('searchResults');
  const searchLoading = document.getElementById('searchLoading');
  const searchInput = document.getElementById('searchInput');

  if (searchForm) {
    searchForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const query = searchInput.value.trim();
      if (!query) return;

      // Show loading state
      searchResults.classList.add('d-none');
      searchLoading.classList.remove('d-none');
      searchSubmitBtn.disabled = true;

      try {
        // Make AJAX request to search endpoint
        const response = await fetch(`/api/v1/spotify/search?q=${encodeURIComponent(query)}&type=track,album&limit=20`);
        const data = await response.json();

        // Debug: Log the response structure
        console.log('Spotify API Response:', data);

        let html = '';

        // Display album results if available
        if (data.data && data.data.albums && data.data.albums.items && data.data.albums.items.length > 0) {
          html += '<div class="mb-4">';
          html += '<div class="d-flex justify-content-between align-items-center mb-3">';
          html += '<h6 class="mb-0"><i class="bi bi-disc me-2"></i>Albums</h6>';
          html += '<span class="badge bg-secondary">' + data.data.albums.items.length + ' found</span>';
          html += '</div>';
          html += '<div class="row g-2 mb-3">';

          data.data.albums.items.forEach(album => {
            const albumImage = album.images && album.images[0] ? album.images[0].url : '';
            const artistNames = album.artists.map(a => a.name).join(', ');
            const trackCount = album.total_tracks;

            html += '<div class="col-12">';
            html += '<div class="card bg-secondary album-card">';
            html += '<div class="row g-0">';
            if (albumImage) {
              html += '<div class="col-auto" style="width: 80px;">';
              html += '<img src="' + albumImage + '" class="img-fluid rounded-start" style="width: 80px; height: 80px; object-fit: cover;" alt="' + album.name + '">';
              html += '</div>';
            }
            html += '<div class="col">';
            html += '<div class="card-body p-2">';
            html += '<h6 class="card-title mb-0">' + album.name + '</h6>';
            html += '<p class="card-text text-muted small mb-0">' + artistNames + '</p>';
            html += '<small class="text-muted">' + album.release_date.substring(0, 4) + ' • ' + trackCount + ' tracks</small>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-auto d-flex align-items-center pe-2">';
            html += '<button type="button" class="btn btn-glow btn-sm select-album" data-album-id="' + album.id + '">';
            html += '<i class="bi bi-check-circle"></i>';
            html += '</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
          });

          html += '</div>';
          html += '</div>';
        }

        // Display track results
        if (data.data && data.data.tracks && data.data.tracks.items && data.data.tracks.items.length > 0) {
          html += '<div class="mb-4">';
          html += '<div class="d-flex justify-content-between align-items-center mb-3">';
          html += '<h6 class="mb-0"><i class="bi bi-music-note me-2"></i>Tracks</h6>';
          html += '<span class="badge bg-secondary">' + data.data.tracks.items.length + ' found</span>';
          html += '</div>';
          html += '<div class="row g-2">';

          data.data.tracks.items.forEach(track => {
            const trackImage = track.album && track.album.images && track.album.images[0] ? track.album.images[0].url : '';
            const artistNames = track.artists.map(a => a.name).join(', ');
            const albumName = track.album ? track.album.name : '';
            const trackData = {
              title: track.name,
              artist: artistNames,
              album: albumName,
              spotify_id: track.id,
              spotify_url: track.external_urls.spotify || '',
              album_art_url: trackImage || '',
              duration: Math.round(track.duration_ms / 1000),
              release_year: track.album && track.album.release_date ? track.album.release_date.substring(0, 4) : '',
              genre: 'Unknown'
            };

            html += '<div class="col-12">';
            html += '<div class="card bg-secondary track-card">';
            html += '<div class="row g-0">';
            if (trackImage) {
              html += '<div class="col-auto" style="width: 80px;">';
              html += '<img src="' + trackImage + '" class="img-fluid rounded-start" style="width: 80px; height: 80px; object-fit: cover;" alt="' + track.name + '">';
              html += '</div>';
            }
            html += '<div class="col">';
            html += '<div class="card-body p-2 d-flex align-items-center">';
            html += '<div class="flex-grow-1 me-2" style="min-width: 0;">';
            html += '<h6 class="card-title mb-0 text-truncate">' + track.name + '</h6>';
            html += '<p class="card-text text-muted small mb-0 text-truncate">' + artistNames;
            if (albumName) html += ' • ' + albumName;
            html += '</p>';
            html += '</div>';
            html += '<div>';
            html += '<button type="button" class="btn btn-glow btn-sm select-track" data-track=\'' + JSON.stringify(trackData) + '\' onclick="console.log(\'Button clicked directly!\');">';
            html += '<i class="bi bi-check-circle"></i>';
            html += '</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
          });

          html += '</div>';
          html += '</div>';
        }

        if (!html) {
          html = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No results found for "' + query + '". Try a different search term.</div>';
        }

        searchResults.innerHTML = html;
        searchLoading.classList.add('d-none');
        searchResults.classList.remove('d-none');
      } catch (error) {
        console.error('Search error:', error);
        searchResults.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error searching. Please try again.</div>';
        searchLoading.classList.add('d-none');
        searchResults.classList.remove('d-none');
      } finally {
        searchSubmitBtn.disabled = false;
      }
    });
  }
});

// Handle track selection from search results
document.addEventListener('DOMContentLoaded', function() {
  document.addEventListener('click', function(e) {
    if (e.target.closest('.select-track')) {
      const button = e.target.closest('.select-track');
      const trackData = JSON.parse(button.dataset.track);
      
      // Populate form with track data
      populateFormWithTrack(trackData);
      
      // Close the search modal
      const searchModalElement = document.getElementById('searchModal');
      if (searchModalElement) {
        const modal = bootstrap.Modal.getInstance(searchModalElement);
        if (modal) {
          modal.hide();
        }
      }
      
      // Scroll to form and highlight it
      setTimeout(() => {
        scrollToForm();
        highlightForm();
      }, 300);
    }
  });
  
  function populateFormWithTrack(trackData) {
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
    
    // Focus on personal rating for user to provide their input
    const personalRating = document.getElementById('personal_rating');
    if (personalRating) {
      personalRating.focus();
    }
  }
  
  function scrollToForm() {
    const form = document.getElementById('addMusicForm');
    if (form) {
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
  
  function highlightForm() {
    const form = document.getElementById('addMusicForm');
    if (form) {
      const formCard = form.closest('.card') || form.closest('.feature-card');
      if (formCard) {
        // Add glow effect
        formCard.style.boxShadow = '0 0 30px rgba(0, 212, 255, 0.5)';
        formCard.style.transform = 'scale(1.02)';
        formCard.style.transition = 'all 0.3s ease';
        
        // Remove glow after 2 seconds
        setTimeout(() => {
          formCard.style.boxShadow = '';
          formCard.style.transform = 'scale(1)';
        }, 2000);
      }
    }
  }
});

// SIMPLE TEST - This should definitely work
console.log('🎯 Music create script loaded');
console.log('🎯 Document ready state:', document.readyState);

// Test if we can find the form
setTimeout(() => {
  const form = document.getElementById('addMusicForm');
  console.log('🎯 Form found:', !!form);
  
  if (form) {
    console.log('🎯 Form ID:', form.id);
  }
}, 1000);

// Handle dynamically created track selection buttons - MAIN HANDLER
document.addEventListener('click', function(e) {
  console.log('🔍 Click detected on:', e.target);
  
  const button = e.target.closest('.select-track');
  if (!button) return;
  
  e.preventDefault();
  e.stopPropagation();
  
  console.log('🎵 Track button clicked!');
  const trackDataStr = button.getAttribute('data-track');
  
  if (!trackDataStr) {
    console.error('❌ No data-track attribute found');
    return;
  }
  
  console.log('📊 Track data string:', trackDataStr.substring(0, 100) + '...');
  
  try {
    const trackData = JSON.parse(trackDataStr);
    console.log('✅ Track data parsed:', trackData);
    
    // Set form values
    const fields = {
      title: trackData.title || '',
      artist: trackData.artist || '',
      album: trackData.album || '',
      genre: trackData.genre || 'Unknown',
      release_year: trackData.release_year || '',
      duration: trackData.duration || '',
      spotify_id: trackData.spotify_id || '',
      spotify_url: trackData.spotify_url || '',
      album_art_url: trackData.album_art_url || ''
    };
    
    for (const [fieldId, value] of Object.entries(fields)) {
      const element = document.getElementById(fieldId);
      if (element) {
        element.value = value;
        console.log(`✅ Set ${fieldId} = ${value}`);
      } else {
        console.warn(`⚠️ Element #${fieldId} not found`);
      }
    }
    
    // Close modal
    const searchModalElement = document.getElementById('searchModal');
    if (searchModalElement) {
      const modal = bootstrap.Modal.getInstance(searchModalElement);
      if (modal) {
        console.log('🚪 Hiding modal...');
        modal.hide();
      }
    }
    
    // Scroll and focus
    setTimeout(() => {
      const form = document.getElementById('addMusicForm');
      if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        console.log('📜 Scrolled to form');
      }
      
      const personalRating = document.getElementById('personal_rating');
      if (personalRating) {
        personalRating.focus();
        console.log('🎯 Focused on personal_rating');
      }
    }, 300);
    
  } catch (error) {
    console.error('❌ Error parsing track data:', error);
    console.error('Track data string:', trackDataStr);
  }
}, true);
</script>
@endpush

