# Supabase Timezone Configuration Guide

**Date:** 2025-10-21
**Status:** ✅ UTC Storage + Manila Display (Correct Architecture)

## Understanding Supabase Timezone Architecture

### Current Setup (Correct ✓)

```
Supabase (PostgreSQL) → Stores in UTC
                    ↓ (PHP reads as UTC)
                    ↓ (Converts to Manila)
PHP Application    → Displays in Manila timezone
```

### Why This Is Correct

1. **Database stores in UTC** - Best practice for any timezone-aware application
2. **PHP converts for display** - Application layer handles timezone conversion
3. **No timezone info lost** - UTC is unambiguous, works globally

## How Timestamps Work

### Storage in Supabase (UTC)
```sql
-- All timestamps stored in UTC by default
INSERT INTO users (created_at) VALUES (CURRENT_TIMESTAMP);
-- Result: 2025-10-21 04:30:00 UTC (stored)
```

### Display in PHP (Manila)
```php
// PHP reads UTC timestamp
$created_at = '2025-10-21 04:30:00';

// Converts to Manila timezone
$formatted = format_timestamp($created_at);
// Result: Oct 21, 2025 12:30 PM (Manila time)
```

### Conversion Process
```
Database: 2025-10-21 04:30:00 (UTC)
          ↓ +8 hours
Manila:   2025-10-21 12:30:00 (UTC+8)
```

## Supabase SQL Functions (NEW)

Added 3 helpful SQL functions for Supabase:

### 1. `current_timestamp_manila()`
Returns current timestamp in Manila timezone.

```sql
-- Returns current time in Manila (not UTC)
SELECT current_timestamp_manila();
-- Result: 2025-10-21 12:30:00+08:00
```

**Use case:** When you need Manila time directly in SQL

### 2. `to_manila_timezone(ts)`
Converts any timestamp to Manila timezone.

```sql
-- Convert a UTC timestamp to Manila
SELECT to_manila_timezone('2025-10-21 04:30:00+00:00');
-- Result: 2025-10-21 12:30:00+08:00
```

**Use case:** Viewing timestamps in SQL

### 3. `to_utc_timezone(ts)`
Converts any timestamp back to UTC.

```sql
-- Convert a Manila timestamp to UTC
SELECT to_utc_timezone('2025-10-21 12:30:00+08:00');
-- Result: 2025-10-21 04:30:00+00:00
```

**Use case:** Saving Manila time as UTC

## Recommended SQL Queries

### Get Today's Records (Manila Timezone)
```sql
-- Query records created "today" in Manila time
SELECT * FROM users
WHERE created_at::date = current_timestamp_manila()::date;
```

### Compare Times with Manila Timezone
```sql
-- Find records created in the last 7 days (Manila time)
SELECT * FROM activity_log
WHERE created_at > (current_timestamp_manila() - INTERVAL '7 days');
```

### Display Timestamps in Manila
```sql
-- Select with Manila timezone conversion
SELECT
    id,
    user_id,
    to_manila_timezone(created_at) as created_in_manila,
    created_at as created_in_utc
FROM activity_log;
```

## PHP + Supabase Integration

### PHP Handles All Timezone Conversion

**Key insight:** We handle timezone conversion in PHP, not in database queries!

```php
// Database returns UTC timestamps
$dbRow = $db->queryOne("SELECT * FROM users WHERE id = 1");
$created_at = $dbRow['created_at']; // UTC: 2025-10-21 04:30:00

// PHP converts to Manila for display
$formatted = format_timestamp($created_at);
// Result: Oct 21, 2025 12:30 PM (Manila)

// Time ago calculations also use Manila timezone
$timeAgo = format_time_ago($created_at);
// Result: "11 hours ago" (correctly calculated in Manila)
```

### Database Queries Don't Need Timezone Conversion

❌ **DON'T do this:**
```php
// Unnecessary - conversions happen in PHP
$sql = "SELECT to_manila_timezone(created_at) FROM users";
```

