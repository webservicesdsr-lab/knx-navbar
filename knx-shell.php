<?php
/**
 * Plugin Name: KNX Shell
 * Description: Reusable global UI shell for KNX-based WordPress products.
 * Version: 0.2.0
 * Author: KNX
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin constants.
 */
if (!defined('KNX_SHELL_PATH')) {
    define('KNX_SHELL_PATH', plugin_dir_path(__FILE__));
}

if (!defined('KNX_SHELL_URL')) {
    define('KNX_SHELL_URL', plugin_dir_url(__FILE__));
}

if (!defined('KNX_SHELL_VERSION')) {
    define('KNX_SHELL_VERSION', '0.2.0');
}

if (!function_exists('knx_shell_require')) {
    /**
     * Safely load a KNX Shell file.
     *
     * @param string $relative Relative path from plugin root.
     * @return void
     */
    function knx_shell_require($relative) {
        $path = KNX_SHELL_PATH . ltrim($relative, '/');

        if (file_exists($path)) {
            require_once $path;
        }
    }
}

/**
 * Load KNX Shell files immediately.
 */
knx_shell_require('inc/functions/helpers.php');
knx_shell_require('inc/modules/admin/admin-menu.php');
knx_shell_require('inc/modules/navbar/navbar.php');

/**
 * Admin dependency notice.
 */
add_action('admin_notices', function () {
    if (function_exists('knx_get_session')) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html('KNX Shell works best with KNX Auth & Core active. The navbar will still render, but session-aware features will be limited.');
    echo '</p></div>';
});