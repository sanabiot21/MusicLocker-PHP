@extends('layouts.app')

@section('title', 'System Health')

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-cpu me-2" style="color: var(--accent-blue);"></i>
                System Health
            </h1>
            <p class="page-description">Monitor system status and database statistics</p>
        </div>
    </div>

    <!-- Database Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="feature-card stat-card-colored stat-card-blue text-center">
                <div class="stat-value stat-value-blue">{{ formatNumber($dbStats['users']) }}</div>
                <div class="stat-label">Users</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="feature-card stat-card-colored stat-card-purple text-center">
                <div class="stat-value stat-value-purple">{{ formatNumber($dbStats['music_entries']) }}</div>
                <div class="stat-label">Music Entries</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="feature-card stat-card-colored stat-card-yellow text-center">
                <div class="stat-value stat-value-yellow">{{ formatNumber($dbStats['playlists']) }}</div>
                <div class="stat-label">Playlists</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="feature-card stat-card-colored stat-card-teal text-center">
                <div class="stat-value" style="color: #4ecdc4;">{{ formatNumber($dbStats['tags']) }}</div>
                <div class="stat-label">Tags</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="feature-card stat-card-colored stat-card-green text-center">
                <div class="stat-value stat-value-green">{{ formatNumber($dbStats['activity_logs']) }}</div>
                <div class="stat-label">Activity Logs</div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row g-4">
        <!-- Environment Configuration -->
        <div class="col-md-6">
            <div class="test-section">
                <h5>
                    <i class="bi bi-gear me-2"></i>Environment Configuration
                </h5>
                <div class="test-item">
                    <span class="test-label">Environment</span>
                    <span class="test-result status-{{ $serverInfo['environment'] === 'production' ? 'success' : 'warning' }}">
                        {{ ucfirst($serverInfo['environment']) }}
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-label">Database Type</span>
                    <span class="test-result status-info">{{ ucfirst($serverInfo['database']) }}</span>
                </div>
                <div class="test-item">
                    <span class="test-label">DB Connection</span>
                    <span class="test-result status-{{ str_contains($serverInfo['db_connection'], 'Connected') ? 'success' : 'error' }}">
                        {{ $serverInfo['db_connection'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- PHP Environment -->
        <div class="col-md-6">
            <div class="test-section">
                <h5>
                    <i class="bi bi-code-square me-2"></i>PHP Environment
                </h5>
                <div class="test-item">
                    <span class="test-label">PHP Version</span>
                    <span class="test-result status-success">{{ $serverInfo['php_version'] }}</span>
                </div>
                <div class="test-item">
                    <span class="test-label">Laravel Version</span>
                    <span class="test-result status-success">{{ $serverInfo['laravel_version'] }}</span>
                </div>
                <div class="test-item">
                    <span class="test-label">Memory Limit</span>
                    <span class="test-result status-info">{{ ini_get('memory_limit') }}</span>
                </div>
                <div class="test-item">
                    <span class="test-label">Max Execution Time</span>
                    <span class="test-result status-info">{{ ini_get('max_execution_time') }}s</span>
                </div>
            </div>
        </div>

        <!-- System Performance -->
        <div class="col-md-6">
            <div class="test-section">
                <h5>
                    <i class="bi bi-speedometer me-2"></i>System Performance
                </h5>
                <div class="test-item">
                    <span class="test-label">Server Load</span>
                    <span class="test-result status-success">
                        @php
                            $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
                            echo round($load[0], 2);
                        @endphp
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-label">Disk Space Available</span>
                    <span class="test-result status-info">
                        {{ round(disk_free_space('/') / 1024 / 1024 / 1024, 2) }} GB
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-label">Upload Max Filesize</span>
                    <span class="test-result status-info">{{ ini_get('upload_max_filesize') }}</span>
                </div>
                <div class="test-item">
                    <span class="test-label">Post Max Size</span>
                    <span class="test-result status-info">{{ ini_get('post_max_size') }}</span>
                </div>
            </div>
        </div>

        <!-- Database Details -->
        <div class="col-md-6">
            <div class="test-section">
                <h5>
                    <i class="bi bi-database me-2"></i>Database Details
                </h5>
                <div class="test-item">
                    <span class="test-label">Total Records</span>
                    <span class="test-result status-success">
                        {{ formatNumber(array_sum($dbStats)) }}
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-label">Users Table</span>
                    <span class="test-result status-info">{{ formatNumber($dbStats['users']) }} rows</span>
                </div>
                <div class="test-item">
                    <span class="test-label">Music Entries Table</span>
                    <span class="test-result status-info">{{ formatNumber($dbStats['music_entries']) }} rows</span>
                </div>
                <div class="test-item">
                    <span class="test-label">Activity Logs Table</span>
                    <span class="test-result status-info">{{ formatNumber($dbStats['activity_logs']) }} rows</span>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection
