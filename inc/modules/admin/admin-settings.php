<?php
if (!defined('ABSPATH')) exit;

/**
 * KNX Shell - Admin Settings
 */

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

$message = '';
$error = '';

$defaults = function_exists('knx_shell_default_settings') ? knx_shell_default_settings() : [];
$settings = function_exists('knx_shell_get_settings') ? knx_shell_get_settings() : $defaults;

if (!function_exists('knx_shell_admin_clean_opacity')) {
    /**
     * Sanitize opacity.
     *
     * @param mixed  $value Input value.
     * @param string $fallback Fallback.
     * @return string
     */
    function knx_shell_admin_clean_opacity($value, $fallback = '1.00') {
        if ($value === '' || is_null($value)) {
            $value = $fallback;
        }

        $float = (float) $value;
        $float = max(0, min(1, $float));

        return number_format($float, 2, '.', '');
    }
}

if (!function_exists('knx_shell_admin_clean_px')) {
    /**
     * Sanitize pixel input.
     *
     * @param mixed  $value Input.
     * @param string $fallback Fallback.
     * @return string
     */
    function knx_shell_admin_clean_px($value, $fallback) {
        $value = is_string($value) ? trim($value) : '';

        if (preg_match('/^\d{1,3}px$/', $value)) {
            return $value;
        }

        return $fallback;
    }
}

if (!function_exists('knx_shell_admin_clean_menu_items')) {
    /**
     * Sanitize menu builder payload.
     *
     * @param mixed $raw Raw menu array.
     * @return array
     */
    function knx_shell_admin_clean_menu_items($raw) {
        if (!is_array($raw)) {
            return [];
        }

        $items = [];

        foreach ($raw as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = isset($item['label']) ? sanitize_text_field(wp_unslash($item['label'])) : '';

            if ($label === '') {
                continue;
            }

            $url = isset($item['url']) ? esc_url_raw(wp_unslash($item['url'])) : '#';

            if ($url === '') {
                $url = '#';
            }

            $visibility = isset($item['visibility']) ? sanitize_key(wp_unslash($item['visibility'])) : 'all';
            if (!in_array($visibility, ['all', 'logged_in', 'logged_out'], true)) {
                $visibility = 'all';
            }

            $placement = isset($item['placement']) ? sanitize_key(wp_unslash($item['placement'])) : 'all';
            if (!in_array($placement, ['all', 'desktop', 'drawer'], true)) {
                $placement = 'all';
            }

            $target = isset($item['target']) ? sanitize_key(wp_unslash($item['target'])) : 'self';
            if (!in_array($target, ['self', 'blank'], true)) {
                $target = 'self';
            }

            $children = [];

            if (isset($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $child) {
                    if (!is_array($child)) {
                        continue;
                    }

                    $child_label = isset($child['label']) ? sanitize_text_field(wp_unslash($child['label'])) : '';

                    if ($child_label === '') {
                        continue;
                    }

                    $child_url = isset($child['url']) ? esc_url_raw(wp_unslash($child['url'])) : '#';

                    if ($child_url === '') {
                        $child_url = '#';
                    }

                    $child_target = isset($child['target']) ? sanitize_key(wp_unslash($child['target'])) : 'self';
                    if (!in_array($child_target, ['self', 'blank'], true)) {
                        $child_target = 'self';
                    }

                    $children[] = [
                        'label'  => $child_label,
                        'url'    => $child_url,
                        'icon'   => isset($child['icon']) ? sanitize_text_field(wp_unslash($child['icon'])) : '',
                        'target' => $child_target,
                    ];
                }
            }

            $items[] = [
                'label'      => $label,
                'url'        => $url,
                'icon'       => isset($item['icon']) ? sanitize_text_field(wp_unslash($item['icon'])) : '',
                'visibility' => $visibility,
                'placement'  => $placement,
                'target'     => $target,
                'priority'   => isset($item['priority']) ? (int) $item['priority'] : (($index + 1) * 10),
                'children'   => $children,
            ];
        }

        return array_values($items);
    }
}

