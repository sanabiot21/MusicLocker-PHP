-- Additional composite indexes for PostgreSQL optimization
-- Music Locker - Team NaturalStupidity
-- Created to address N+1 query issues and improve query performance

-- Composite index for user music entries sorted by date (very common query pattern)
CREATE INDEX IF NOT EXISTS idx_music_entries_user_date ON music_entries (user_id, date_added DESC);

-- Composite index for user music entries with favorites filter
CREATE INDEX IF NOT EXISTS idx_music_entries_user_favorite ON music_entries (user_id, is_favorite) WHERE is_favorite = TRUE;

-- Composite index for user music entries by genre (common filter)
CREATE INDEX IF NOT EXISTS idx_music_entries_user_genre ON music_entries (user_id, genre);

-- Composite index for tag lookups in music_entry_tags (optimizes tag filtering)
CREATE INDEX IF NOT EXISTS idx_music_entry_tags_tag_entry ON music_entry_tags (tag_id, music_entry_id);
