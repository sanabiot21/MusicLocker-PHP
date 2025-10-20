@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">System Settings</h1>
        <p class="page-description">Configure application settings</p>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">Application Settings</h2>
        </div>
        <div class="card-body">
            <form id="settingsForm">
                @csrf
                <div class="form-group">
                    <label for="app_name" class="form-label">Application Name</label>
                    <input type="text" id="app_name" name="app_name" class="form-control"
                           value="{{ $settings['app_name'] ?? 'Music Locker' }}" required>
                </div>

                <div class="form-group">
                    <label for="app_description" class="form-label">Application Description</label>
                    <textarea id="app_description" name="app_description" class="form-control" rows="3">{{ $settings['app_description'] ?? '' }}</textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="registration_enabled" value="1"
                               {{ ($settings['registration_enabled'] ?? true) ? 'checked' : '' }}>
                        <span>Allow new user registration</span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="maintenance_mode" value="1"
                               {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                        <span>Enable maintenance mode</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        app_name: formData.get('app_name'),
        app_description: formData.get('app_description'),
        registration_enabled: formData.get('registration_enabled') === '1',
        maintenance_mode: formData.get('maintenance_mode') === '1'
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
