@extends('layouts.app')

@section('title', 'My Music Collection')

@section('content')
<!-- Music Collection Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- Header & Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">
                        <i class="bi bi-collection me-2"></i>My Music Collection
                        @if(isset($stats))
                            <small class="text-muted ms-2">({{ $stats['total_entries'] ?? 0 }} tracks)</small>
                        @endif
                    </h1>
                    <a href="{{ route('music.create') }}" class="btn btn-glow">
                        <i class="bi bi-plus-circle me-2"></i>Add Music
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="feature-card mb-4">
            <form method="GET" action="{{ route('music.index') }}" id="musicFilterForm">
                <!-- Primary Search Row -->
                <div class="row g-2 mb-3">
                    <div class="col-md-8 col-lg-9">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search by title, artist, album, or tags..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-glow flex-grow-1" id="toggleAdvancedFilters">
                                <i class="bi bi-funnel me-1"></i>Filters
                                @php
                                $activeFiltersCount = 0;
                                if (!empty(request('genre'))) $activeFiltersCount++;
                                if (!empty(request('tag_id'))) $activeFiltersCount++;
                                if (!empty(request('mood'))) $activeFiltersCount++;
                                if (!empty(request('rating'))) $activeFiltersCount++;
                                if (!empty(request('sort_by')) && request('sort_by') !== 'created_at') $activeFiltersCount++;
                                @endphp
                                @if($activeFiltersCount > 0)
                                    <span class="badge bg-glow">{{ $activeFiltersCount }}</span>
                                @endif
                            </button>
                            <button type="submit" class="btn btn-glow">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Filters (Collapsible) -->
                <div id="advancedFilters" class="{{ $activeFiltersCount > 0 ? '' : 'collapse' }}">
                    <div class="row g-2 mb-3 pb-3 border-bottom border-secondary">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Genre</label>
                            <select class="form-select form-select-sm" name="genre">
                                <option value="">All Genres</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre }}" {{ request('genre') == $genre ? 'selected' : '' }}>
                                        {{ $genre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Mood</label>
                            <select class="form-select form-select-sm" name="mood">
                                <option value="">All Moods</option>
                                @if(!empty($moodTags))
                                    @foreach($moodTags as $tag)
                                        <option value="{{ $tag->id }}" {{ request('mood') == $tag->id ? 'selected' : '' }}>
                                            {{ preg_replace('/^Mood:\s*/i', '', $tag->name) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Tag</label>
                            <select class="form-select form-select-sm" name="tag_id">
                                <option value="">All Tags</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Rating</label>
                            <select class="form-select form-select-sm" name="rating">
                                <option value="">All Ratings</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                        {{ $i }} Stars
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Sort By</label>
                            <select class="form-select form-select-sm" name="sort_by">
                                <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Recently Added</option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                                <option value="artist" {{ request('sort_by') == 'artist' ? 'selected' : '' }}>Artist (A-Z)</option>
                                <option value="personal_rating" {{ request('sort_by') == 'personal_rating' ? 'selected' : '' }}>Rating (High-Low)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Row -->
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="favorite" id="favorites"
                                   {{ request('favorite') ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label" for="favorites">
                                <i class="bi bi-heart-fill text-danger me-1"></i>Favorites Only
                            </label>
                        </div>
                        
                        @if(request()->hasAny(['search', 'genre', 'rating', 'tag_id', 'mood', 'favorite']) || (!empty(request('sort_by')) && request('sort_by') !== 'created_at'))
                            <div class="ms-auto">
                                <a href="{{ route('music.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle me-1"></i>Clear All Filters
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>


        <!-- Music Entries -->
        @if($entries->count() > 0)
            <div class="row">
                @foreach($entries as $entry)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="feature-card h-100">
                            <!-- Album Art -->
                            @if($entry->album_art_url)
                                <img src="{{ $entry->album_art_url }}" class="card-img-top" 
                                     alt="{{ $entry->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="bi bi-music-note display-4 text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="p-3">
                                <h6 class="fw-bold mb-1">{{ $entry->title }}</h6>
                                <p class="text-muted small mb-2">{{ $entry->artist }}</p>
                                
                                @if($entry->album)
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-disc me-1"></i>{{ $entry->album }}
                                    </p>
                                @endif
                                
                                <!-- Tags -->
                                @if($entry->tags && $entry->tags->count() > 0)
                                    <div class="mb-2 d-flex flex-wrap gap-1">
                                        @foreach($entry->tags as $tag)
                                            <span class="badge" style="background-color: {{ $tag->color }};">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Rating & Favorite -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    @if($entry->personal_rating)
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $entry->personal_rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    @else
                                        <span class="text-muted small">Not rated</span>
                                    @endif
                                    
                                    <button class="btn btn-sm favorite-btn" data-entry-id="{{ $entry->id }}"
                                            data-is-favorite="{{ $entry->is_favorite ? '1' : '0' }}"
                                            title="{{ $entry->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                                        <i class="bi bi-heart{{ $entry->is_favorite ? '-fill text-danger' : '' }}"></i>
                                    </button>
                                </div>
                                
                                <!-- Actions -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('music.show', $entry->id) }}" class="btn btn-outline-glow btn-sm">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('music.edit', $entry->id) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        <form action="{{ route('music.destroy', $entry->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this track?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="feature-card text-center py-5">
                <i class="bi bi-music-note-beamed display-1 mb-4" 
                   style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <h3 class="mb-3">No Music Found</h3>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'genre', 'rating', 'tag_id', 'mood', 'favorite']))
                        No tracks match your current filters. Try adjusting your search criteria.
                    @else
                        Your music collection is empty. Start building your personal library by adding your first track!
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request()->hasAny(['search', 'genre', 'rating', 'tag_id', 'mood', 'favorite']))
                        <a href="{{ route('music.index') }}" class="btn btn-outline-glow">
                            <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('music.create') }}" class="btn btn-glow">
                        <i class="bi bi-plus-circle me-2"></i>Add Music
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete "<span id="deleteTitle"></span>" from your collection?</p>
                    <p class="text-warning small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="{{ asset('js/music.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle advanced filters
    const toggleBtn = document.getElementById('toggleAdvancedFilters');
    const advancedFilters = document.getElementById('advancedFilters');
    
    if (toggleBtn && advancedFilters) {
        toggleBtn.addEventListener('click', function() {
            const bsCollapse = new bootstrap.Collapse(advancedFilters, {
                toggle: true
            });
            
            // Toggle icon
            const icon = this.querySelector('i');
            advancedFilters.addEventListener('shown.bs.collapse', function() {
                icon.classList.remove('bi-funnel');
                icon.classList.add('bi-funnel-fill');
            });
            advancedFilters.addEventListener('hidden.bs.collapse', function() {
                icon.classList.remove('bi-funnel-fill');
                icon.classList.add('bi-funnel');
            });
        });
    }
    
    // Auto-submit on filter change for better UX
    const filterSelects = document.querySelectorAll('#advancedFilters select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
@endsection
