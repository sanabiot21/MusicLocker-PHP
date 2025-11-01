@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-gear me-2" style="color: var(--accent-blue);"></i>
                System Settings
            </h1>
            <p class="page-description">Configure application settings and features</p>
        </div>
    </div>

    <form id="settingsForm">
        @csrf

        <div class="row g-4">
            <!-- Application Settings -->
            <div class="col-md-6">
                <div class="feature-card h-100">
                    <div class="settings-category-header">
                        <div class="settings-category-icon icon-blue">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h3 class="settings-category-title">Application Settings</h3>
                    </div>

                    <div class="settings-category">
                        <div class="setting-item">
                            <label for="app_name" class="form-label">Application Name</label>
                            <input type="text" id="app_name" name="app_name" class="form-control form-control-dark"
                                   value="{{ $settings['app_name'] ?? 'Music Locker' }}" required>
                            <div class="setting-description">The name displayed throughout the application</div>
                        </div>

                        <div class="setting-item">
                            <label for="app_description" class="form-label">Application Description</label>
                            <textarea id="app_description" name="app_description" class="form-control form-control-dark" rows="3">{{ $settings['app_description'] ?? '' }}</textarea>
                            <div class="setting-description">Brief description shown on the homepage</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Flags -->
            <div class="col-md-6">
                <div class="feature-card h-100">
                    <div class="settings-category-header">
                        <div class="settings-category-icon icon-purple">
                            <i class="bi bi-toggles"></i>
                        </div>
                        <h3 class="settings-category-title">Feature Flags</h3>
                    </div>

                    <div class="settings-category">
                        <div class="setting-item">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="registration_enabled" name="registration_enabled" value="1"
                                       {{ ($settings['registration_enabled'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="registration_enabled">
                                    Allow New User Registration
                                </label>
                            </div>
                            <div class="setting-description">Enable or disable public user registration</div>
                        </div>

                        <div class="setting-item">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="maintenance_mode" name="maintenance_mode" value="1"
                                       {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">
                                    Maintenance Mode
                                </label>
                            </div>
                            <div class="setting-description">Put the application in maintenance mode (admins can still access)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Limits & Defaults -->
            <div class="col-md-6">
                <div class="feature-card h-100">
                    <div class="settings-category-header">
                        <div class="settings-category-icon icon-yellow">
                            <i class="bi bi-speedometer"></i>
                        </div>
                        <h3 class="settings-category-title">Limits & Defaults</h3>
                    </div>

                    <div class="settings-category">
                        <div class="setting-item">
                            <label for="max_upload_size" class="form-label">Max Upload Size (MB)</label>
                            <input type="number" id="max_upload_size" name="max_upload_size"
                                   class="form-control form-control-dark"
                                   value="{{ $settings['max_upload_size'] ?? 10 }}" min="1" max="100">
                            <div class="setting-description">Maximum file size for music uploads</div>
                        </div>

                        <div class="setting-item">
                            <label for="items_per_page" class="form-label">Items Per Page</label>
                            <input type="number" id="items_per_page" name="items_per_page"
                                   class="form-control form-control-dark"
                                   value="{{ $settings['items_per_page'] ?? 20 }}" min="10" max="100">
                            <div class="setting-description">Default pagination size for lists</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Settings -->
            <div class="col-md-6">
                <div class="feature-card h-100">
                    <div class="settings-category-header">
                        <div class="settings-category-icon icon-green">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <h3 class="settings-category-title">Other Settings</h3>
                    </div>

                    <div class="settings-category">
                        <div class="setting-item">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="email_notifications" name="email_notifications" value="1"
                                       {{ ($settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="setting-description">Send email notifications for important events</div>
                        </div>

                        <div class="setting-item">
                            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                            <input type="number" id="session_timeout" name="session_timeout"
                                   class="form-control form-control-dark"
                                   value="{{ $settings['session_timeout'] ?? 120 }}" min="30" max="1440">
                            <div class="setting-description">User session expiration time</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-glow btn-lg">
                <i class="bi bi-check-circle me-2"></i>Save All Settings
            </button>
        </div>
    </form>
</div>
</section>

@push('scripts')
<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        app_name: formData.get('app_name'),
        app_description: formData.get('app_description'),
        registration_enabled: formData.get('registration_enabled') === '1',
        maintenance_mode: formData.get('maintenance_mode') === '1',
        max_upload_size: parseInt(formData.get('max_upload_size') || 10),
        items_per_page: parseInt(formData.get('items_per_page') || 20),
        email_notifications: formData.get('email_notifications') === '1',
        session_timeout: parseInt(formData.get('session_timeout') || 120)
    };

    fetch('/admin/settings/update', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            MusicLocker.showToast('Settings updated successfully!', 'success');
        } else {
            MusicLocker.showToast(data.message || 'Error updating settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        MusicLocker.showToast('Error updating settings', 'danger');
    });
});
</script>
@endpush
@endsection
