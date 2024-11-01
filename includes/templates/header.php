<?php
if (!defined('ABSPATH')):
    require_once '../access-check-wp.php';
endif;
?>
<div class="superrishi-plugin-settings-container"><!--This div closes in footer.-->
    <div class="superrishi-plugin-settings-header">
        <div class="superrishi-header-logo">
            <a href="<?= SUPER_RISHI_WEBSITE; ?>" class="superrishi-logo-link" target="_blank">
                <span class="superrishi-logo-text">SUPER RISHI</span>
            </a>
        </div>
        <div class="superrishi-header-nav">
            <ul class="superrishi-header-menu">
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_ITEMS); ?>" class="superrishi-header-menu-item-link <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_ITEMS) !== false ? 'current-page' : ''; ?>">360&deg; Views &#9885;</a>
                </li>
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_SETTINGS); ?>" class="superrishi-header-menu-item-link <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_SETTINGS) !== false ? 'current-page' : ''; ?>">Global Settings</a>
                </li>
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_IMPORT_IMAGES); ?>" class="superrishi-header-menu-item-link  <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_IMPORT_IMAGES) !== false ? 'current-page' : ''; ?>">Bulk Import Images</a>
                </li>
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_RESTORE); ?>" class="superrishi-header-menu-item-link <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_RESTORE) !== false ? 'current-page' : ''; ?>">Restore Previous Views</a>
                </li>
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_SHORTCODES); ?>" class="superrishi-header-menu-item-link <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_SHORTCODES) !== false ? 'current-page' : ''; ?>">Shortcodes</a>
                </li>
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_HELP); ?>" class="superrishi-header-menu-item-link <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_HELP) !== false ? 'current-page' : ''; ?>">Help <i class="dashicons dashicons-editor-help"></i></a>
                </li>
                <li class="superrishi-header-menu-item">
                    <a href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_LICENSE); ?>" class="superrishi-header-menu-item-link <?= strpos($_SERVER['QUERY_STRING'], _SR_360_ADMIN_PAGE_LICENSE) !== false ? 'current-page' : ''; ?>">Activate Plugin &#128273;</a>
                </li>
            </ul>
        </div>
    </div>