if (!function_exists('knx_shell_admin_color_field')) {
    /**
     * Render color field.
     *
     * @param string $name Name.
     * @param string $label Label.
     * @param string $value Value.
     * @return void
     */
    function knx_shell_admin_color_field($name, $label, $value) {
        $value = sanitize_hex_color($value) ?: '#000000';

        echo '<label class="knx-shell-admin__color-label">';
        echo '<span>' . esc_html($label) . '</span>';
        echo '<div class="knx-shell-admin__color-field" data-knx-color-field>';
        echo '<input type="color" value="' . esc_attr($value) . '" data-knx-color-picker>';
        echo '<input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" data-knx-color-hex maxlength="7" autocomplete="off" spellcheck="false">';
        echo '<input type="text" value="" readonly data-knx-color-rgb tabindex="-1">';
        echo '</div>';
        echo '</label>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['knx_shell_reset_theme'])) {
    if (
        !isset($_POST['_knx_shell_settings_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_knx_shell_settings_nonce'])), 'knx_shell_settings_action')
    ) {
        wp_die('Security check failed.');
    }

    $current = function_exists('knx_shell_get_settings') ? knx_shell_get_settings() : [];
    $theme_defaults = function_exists('knx_shell_theme_default_settings') ? knx_shell_theme_default_settings() : [];

    update_option('knx_shell_settings', array_merge($current, $theme_defaults));

    $settings = function_exists('knx_shell_get_settings') ? knx_shell_get_settings() : $defaults;
    $message = 'Theme defaults restored. Branding, actions, and menus were preserved.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['knx_shell_settings_submit'])) {
    if (
        !isset($_POST['_knx_shell_settings_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_knx_shell_settings_nonce'])), 'knx_shell_settings_action')
    ) {
        wp_die('Security check failed.');
    }

    $design = isset($_POST['design']) ? sanitize_key(wp_unslash($_POST['design'])) : 'solid';
    if (!in_array($design, ['solid', 'glass', 'dark'], true)) {
        $design = 'solid';
    }

    $layout = isset($_POST['layout']) ? sanitize_key(wp_unslash($_POST['layout'])) : 'left';
    if (!in_array($layout, ['left', 'center', 'split'], true)) {
        $layout = 'left';
    }

    $drawer_side = isset($_POST['drawer_side']) ? sanitize_key(wp_unslash($_POST['drawer_side'])) : 'right';
    if (!in_array($drawer_side, ['current', 'left', 'right'], true)) {
        $drawer_side = 'right';
    }

    $position_mode = isset($_POST['position_mode']) ? sanitize_key(wp_unslash($_POST['position_mode'])) : 'push';
    if (!in_array($position_mode, ['push', 'overlay'], true)) {
        $position_mode = 'push';
    }

    $hamburger_style = isset($_POST['hamburger_style']) ? sanitize_key(wp_unslash($_POST['hamburger_style'])) : 'basic';
    if (!in_array($hamburger_style, ['basic', 'glass', 'circle', 'solid'], true)) {
        $hamburger_style = 'basic';
    }

    $cta_label = isset($_POST['cta_label']) ? sanitize_text_field(wp_unslash($_POST['cta_label'])) : 'Login';
    $legacy_cta_labels = ['Get Estimate', 'Get Started'];

    if ($cta_label === '' || in_array($cta_label, $legacy_cta_labels, true)) {
        $cta_label = 'Login';
    }

    $cta_url = isset($_POST['cta_url']) ? esc_url_raw(wp_unslash($_POST['cta_url'])) : '/login';

    if ($cta_url === '') {
        $cta_url = '/login';
    }

    $cta_icon = isset($_POST['cta_icon']) ? sanitize_text_field(wp_unslash($_POST['cta_icon'])) : 'fa-solid fa-arrow-right-to-bracket';

    if ($cta_icon === '') {
        $cta_icon = 'fa-solid fa-arrow-right-to-bracket';
    }

    $new_settings = [
        'enabled'       => isset($_POST['enabled']) ? '1' : '0',
        'is_sticky'     => isset($_POST['is_sticky']) ? '1' : '0',
        'design'        => $design,
        'layout'        => $layout,
        'drawer_side'   => $drawer_side,
        'position_mode' => $position_mode,
        'hamburger_style' => $hamburger_style,

        'brand_name' => isset($_POST['brand_name']) ? sanitize_text_field(wp_unslash($_POST['brand_name'])) : '',
        'app_label'  => isset($_POST['app_label']) ? sanitize_text_field(wp_unslash($_POST['app_label'])) : '',
        'initials'   => isset($_POST['initials']) ? strtoupper(preg_replace('/[^A-Z0-9]/', '', sanitize_text_field(wp_unslash($_POST['initials'])))) : 'KNX',
        'logo_url'   => isset($_POST['logo_url']) ? esc_url_raw(wp_unslash($_POST['logo_url'])) : '',
        'home_url'   => isset($_POST['home_url']) ? esc_url_raw(wp_unslash($_POST['home_url'])) : home_url('/'),

        'logo_height_desktop' => knx_shell_admin_clean_px($_POST['logo_height_desktop'] ?? '72px', '72px'),
        'logo_height_mobile'  => knx_shell_admin_clean_px($_POST['logo_height_mobile'] ?? '64px', '64px'),
        'logo_max_width'      => knx_shell_admin_clean_px($_POST['logo_max_width'] ?? '260px', '260px'),

        'drawer_logo_size' => knx_shell_admin_clean_px($_POST['drawer_logo_size'] ?? '64px', '64px'),
        'drawer_logo_bg'   => isset($_POST['drawer_logo_bg']) ? (sanitize_hex_color(wp_unslash($_POST['drawer_logo_bg'])) ?: '#1E63C8') : '#1E63C8',
        'drawer_logo_text' => isset($_POST['drawer_logo_text']) ? (sanitize_hex_color(wp_unslash($_POST['drawer_logo_text'])) ?: '#ffffff') : '#ffffff',

        'action_label' => isset($_POST['action_label']) ? sanitize_text_field(wp_unslash($_POST['action_label'])) : '',
        'action_url'   => isset($_POST['action_url']) ? esc_url_raw(wp_unslash($_POST['action_url'])) : '',
        'action_icon'  => isset($_POST['action_icon']) ? sanitize_text_field(wp_unslash($_POST['action_icon'])) : '',
        'action_style' => isset($_POST['action_style']) ? sanitize_key(wp_unslash($_POST['action_style'])) : 'text',

        'cta_label' => $cta_label,
        'cta_url'   => $cta_url,
        'cta_icon'  => $cta_icon,
        'cta_style' => isset($_POST['cta_style']) ? sanitize_key(wp_unslash($_POST['cta_style'])) : 'light',

        'menu_items' => knx_shell_admin_clean_menu_items($_POST['menu_items'] ?? []),

        'primary' => isset($_POST['primary']) ? (sanitize_hex_color(wp_unslash($_POST['primary'])) ?: '#1E63C8') : '#1E63C8',
        'accent'  => isset($_POST['accent']) ? (sanitize_hex_color(wp_unslash($_POST['accent'])) ?: '#0B1F3A') : '#0B1F3A',
        'text'    => isset($_POST['text']) ? (sanitize_hex_color(wp_unslash($_POST['text'])) ?: '#ffffff') : '#ffffff',
        'muted'   => isset($_POST['muted']) ? (sanitize_hex_color(wp_unslash($_POST['muted'])) ?: '#D6E6FF') : '#D6E6FF',

        'button_bg'      => isset($_POST['button_bg']) ? (sanitize_hex_color(wp_unslash($_POST['button_bg'])) ?: '#ffffff') : '#ffffff',
        'button_text'    => isset($_POST['button_text']) ? (sanitize_hex_color(wp_unslash($_POST['button_text'])) ?: '#1E63C8') : '#1E63C8',
        'button_opacity' => knx_shell_admin_clean_opacity($_POST['button_opacity'] ?? '1.00', '1.00'),

        'navbar_gradient_1' => isset($_POST['navbar_gradient_1']) ? (sanitize_hex_color(wp_unslash($_POST['navbar_gradient_1'])) ?: '#1E63C8') : '#1E63C8',
        'navbar_gradient_2' => isset($_POST['navbar_gradient_2']) ? (sanitize_hex_color(wp_unslash($_POST['navbar_gradient_2'])) ?: '#1557BB') : '#1557BB',
        'navbar_gradient_3' => isset($_POST['navbar_gradient_3']) ? (sanitize_hex_color(wp_unslash($_POST['navbar_gradient_3'])) ?: '#0F4EA8') : '#0F4EA8',
        'navbar_opacity'    => knx_shell_admin_clean_opacity($_POST['navbar_opacity'] ?? '1.00', '1.00'),
        'navbar_border_opacity' => knx_shell_admin_clean_opacity($_POST['navbar_border_opacity'] ?? '0.20', '0.20'),

        'sidebar_bg'      => isset($_POST['sidebar_bg']) ? (sanitize_hex_color(wp_unslash($_POST['sidebar_bg'])) ?: '#ffffff') : '#ffffff',
        'sidebar_text'    => isset($_POST['sidebar_text']) ? (sanitize_hex_color(wp_unslash($_POST['sidebar_text'])) ?: '#0B1F3A') : '#0B1F3A',
        'sidebar_opacity' => knx_shell_admin_clean_opacity($_POST['sidebar_opacity'] ?? '1.00', '1.00'),
        'sidebar_border_opacity' => knx_shell_admin_clean_opacity($_POST['sidebar_border_opacity'] ?? '0.28', '0.28'),

        'bg'            => isset($_POST['bg']) ? (sanitize_hex_color(wp_unslash($_POST['bg'])) ?: '#f7f8f9') : '#f7f8f9',
        'radius'        => knx_shell_admin_clean_px($_POST['radius'] ?? '22px', '22px'),
        'blur'          => knx_shell_admin_clean_px($_POST['blur'] ?? '20px', '20px'),
        'glass_opacity' => knx_shell_admin_clean_opacity($_POST['glass_opacity'] ?? '0.78', '0.78'),
    ];

    if ($new_settings['brand_name'] === '') {
        $new_settings['brand_name'] = 'KNX Shell';
    }

    if ($new_settings['initials'] === '') {
        $new_settings['initials'] = 'KNX';
    }

    if ($new_settings['home_url'] === '') {
        $new_settings['home_url'] = home_url('/');
    }

    update_option('knx_shell_settings', $new_settings);

    $settings = function_exists('knx_shell_get_settings') ? knx_shell_get_settings() : $new_settings;
    $message = 'KNX Shell settings saved.';
}

