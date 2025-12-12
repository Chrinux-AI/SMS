<?php

/**
 * Theme Selector Modal Component
 * Beautiful theme selection UI with live preview
 * Verdant SMS v3.0
 */

require_once __DIR__ . '/theme-loader.php';
require_once __DIR__ . '/functions.php';

$themes = get_available_themes();
$current_theme = get_user_theme();
?>

<!-- Theme Selector Modal -->
<div class="theme-modal-overlay" id="themeModal">
    <div class="theme-modal">
        <div class="theme-modal-header">
            <div class="theme-modal-title">
                <i class="fas fa-palette"></i>
                <h2>Choose Your Theme</h2>
            </div>
            <button class="theme-modal-close" onclick="closeThemeModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="theme-modal-body">
            <p class="theme-modal-subtitle">Personalize your experience with one of our beautiful themes</p>

            <div class="theme-grid">
                <?php foreach ($themes as $theme_id => $theme): ?>
                    <div class="theme-card <?php echo $theme_id === $current_theme ? 'active' : ''; ?>"
                        data-theme="<?php echo $theme_id; ?>"
                        onclick="previewTheme('<?php echo $theme_id; ?>')"
                        tabindex="0"
                        role="button"
                        aria-pressed="<?php echo $theme_id === $current_theme ? 'true' : 'false'; ?>">

                        <div class="theme-preview" style="background: <?php echo $theme['colors'][2] ?? '#000'; ?>;">
                            <div class="theme-preview-sidebar" style="background: <?php echo $theme['colors'][1] ?? '#333'; ?>;"></div>
                            <div class="theme-preview-content">
                                <div class="theme-preview-header" style="background: <?php echo $theme['colors'][0]; ?>;"></div>
                                <div class="theme-preview-cards">
                                    <div class="theme-preview-card" style="border-color: <?php echo $theme['colors'][0]; ?>;"></div>
                                    <div class="theme-preview-card" style="border-color: <?php echo $theme['colors'][0]; ?>;"></div>
                                </div>
                            </div>
                            <?php if (isset($theme['accessibility'])): ?>
                                <div class="theme-accessibility-badge" title="Accessibility Optimized">
                                    <i class="fas fa-universal-access"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="theme-info">
                            <div class="theme-name">
                                <i class="fas fa-<?php echo $theme['icon']; ?>"></i>
                                <?php echo htmlspecialchars($theme['name']); ?>
                            </div>
                            <div class="theme-description"><?php echo htmlspecialchars($theme['description']); ?></div>
                            <div class="theme-colors">
                                <?php foreach ($theme['colors'] as $color): ?>
                                    <span class="theme-color-dot" style="background: <?php echo $color; ?>;"></span>
                                <?php endforeach; ?>
                                <span class="theme-type-badge <?php echo $theme['type']; ?>">
                                    <?php echo $theme['type'] === 'light' ? 'Light' : 'Dark'; ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($theme_id === $current_theme): ?>
                            <div class="theme-active-badge">
                                <i class="fas fa-check-circle"></i> Active
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="theme-modal-footer">
            <div class="theme-footer-left">
                <label class="theme-auto-toggle">
                    <input type="checkbox" id="autoThemeToggle" onchange="toggleAutoTheme(this.checked)">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">
                        <i class="fas fa-adjust"></i> Auto (follow system)
                    </span>
                </label>
            </div>
            <div class="theme-footer-right">
                <button class="theme-btn theme-btn-secondary" onclick="closeThemeModal()">Cancel</button>
                <button class="theme-btn theme-btn-primary" onclick="saveTheme()" id="saveThemeBtn">
                    <i class="fas fa-save"></i> Save Theme
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Smooth Theme Transition */
    body,
    body *,
    body *::before,
    body *::after {
        transition: background-color 0.3s ease,
            border-color 0.3s ease,
            box-shadow 0.3s ease,
            color 0.15s ease !important;
    }

    /* Exclude animations that shouldn't be affected */
    body.theme-switching *,
    .theme-modal *,
    .fa-spin {
        transition: none !important;
    }

    /* Theme Modal Styles */
    .theme-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .theme-modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .theme-modal {
        background: var(--theme-bg-secondary, #12121a);
        border: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
        border-radius: 16px;
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    }

    .theme-modal-overlay.active .theme-modal {
        transform: scale(1);
    }

    .theme-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
    }

    .theme-modal-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .theme-modal-title i {
        font-size: 1.5rem;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .theme-modal-title h2 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--theme-text-primary, #fff);
    }

    .theme-modal-close {
        background: transparent;
        border: none;
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.6));
        font-size: 1.2rem;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .theme-modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--theme-text-primary, #fff);
    }

    .theme-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }

    .theme-modal-subtitle {
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.6));
        margin: 0 0 20px 0;
        text-align: center;
    }

    .theme-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .theme-card {
        background: var(--theme-bg-card, rgba(255, 255, 255, 0.03));
        border: 2px solid var(--theme-border, rgba(255, 255, 255, 0.1));
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .theme-card:hover {
        border-color: var(--theme-primary, #a855f7);
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .theme-card.active {
        border-color: var(--theme-primary, #a855f7);
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);
    }

    .theme-card.previewing {
        border-color: #fbbf24;
        box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
    }

    .theme-preview {
        height: 100px;
        display: flex;
        padding: 8px;
        gap: 8px;
        position: relative;
    }

    .theme-preview-sidebar {
        width: 40px;
        border-radius: 6px;
        opacity: 0.8;
    }

    .theme-preview-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .theme-preview-header {
        height: 20px;
        border-radius: 4px;
        opacity: 0.9;
    }

    .theme-preview-cards {
        display: flex;
        gap: 8px;
        flex: 1;
    }

    .theme-preview-card {
        flex: 1;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid;
        border-radius: 4px;
    }

    .theme-accessibility-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #fbbf24;
        color: #000;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .theme-info {
        padding: 16px;
    }

    .theme-name {
        font-weight: 600;
        color: var(--theme-text-primary, #fff);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .theme-name i {
        color: var(--theme-primary, #a855f7);
    }

    .theme-description {
        font-size: 0.85rem;
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.6));
        margin-bottom: 12px;
    }

    .theme-colors {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .theme-color-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .theme-type-badge {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: auto;
        text-transform: uppercase;
        font-weight: 600;
    }

    .theme-type-badge.dark {
        background: rgba(0, 0, 0, 0.5);
        color: #a5b4fc;
    }

    .theme-type-badge.light {
        background: rgba(255, 255, 255, 0.9);
        color: #1e293b;
    }

    .theme-active-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--theme-success, #10b981);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .theme-modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        border-top: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
        background: var(--theme-bg-tertiary, rgba(0, 0, 0, 0.2));
    }

    .theme-footer-left {
        display: flex;
        align-items: center;
    }

    .theme-auto-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .theme-auto-toggle input {
        display: none;
    }

    .toggle-slider {
        width: 44px;
        height: 24px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        position: relative;
        transition: background 0.3s;
    }

    .toggle-slider::after {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        top: 3px;
        left: 3px;
        transition: transform 0.3s;
    }

    .theme-auto-toggle input:checked+.toggle-slider {
        background: var(--theme-primary, #a855f7);
    }

    .theme-auto-toggle input:checked+.toggle-slider::after {
        transform: translateX(20px);
    }

    .toggle-label {
        color: var(--theme-text-secondary, rgba(255, 255, 255, 0.8));
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .theme-footer-right {
        display: flex;
        gap: 12px;
    }

    .theme-btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .theme-btn-secondary {
        background: transparent;
        color: var(--theme-text-secondary, rgba(255, 255, 255, 0.8));
        border: 1px solid var(--theme-border, rgba(255, 255, 255, 0.2));
    }

    .theme-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .theme-btn-primary {
        background: linear-gradient(135deg, #a855f7, #ec4899);
        color: white;
    }

    .theme-btn-primary:hover {
        box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .theme-modal {
            width: 95%;
            max-height: 95vh;
        }

        .theme-grid {
            grid-template-columns: 1fr;
        }

        .theme-modal-footer {
            flex-direction: column;
            gap: 16px;
        }

        .theme-footer-right {
            width: 100%;
        }

        .theme-btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<script>
    // Theme Selector JavaScript
    let selectedTheme = '<?php echo $current_theme; ?>';
    let originalTheme = '<?php echo $current_theme; ?>';

    function openThemeModal() {
        const modal = document.getElementById('themeModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Reset selection to current theme
        selectedTheme = originalTheme;
        updateThemeCards();
    }

    function closeThemeModal() {
        const modal = document.getElementById('themeModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';

        // Revert to original theme if not saved
        if (selectedTheme !== originalTheme) {
            applyTheme(originalTheme);
            selectedTheme = originalTheme;
        }
    }

    function previewTheme(themeId) {
        selectedTheme = themeId;
        applyTheme(themeId);
        updateThemeCards();
    }

    function updateThemeCards() {
        document.querySelectorAll('.theme-card').forEach(card => {
            card.classList.remove('active', 'previewing');
            if (card.dataset.theme === originalTheme) {
                card.classList.add('active');
            }
            if (card.dataset.theme === selectedTheme && selectedTheme !== originalTheme) {
                card.classList.add('previewing');
            }
        });
    }

    function applyTheme(themeId) {
        // Update body class
        const body = document.body;
        const currentThemeClass = Array.from(body.classList).find(c => c.startsWith('theme-'));
        if (currentThemeClass) {
            body.classList.remove(currentThemeClass);
        }
        body.classList.add('theme-' + themeId);

        // Update stylesheet
        const stylesheet = document.getElementById('theme-stylesheet');
        if (stylesheet) {
            const basePath = stylesheet.href.substring(0, stylesheet.href.lastIndexOf('/') + 1);
            stylesheet.href = basePath + themeId + '.css';
        }
    }

    function saveTheme() {
        const btn = document.getElementById('saveThemeBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;

        // Save via AJAX
        fetch('<?php echo rtrim(APP_URL, '/'); ?>/api/save-theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>',
                },
                body: JSON.stringify({
                    theme: selectedTheme
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    originalTheme = selectedTheme;
                    updateThemeCards();

                    btn.innerHTML = '<i class="fas fa-check"></i> Saved!';
                    setTimeout(() => {
                        closeThemeModal();
                        btn.innerHTML = '<i class="fas fa-save"></i> Save Theme';
                        btn.disabled = false;
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to save theme');
                }
            })
            .catch(error => {
                console.error('Error saving theme:', error);
                btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-save"></i> Save Theme';
                    btn.disabled = false;
                }, 2000);
            });
    }

    function toggleAutoTheme(enabled) {
        if (enabled && window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const lightTheme = 'minimal-white';
            const darkTheme = 'verdant-nature';

            const handleChange = (e) => {
                previewTheme(e.matches ? darkTheme : lightTheme);
            };

            darkModeQuery.addEventListener('change', handleChange);
            handleChange(darkModeQuery);
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeThemeModal();
        }
    });

    // Close modal on overlay click
    document.getElementById('themeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeThemeModal();
        }
    });
</script>