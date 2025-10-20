@extends('layouts.app')

@section('title', 'System Health')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">System Health</h1>
        <p class="page-description">Monitor system status and database statistics</p>
    </div>

    <div class="admin-grid">
        <!-- Database Statistics -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Database Statistics</h2>
            </div>
            <div class="card-body">
                <div class="stats-list">
                    <div class="stat-item">
                        <div class="stat-value">{{ formatNumber($dbStats['users']) }}</div>
                        <div class="stat-label">Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ formatNumber($dbStats['music_entries']) }}</div>
                        <div class="stat-label">Music Entries</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ formatNumber($dbStats['playlists']) }}</div>
                        <div class="stat-label">Playlists</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ formatNumber($dbStats['tags']) }}</div>
                        <div class="stat-label">Tags</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ formatNumber($dbStats['activity_logs']) }}</div>
                        <div class="stat-label">Activity Logs</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Server Information -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Server Information</h2>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">PHP Version:</span>
                        <span class="info-value">{{ $serverInfo['php_version'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Laravel Version:</span>
                        <span class="info-value">{{ $serverInfo['laravel_version'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Database:</span>
                        <span class="info-value">{{ ucfirst($serverInfo['database']) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">DB Connection:</span>
                        <span class="badge badge-{{ str_contains($serverInfo['db_connection'], 'Connected') ? 'success' : 'danger' }}">
                            {{ $serverInfo['db_connection'] }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Environment:</span>
                        <span class="badge badge-{{ $serverInfo['environment'] === 'production' ? 'success' : 'warning' }}">
                            {{ ucfirst($serverInfo['environment']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
