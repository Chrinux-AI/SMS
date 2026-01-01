<?php
/**
 * Universal Footer Component
 * Consistent footer across all pages
 */

$current_year = date('Y');
?>
<footer class="cyber-footer" style="margin-top: auto; padding: 20px; text-align: center; border-top: 1px solid rgba(0, 191, 255, 0.2);">
    <div class="footer-content" style="color: var(--text-muted, #9ca3af); font-size: 0.85rem;">
        <p style="margin: 0;">
            &copy; <?php echo $current_year; ?> <?php echo APP_NAME; ?>. All rights reserved.
        </p>
        <p style="margin: 5px 0 0;">
            <a href="<?php echo APP_URL; ?>/visitor/privacy-policy.php" style="color: var(--cyber-cyan, #00BFFF); text-decoration: none; margin: 0 10px;">Privacy Policy</a>
            <a href="<?php echo APP_URL; ?>/visitor/contact.php" style="color: var(--cyber-cyan, #00BFFF); text-decoration: none; margin: 0 10px;">Contact</a>
            <a href="<?php echo APP_URL; ?>/visitor/faq.php" style="color: var(--cyber-cyan, #00BFFF); text-decoration: none; margin: 0 10px;">FAQ</a>
        </p>
        <p style="margin: 5px 0 0; font-size: 0.75rem; opacity: 0.7;">
            Version <?php echo APP_VERSION; ?>
        </p>
    </div>
</footer>