$menu_items = isset($settings['menu_items']) && is_array($settings['menu_items']) ? $settings['menu_items'] : [];
?>

<h1>KNX Shell</h1>

<?php if ($message): ?>
    <div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="notice notice-error"><p><?php echo esc_html($error); ?></p></div>
<?php endif; ?>

<div class="knx-shell-admin">
    <div class="knx-shell-admin__hero">
        <div>
            <span class="knx-shell-admin__kicker">Reusable UI System</span>
            <h2>KNX Shell Builder</h2>
            <p>Design a global reusable navbar with custom menus, submenus, branding, actions, mobile drawer, and theme controls.</p>
        </div>

        <div class="knx-shell-admin__stats">
            <div><strong><?php echo !empty($settings['enabled']) && $settings['enabled'] === '1' ? 'On' : 'Off'; ?></strong><span>Navbar</span></div>
            <div><strong><?php echo esc_html(ucfirst($settings['design'] ?? 'solid')); ?></strong><span>Design</span></div>
            <div><strong><?php echo esc_html(count($menu_items)); ?></strong><span>Menus</span></div>
            <div><strong><?php echo !empty($settings['is_sticky']) && $settings['is_sticky'] === '1' ? 'Sticky' : 'Static'; ?></strong><span>Behavior</span></div>
        </div>
    </div>

    <form method="post" class="knx-shell-admin__form" id="knxShellSettingsForm">
        <?php wp_nonce_field('knx_shell_settings_action', '_knx_shell_settings_nonce'); ?>

        <div class="knx-shell-admin__layout">
            <aside class="knx-shell-admin__side">
                <a href="#knx-panel-overview">Overview</a>
                <a href="#knx-panel-branding">Branding</a>
                <a href="#knx-panel-menu">Menu Builder</a>
                <a href="#knx-panel-actions">Header Actions</a>
                <a href="#knx-panel-mobile">Mobile Drawer</a>
                <a href="#knx-panel-theme">Theme Colors</a>
                <a href="#knx-panel-advanced">Advanced</a>
            </aside>

            <main class="knx-shell-admin__main">
                <details class="knx-shell-admin__panel" id="knx-panel-overview" open>
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Overview</h3>
                            <p>Global navbar behavior and layout.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div class="knx-shell-admin__grid">
                            <label class="knx-shell-admin__toggle">
                                <input type="checkbox" name="enabled" value="1" <?php checked(!empty($settings['enabled']) && $settings['enabled'] === '1'); ?>>
                                <span>Enable global navbar</span>
                            </label>

                            <label class="knx-shell-admin__toggle">
                                <input type="checkbox" name="is_sticky" value="1" <?php checked(!empty($settings['is_sticky']) && $settings['is_sticky'] === '1'); ?>>
                                <span>Sticky navbar</span>
                            </label>

                            <label>
                                <span>Visual Design</span>
                                <select name="design">
                                    <option value="solid" <?php selected($settings['design'], 'solid'); ?>>Solid Pro</option>
                                    <option value="glass" <?php selected($settings['design'], 'glass'); ?>>Liquid Glass</option>
                                    <option value="dark" <?php selected($settings['design'], 'dark'); ?>>Dark Command</option>
                                </select>
                            </label>

                            <label>
                                <span>Desktop Layout</span>
                                <select name="layout">
                                    <option value="left" <?php selected($settings['layout'], 'left'); ?>>Logo Left</option>
                                    <option value="center" <?php selected($settings['layout'], 'center'); ?>>Logo Center</option>
                                    <option value="split" <?php selected($settings['layout'], 'split'); ?>>Split App</option>
                                </select>
                            </label>

                            <label>
                                <span>Position Mode</span>
                                <select name="position_mode">
                                    <option value="push" <?php selected($settings['position_mode'], 'push'); ?>>Push Content Down</option>
                                    <option value="overlay" <?php selected($settings['position_mode'], 'overlay'); ?>>Overlay Background</option>
                                </select>
                            </label>

                            <label>
                                <span>Mobile Drawer Side</span>
                                <select name="drawer_side">
                                    <option value="right" <?php selected($settings['drawer_side'], 'right'); ?>>Right</option>
                                    <option value="left" <?php selected($settings['drawer_side'], 'left'); ?>>Left</option>
                                    <option value="current" <?php selected($settings['drawer_side'], 'current'); ?>>Bottom Sheet</option>
                                </select>
                            </label>
                        </div>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Overview</button>
                        </div>
                    </div>
                </details>

                <details class="knx-shell-admin__panel" id="knx-panel-branding">
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Branding</h3>
                            <p>Logo, name, drawer logo, and sizing controls.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div class="knx-shell-admin__grid">
                            <label>
                                <span>Brand Name</span>
                                <input type="text" name="brand_name" value="<?php echo esc_attr($settings['brand_name']); ?>">
                            </label>

                            <label>
                                <span>App Label</span>
                                <input type="text" name="app_label" value="<?php echo esc_attr($settings['app_label']); ?>" placeholder="Optional drawer subtitle">
                            </label>

                            <label>
                                <span>Initials</span>
                                <input type="text" name="initials" value="<?php echo esc_attr($settings['initials']); ?>" maxlength="6">
                            </label>

                            <label>
                                <span>Home URL</span>
                                <input type="text" name="home_url" value="<?php echo esc_attr($settings['home_url']); ?>">
                            </label>

                            <label class="knx-shell-admin__full">
                                <span>Logo URL</span>
                                <input type="url" id="knxShellLogoUrl" name="logo_url" value="<?php echo esc_attr($settings['logo_url']); ?>" placeholder="https://...">
                            </label>
                        </div>

                        <div class="knx-shell-admin__logo-row">
                            <div class="knx-shell-admin__logo-preview <?php echo !empty($settings['logo_url']) ? 'has-logo' : ''; ?>" id="knxShellLogoPreview">
                                <span><?php echo esc_html($settings['initials']); ?></span>
                                <?php if (!empty($settings['logo_url'])): ?>
                                    <img src="<?php echo esc_url($settings['logo_url']); ?>" alt="">
                                <?php endif; ?>
                            </div>

                            <div class="knx-shell-admin__logo-actions">
                                <button type="button" class="button" id="knxShellUploadLogo">Upload / Select Logo</button>
                                <button type="button" class="button" id="knxShellRemoveLogo">Remove</button>
                            </div>
                        </div>

                        <div class="knx-shell-admin__grid">
                            <label>
                                <span>Desktop Logo Height</span>
                                <input type="text" name="logo_height_desktop" value="<?php echo esc_attr($settings['logo_height_desktop']); ?>" placeholder="72px">
                            </label>

                            <label>
                                <span>Mobile Logo Height</span>
                                <input type="text" name="logo_height_mobile" value="<?php echo esc_attr($settings['logo_height_mobile']); ?>" placeholder="64px">
                            </label>

                            <label>
                                <span>Logo Max Width</span>
                                <input type="text" name="logo_max_width" value="<?php echo esc_attr($settings['logo_max_width']); ?>" placeholder="260px">
                            </label>

                            <label>
                                <span>Drawer Logo Size</span>
                                <input type="text" name="drawer_logo_size" value="<?php echo esc_attr($settings['drawer_logo_size']); ?>" placeholder="64px">
                            </label>
                        </div>

                        <div class="knx-shell-admin__theme-grid knx-shell-admin__drawer-logo-colors">
                            <div>
                                <h4>Drawer Logo Holder</h4>
                                <?php
                                knx_shell_admin_color_field('drawer_logo_bg', 'Drawer Logo Background', $settings['drawer_logo_bg']);
                                knx_shell_admin_color_field('drawer_logo_text', 'Drawer Logo Text', $settings['drawer_logo_text']);
                                ?>
                            </div>
                        </div>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Branding</button>
                        </div>
                    </div>
                </details>

                <details class="knx-shell-admin__panel" id="knx-panel-menu">
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Menu Builder</h3>
                            <p>Add, remove, reorder, and customize menus and submenus.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div id="knxShellMenuBuilder" class="knx-shell-menu-builder">
                            <?php foreach ($menu_items as $i => $item): ?>
                                <div class="knx-shell-menu-card" data-menu-card>
                                    <div class="knx-shell-menu-card__top">
                                        <strong>Menu Item</strong>
                                        <button type="button" class="button-link-delete" data-remove-menu>Remove</button>
                                    </div>

                                    <div class="knx-shell-admin__grid">
                                        <label>
                                            <span>Label</span>
                                            <input type="text" name="menu_items[<?php echo esc_attr($i); ?>][label]" value="<?php echo esc_attr($item['label'] ?? ''); ?>">
                                        </label>

                                        <label>
                                            <span>URL</span>
                                            <input type="text" name="menu_items[<?php echo esc_attr($i); ?>][url]" value="<?php echo esc_attr($item['url'] ?? ''); ?>">
                                        </label>

                                        <label>
                                            <span>Icon Class</span>
                                            <input type="text" name="menu_items[<?php echo esc_attr($i); ?>][icon]" value="<?php echo esc_attr($item['icon'] ?? ''); ?>" placeholder="fa-solid fa-house">
                                        </label>

                                        <label>
                                            <span>Priority</span>
                                            <input type="number" name="menu_items[<?php echo esc_attr($i); ?>][priority]" value="<?php echo esc_attr($item['priority'] ?? (($i + 1) * 10)); ?>">
                                        </label>

                                        <label>
                                            <span>Visibility</span>
                                            <select name="menu_items[<?php echo esc_attr($i); ?>][visibility]">
                                                <option value="all" <?php selected($item['visibility'] ?? 'all', 'all'); ?>>All</option>
                                                <option value="logged_in" <?php selected($item['visibility'] ?? 'all', 'logged_in'); ?>>Logged In</option>
                                                <option value="logged_out" <?php selected($item['visibility'] ?? 'all', 'logged_out'); ?>>Logged Out</option>
                                            </select>
                                        </label>

                                        <label>
                                            <span>Placement</span>
                                            <select name="menu_items[<?php echo esc_attr($i); ?>][placement]">
                                                <option value="all" <?php selected($item['placement'] ?? 'all', 'all'); ?>>All</option>
                                                <option value="desktop" <?php selected($item['placement'] ?? 'all', 'desktop'); ?>>Desktop Only</option>
                                                <option value="drawer" <?php selected($item['placement'] ?? 'all', 'drawer'); ?>>Drawer Only</option>
                                            </select>
                                        </label>

                                        <label>
                                            <span>Target</span>
                                            <select name="menu_items[<?php echo esc_attr($i); ?>][target]">
                                                <option value="self" <?php selected($item['target'] ?? 'self', 'self'); ?>>Same Tab</option>
                                                <option value="blank" <?php selected($item['target'] ?? 'self', 'blank'); ?>>New Tab</option>
                                            </select>
                                        </label>
                                    </div>

                                    <div class="knx-shell-submenu-wrap" data-submenu-wrap>
                                        <div class="knx-shell-submenu-wrap__head">
                                            <span>Submenus</span>
                                            <button type="button" class="button" data-add-submenu>Add Submenu</button>
                                        </div>

                                        <?php $children = isset($item['children']) && is_array($item['children']) ? $item['children'] : []; ?>
                                        <?php foreach ($children as $j => $child): ?>
                                            <div class="knx-shell-submenu-row" data-submenu-row>
                                                <input type="text" name="menu_items[<?php echo esc_attr($i); ?>][children][<?php echo esc_attr($j); ?>][label]" value="<?php echo esc_attr($child['label'] ?? ''); ?>" placeholder="Submenu label">
                                                <input type="text" name="menu_items[<?php echo esc_attr($i); ?>][children][<?php echo esc_attr($j); ?>][url]" value="<?php echo esc_attr($child['url'] ?? ''); ?>" placeholder="/submenu-url">
                                                <input type="text" name="menu_items[<?php echo esc_attr($i); ?>][children][<?php echo esc_attr($j); ?>][icon]" value="<?php echo esc_attr($child['icon'] ?? ''); ?>" placeholder="Icon class">
                                                <select name="menu_items[<?php echo esc_attr($i); ?>][children][<?php echo esc_attr($j); ?>][target]">
                                                    <option value="self" <?php selected($child['target'] ?? 'self', 'self'); ?>>Same</option>
                                                    <option value="blank" <?php selected($child['target'] ?? 'self', 'blank'); ?>>New</option>
                                                </select>
                                                <button type="button" class="button-link-delete" data-remove-submenu>Remove</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="button button-secondary knx-shell-admin__add-menu" id="knxShellAddMenu">
                            + Add Menu Item
                        </button>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Menus</button>
                        </div>
                    </div>
                </details>

                <details class="knx-shell-admin__panel" id="knx-panel-actions">
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Header Actions</h3>
                            <p>Optional action link and primary Login CTA.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div class="knx-shell-admin__grid">
                            <label>
                                <span>Action Label</span>
                                <input type="text" name="action_label" value="<?php echo esc_attr($settings['action_label']); ?>">
                            </label>

                            <label>
                                <span>Action URL</span>
                                <input type="text" name="action_url" value="<?php echo esc_attr($settings['action_url']); ?>">
                            </label>

                            <label>
                                <span>Action Icon</span>
                                <input type="text" name="action_icon" value="<?php echo esc_attr($settings['action_icon']); ?>" placeholder="fa-solid fa-phone">
                            </label>

                            <label>
                                <span>Action Style</span>
                                <select name="action_style">
                                    <option value="text" <?php selected($settings['action_style'], 'text'); ?>>Text</option>
                                    <option value="pill" <?php selected($settings['action_style'], 'pill'); ?>>Pill</option>
                                    <option value="hidden" <?php selected($settings['action_style'], 'hidden'); ?>>Hidden</option>
                                </select>
                            </label>

                            <label>
                                <span>CTA Label</span>
                                <input type="text" name="cta_label" value="<?php echo esc_attr($settings['cta_label']); ?>">
                            </label>

                            <label>
                                <span>CTA URL</span>
                                <input type="text" name="cta_url" value="<?php echo esc_attr($settings['cta_url']); ?>">
                            </label>

                            <label>
                                <span>CTA Icon</span>
                                <input type="text" name="cta_icon" value="<?php echo esc_attr($settings['cta_icon']); ?>" placeholder="fa-solid fa-arrow-right-to-bracket">
                            </label>

                            <label>
                                <span>CTA Style</span>
                                <select name="cta_style">
                                    <option value="light" <?php selected($settings['cta_style'], 'light'); ?>>Light</option>
                                    <option value="solid" <?php selected($settings['cta_style'], 'solid'); ?>>Solid</option>
                                    <option value="outline" <?php selected($settings['cta_style'], 'outline'); ?>>Outline</option>
                                    <option value="hidden" <?php selected($settings['cta_style'], 'hidden'); ?>>Hidden</option>
                                </select>
                            </label>
                        </div>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Actions</button>
                        </div>
                    </div>
                </details>

                <details class="knx-shell-admin__panel" id="knx-panel-mobile">
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Mobile Drawer</h3>
                            <p>Mobile hamburger and drawer options.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div class="knx-shell-admin__grid">
                            <label>
                                <span>Hamburger Style</span>
                                <select name="hamburger_style">
                                    <option value="basic" <?php selected($settings['hamburger_style'], 'basic'); ?>>Basic Lines</option>
                                    <option value="glass" <?php selected($settings['hamburger_style'], 'glass'); ?>>Glass Box</option>
                                    <option value="circle" <?php selected($settings['hamburger_style'], 'circle'); ?>>Minimal Circle</option>
                                    <option value="solid" <?php selected($settings['hamburger_style'], 'solid'); ?>>Solid Button</option>
                                </select>
                            </label>
                        </div>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Mobile</button>
                        </div>
                    </div>
                </details>

                <details class="knx-shell-admin__panel" id="knx-panel-theme">
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Theme Colors</h3>
                            <p>Reusable colors, gradients, opacity, and drawer styling.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div class="knx-shell-admin__theme-grid">
                            <div>
                                <h4>Core</h4>
                                <?php
                                knx_shell_admin_color_field('primary', 'Primary', $settings['primary']);
                                knx_shell_admin_color_field('accent', 'Accent', $settings['accent']);
                                knx_shell_admin_color_field('text', 'Navbar Text', $settings['text']);
                                knx_shell_admin_color_field('muted', 'Muted Text', $settings['muted']);
                                knx_shell_admin_color_field('bg', 'Page Background', $settings['bg']);
                                ?>
                            </div>

                            <div>
                                <h4>Buttons</h4>
                                <?php
                                knx_shell_admin_color_field('button_bg', 'Button Background', $settings['button_bg']);
                                knx_shell_admin_color_field('button_text', 'Button Text', $settings['button_text']);
                                ?>
                                <label><span>Button Opacity</span><input type="number" name="button_opacity" value="<?php echo esc_attr($settings['button_opacity']); ?>" min="0" max="1" step="0.01"></label>
                            </div>

                            <div>
                                <h4>Navbar Gradient</h4>
                                <?php
                                knx_shell_admin_color_field('navbar_gradient_1', 'Gradient Color 1', $settings['navbar_gradient_1']);
                                knx_shell_admin_color_field('navbar_gradient_2', 'Gradient Color 2', $settings['navbar_gradient_2']);
                                knx_shell_admin_color_field('navbar_gradient_3', 'Gradient Color 3', $settings['navbar_gradient_3']);
                                ?>
                                <label><span>Navbar Opacity</span><input type="number" name="navbar_opacity" value="<?php echo esc_attr($settings['navbar_opacity']); ?>" min="0" max="1" step="0.01"></label>
                                <label><span>Navbar Border Opacity</span><input type="number" name="navbar_border_opacity" value="<?php echo esc_attr($settings['navbar_border_opacity']); ?>" min="0" max="1" step="0.01"></label>
                            </div>

                            <div>
                                <h4>Sidebar / Drawer</h4>
                                <?php
                                knx_shell_admin_color_field('sidebar_bg', 'Sidebar Background', $settings['sidebar_bg']);
                                knx_shell_admin_color_field('sidebar_text', 'Sidebar Text', $settings['sidebar_text']);
                                ?>
                                <label><span>Sidebar Opacity</span><input type="number" name="sidebar_opacity" value="<?php echo esc_attr($settings['sidebar_opacity']); ?>" min="0" max="1" step="0.01"></label>
                                <label><span>Sidebar Border Opacity</span><input type="number" name="sidebar_border_opacity" value="<?php echo esc_attr($settings['sidebar_border_opacity']); ?>" min="0" max="1" step="0.01"></label>
                            </div>
                        </div>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Theme Colors</button>
                        </div>
                    </div>
                </details>

                <details class="knx-shell-admin__panel" id="knx-panel-advanced">
                    <summary class="knx-shell-admin__panel-head">
                        <span>
                            <h3>Advanced</h3>
                            <p>Shape, blur, and reset controls.</p>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </summary>

                    <div class="knx-shell-admin__panel-body">
                        <div class="knx-shell-admin__grid">
                            <label>
                                <span>Border Radius</span>
                                <input type="text" name="radius" value="<?php echo esc_attr($settings['radius']); ?>" placeholder="22px">
                            </label>

                            <label>
                                <span>Blur</span>
                                <input type="text" name="blur" value="<?php echo esc_attr($settings['blur']); ?>" placeholder="20px">
                            </label>

                            <label>
                                <span>Glass Opacity</span>
                                <input type="number" name="glass_opacity" value="<?php echo esc_attr($settings['glass_opacity']); ?>" min="0.20" max="1.00" step="0.01">
                            </label>
                        </div>

                        <div class="knx-shell-admin__reset-box">
                            <h4>Reset Theme</h4>
                            <p>Restores visual design defaults only. Branding, actions, menus, and submenus are preserved.</p>
                            <button type="submit" name="knx_shell_reset_theme" class="button button-secondary">
                                Reset Theme Defaults
                            </button>
                        </div>

                        <div class="knx-shell-admin__section-save">
                            <button type="submit" name="knx_shell_settings_submit" class="button button-primary">Save Advanced</button>
                        </div>
                    </div>
                </details>
            </main>
        </div>
    </form>
</div>