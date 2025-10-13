<!-- Admin System Settings Page -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row">
            <!-- Page Header -->
            <div class="col-12 mb-4">
                <div class="feature-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-1">
                                <i class="bi bi-gear me-2" style="color: var(--accent-blue);"></i>
                                System Settings
                            </h1>
                            <p class="text-muted mb-0">Configure application settings and preferences</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group" role="group">
                                <a href="/admin" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Dashboard
                                </a>
                                <a href="/admin/users" class="btn btn-outline-glow">
                                    <i class="bi bi-people me-1"></i>Users
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <form method="POST">
        <?= csrf_field() ?>
        
        <div class="row g-4">
            <!-- Application Settings -->
            <?php if (!empty($settings['application'])): ?>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h5 class="mb-4">
                            <i class="bi bi-app me-2" style="color: var(--accent-blue);"></i>
                            Application Settings
                        </h5>
                        <div>
                            <?php foreach ($settings['application'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="setting_<?= e($setting['setting_key']) ?>" class="form-label">
                                        <?= e(ucwords(str_replace('_', ' ', $setting['setting_key']))) ?>
                                        <?php if ($setting['is_public']): ?>
                                            <span class="badge bg-info ms-2">Public</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if (!empty($setting['description'])): ?>
                                        <small class="text-muted d-block mb-1"><?= e($setting['description']) ?></small>
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['setting_type'] === 'boolean'): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="setting_<?= e($setting['setting_key']) ?>" 
                                                   name="setting_<?= e($setting['setting_key']) ?>"
                                                   value="1"
                                                   <?= $setting['setting_value'] == '1' ? 'checked' : '' ?>>
                                        </div>
                                    <?php elseif ($setting['setting_type'] === 'integer'): ?>
                                        <input type="number" class="form-control" 
                                               id="setting_<?= e($setting['setting_key']) ?>" 
                                               name="setting_<?= e($setting['setting_key']) ?>"
                                               value="<?= e($setting['setting_value']) ?>">
                                    <?php else: ?>
                                        <input type="text" class="form-control" 
                                               id="setting_<?= e($setting['setting_key']) ?>" 
                                               name="setting_<?= e($setting['setting_key']) ?>"
                                               value="<?= e($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Limits & Defaults -->
            <?php if (!empty($settings['limits'])): ?>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h5 class="mb-4">
                            <i class="bi bi-sliders me-2" style="color: var(--accent-purple);"></i>
                            Limits & Defaults
                        </h5>
                        <div>
                            <?php foreach ($settings['limits'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="setting_<?= e($setting['setting_key']) ?>" class="form-label">
                                        <?= e(ucwords(str_replace('_', ' ', $setting['setting_key']))) ?>
                                        <?php if ($setting['is_public']): ?>
                                            <span class="badge bg-info ms-2">Public</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if (!empty($setting['description'])): ?>
                                        <small class="text-muted d-block mb-1"><?= e($setting['description']) ?></small>
                                    <?php endif; ?>
                                    
                                    <input type="number" class="form-control" 
                                           id="setting_<?= e($setting['setting_key']) ?>" 
                                           name="setting_<?= e($setting['setting_key']) ?>"
                                           value="<?= e($setting['setting_value']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Feature Flags -->
            <?php if (!empty($settings['features'])): ?>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h5 class="mb-4">
                            <i class="bi bi-toggles me-2" style="color: #51cf66;"></i>
                            Feature Flags
                        </h5>
                        <div>
                            <?php foreach ($settings['features'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="setting_<?= e($setting['setting_key']) ?>" class="form-label">
                                        <?= e(ucwords(str_replace('_', ' ', $setting['setting_key']))) ?>
                                        <?php if ($setting['is_public']): ?>
                                            <span class="badge bg-info ms-2">Public</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if (!empty($setting['description'])): ?>
                                        <small class="text-muted d-block mb-1"><?= e($setting['description']) ?></small>
                                    <?php endif; ?>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="setting_<?= e($setting['setting_key']) ?>" 
                                               name="setting_<?= e($setting['setting_key']) ?>"
                                               value="1"
                                               <?= $setting['setting_value'] == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="setting_<?= e($setting['setting_key']) ?>">
                                            <?= $setting['setting_value'] == '1' ? 'Enabled' : 'Disabled' ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Other Settings -->
            <?php if (!empty($settings['other'])): ?>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h5 class="mb-4">
                            <i class="bi bi-wrench me-2" style="color: #feca57;"></i>
                            Other Settings
                        </h5>
                        <div>
                            <?php foreach ($settings['other'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="setting_<?= e($setting['setting_key']) ?>" class="form-label">
                                        <?= e(ucwords(str_replace('_', ' ', $setting['setting_key']))) ?>
                                        <?php if ($setting['is_public']): ?>
                                            <span class="badge bg-info ms-2">Public</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if (!empty($setting['description'])): ?>
                                        <small class="text-muted d-block mb-1"><?= e($setting['description']) ?></small>
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['setting_type'] === 'boolean'): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="setting_<?= e($setting['setting_key']) ?>" 
                                                   name="setting_<?= e($setting['setting_key']) ?>"
                                                   value="1"
                                                   <?= $setting['setting_value'] == '1' ? 'checked' : '' ?>>
                                        </div>
                                    <?php elseif ($setting['setting_type'] === 'integer'): ?>
                                        <input type="number" class="form-control" 
                                               id="setting_<?= e($setting['setting_key']) ?>" 
                                               name="setting_<?= e($setting['setting_key']) ?>"
                                               value="<?= e($setting['setting_value']) ?>">
                                    <?php else: ?>
                                        <input type="text" class="form-control" 
                                               id="setting_<?= e($setting['setting_key']) ?>" 
                                               name="setting_<?= e($setting['setting_key']) ?>"
                                               value="<?= e($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Save Button -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="feature-card">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/admin" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-glow">
                            <i class="bi bi-check-circle me-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</section>

