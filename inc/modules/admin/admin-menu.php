<?php
if (!defined('ABSPATH')) exit;

/**
 * KNX Shell - Admin Menu
 */

add_action('admin_menu', function () {
    add_menu_page(
        'KNX Shell',
        'KNX Shell',
        'manage_options',
        'knx-shell',
        'knx_shell_render_admin_settings',
        'dashicons-layout',
        4
    );
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos((string) $hook, 'knx-shell') === false) {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_style(
        'knx-shell-admin-style',
        KNX_SHELL_URL . 'inc/modules/admin/admin-style.css',
        [],
        KNX_SHELL_VERSION
    );

    wp_enqueue_script(
        'knx-shell-admin-script',
        KNX_SHELL_URL . 'inc/modules/admin/admin-script.js',
        ['jquery'],
        KNX_SHELL_VERSION,
        true
    );
});

if (!function_exists('knx_shell_render_admin_settings')) {
    /**
     * Render KNX Shell admin settings.
     *
     * @return void
     */
    function knx_shell_render_admin_settings() {
        $file = KNX_SHELL_PATH . 'inc/modules/admin/admin-settings.php';

        echo '<div class="wrap">';

        if (!file_exists($file)) {
            echo '<h1>KNX Shell</h1>';
            echo '<div class="notice notice-error"><p>admin-settings.php was not found.</p></div>';
            echo '</div>';
            return;
        }

        require $file;

        echo '</div>';
    }
}