✅ **DO this instead:**
```php
// Simple - PHP handles conversion
$sql = "SELECT created_at FROM users";
$timestamp = $result['created_at']; // PHP formats it
$formatted = format_timestamp($timestamp);
```

## Migration for Timezone Functions

Run this migration to add the SQL functions to Supabase:

```bash
php artisan migrate
```

Or manually in Supabase SQL editor:
```sql
CREATE OR REPLACE FUNCTION current_timestamp_manila()
RETURNS TIMESTAMP WITH TIME ZONE AS $$
BEGIN
    RETURN CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Manila';
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION to_manila_timezone(ts TIMESTAMP WITH TIME ZONE)
RETURNS TIMESTAMP WITH TIME ZONE AS $$
BEGIN
    RETURN ts AT TIME ZONE 'Asia/Manila';
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION to_utc_timezone(ts TIMESTAMP WITH TIME ZONE)
RETURNS TIMESTAMP WITH TIME ZONE AS $$
BEGIN
    RETURN ts AT TIME ZONE 'UTC';
END;
$$ LANGUAGE plpgsql IMMUTABLE;
```

## File Location

Migration file: `laravel/database/migrations/2025_10_21_000000_add_timezone_functions.php`

## Timezone Flow Chart

```
┌─────────────────────────────────────────────────┐
│         SUPABASE (PostgreSQL)                    │
│  ┌────────────────────────────────────────────┐ │
│  │  Tables store timestamps in UTC:           │ │
│  │  • users.created_at = 2025-10-21 04:30:00  │ │
│  │  • activity_log.created_at = UTC           │ │
│  │  • music_entries.date_added = UTC          │ │
│  └────────────────────────────────────────────┘ │
│         ↓ (PHP reads via Database service)       │
└─────────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────────┐
│         PHP APPLICATION                          │
│  ┌────────────────────────────────────────────┐ │
│  │  Conversions happen here:                  │ │
│  │  • format_timestamp() → Manila timezone   │ │
│  │  • format_time_ago() → Manila timezone    │ │
│  │  • DateTime with DateTimeZone             │ │
│  └────────────────────────────────────────────┘ │
│         ↓ (Displays to user)                     │
└─────────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────────┐
│         BROWSER / USER                           │
│  ┌────────────────────────────────────────────┐ │
│  │  User sees Manila timezone:                │ │
│  │  • "Oct 21, 2025 12:30 PM"                │ │
│  │  • "11 hours ago"                         │ │
│  │  • All times in UTC+8                     │ │
│  └────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

## Common Issues & Solutions

### Issue: Supabase showing UTC times
**Solution:** This is correct! Supabase should store UTC. Check PHP conversion code.

### Issue: Timestamps are 8 hours off
**Solution:** Check that `format_timestamp()` and `format_time_ago()` are being used in views.

### Issue: SQL queries returning wrong times
**Solution:** Don't convert in SQL. Return UTC from SQL, convert in PHP.

### Issue: Time ago shows wrong values
**Solution:** Verify `APP_TIMEZONE=Asia/Manila` is set in `.env`

## Best Practices

1. ✅ **Store in UTC** - Supabase default, don't change
2. ✅ **Convert in PHP** - Use helper functions
3. ✅ **Use helper functions** - `format_timestamp()`, `format_time_ago()`
4. ✅ **Set APP_TIMEZONE** - Ensure `.env` has `APP_TIMEZONE=Asia/Manila`
5. ✅ **Use DateTime with timezone** - In HelperFunctions, use `DateTimeZone`

❌ **Don't:**
- Hardcode timezone in queries
- Store Manila time in database
- Use CURRENT_TIMESTAMP for Manila times
- Skip PHP conversion

## Summary

**Supabase Setup:** ✅ Correct
- Stores timestamps in UTC
- PHP reads and converts to Manila
- Helper functions handle conversion
- SQL functions available for advanced queries

**Result:**
- Admin sees correct times in Manila timezone
- Time ago calculations are accurate
- No timezone offset issues
- Works correctly globally

---

**Supabase timezone architecture is correct and properly configured!** ✅
