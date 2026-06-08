<?php
if (!defined('ABSPATH')) exit;

/**
 * KNX Shell - Global Navbar
 */

if (!function_exists('knx_shell_render_attr_target')) {
    /**
     * Render target attributes.
     *
     * @param string $target Target.
     * @return void
     */
    function knx_shell_render_attr_target($target) {
        if ($target === 'blank') {
            echo ' target="_blank" rel="noopener noreferrer"';
        }
    }
}

if (!function_exists('knx_shell_render_desktop_nav_item')) {
    /**
     * Render a desktop nav item.
     *
     * @param array $item Nav item.
     * @return void
     */
    function knx_shell_render_desktop_nav_item(array $item) {
        $label = isset($item['label']) ? (string) $item['label'] : '';
        $url   = isset($item['url']) ? knx_shell_normalize_url((string) $item['url']) : '#';
        $icon  = isset($item['icon']) ? (string) $item['icon'] : '';
        $target = isset($item['target']) ? sanitize_key((string) $item['target']) : 'self';
        $children = isset($item['children']) && is_array($item['children']) ? $item['children'] : [];

        if ($label === '') {
            return;
        }

        $has_children = !empty($children);

        echo '<div class="' . esc_attr($has_children ? 'knx-shell-nav__item has-submenu' : 'knx-shell-nav__item') . '">';
        echo '<a class="knx-shell-nav__link" href="' . esc_url($url) . '"';
        knx_shell_render_attr_target($target);
        echo '>';

        if ($icon !== '') {
            echo '<i class="' . esc_attr($icon) . '" aria-hidden="true"></i>';
        }

        echo '<span>' . esc_html($label) . '</span>';

        if ($has_children) {
            echo '<i class="fa-solid fa-chevron-down knx-shell-nav__chevron" aria-hidden="true"></i>';
        }

        echo '</a>';

        if ($has_children) {
            echo '<div class="knx-shell-nav__submenu">';

            foreach ($children as $child) {
                if (!is_array($child)) {
                    continue;
                }

                $child_label = isset($child['label']) ? (string) $child['label'] : '';
                $child_url   = isset($child['url']) ? knx_shell_normalize_url((string) $child['url']) : '#';
                $child_icon  = isset($child['icon']) ? (string) $child['icon'] : '';
                $child_target = isset($child['target']) ? sanitize_key((string) $child['target']) : 'self';

                if ($child_label === '') {
                    continue;
                }

                echo '<a class="knx-shell-nav__submenu-item" href="' . esc_url($child_url) . '"';
                knx_shell_render_attr_target($child_target);
                echo '>';

                if ($child_icon !== '') {
                    echo '<i class="' . esc_attr($child_icon) . '" aria-hidden="true"></i>';
                }

                echo '<span>' . esc_html($child_label) . '</span>';
                echo '</a>';
            }

            echo '</div>';
        }

        echo '</div>';
    }
}

if (!function_exists('knx_shell_render_drawer_nav_item')) {
    /**
     * Render drawer nav item.
     *
     * @param array $item Nav item.
     * @return void
     */
    function knx_shell_render_drawer_nav_item(array $item) {
        $label = isset($item['label']) ? (string) $item['label'] : '';
        $url   = isset($item['url']) ? knx_shell_normalize_url((string) $item['url']) : '#';
        $icon  = isset($item['icon']) ? (string) $item['icon'] : '';
        $target = isset($item['target']) ? sanitize_key((string) $item['target']) : 'self';
        $children = isset($item['children']) && is_array($item['children']) ? $item['children'] : [];

        if ($label === '') {
            return;
        }

        echo '<a class="knx-shell-drawer__item" href="' . esc_url($url) . '"';
        knx_shell_render_attr_target($target);
        echo '>';

        if ($icon !== '') {
            echo '<i class="' . esc_attr($icon) . '" aria-hidden="true"></i>';
        }

        echo '<span>' . esc_html($label) . '</span>';
        echo '</a>';

        if (!empty($children)) {
            echo '<div class="knx-shell-drawer__children">';

            foreach ($children as $child) {
                if (!is_array($child)) {
                    continue;
                }

                $child_label = isset($child['label']) ? (string) $child['label'] : '';
                $child_url   = isset($child['url']) ? knx_shell_normalize_url((string) $child['url']) : '#';
                $child_icon  = isset($child['icon']) ? (string) $child['icon'] : '';
                $child_target = isset($child['target']) ? sanitize_key((string) $child['target']) : 'self';

                if ($child_label === '') {
                    continue;
                }

                echo '<a class="knx-shell-drawer__child" href="' . esc_url($child_url) . '"';
                knx_shell_render_attr_target($child_target);
                echo '>';

                if ($child_icon !== '') {
                    echo '<i class="' . esc_attr($child_icon) . '" aria-hidden="true"></i>';
                }

                echo '<span>' . esc_html($child_label) . '</span>';
                echo '</a>';
            }

            echo '</div>';
        }
    }
}

if (!function_exists('knx_shell_render_navbar')) {
    /**
     * Render global navbar.
     *
     * @return void
     */
    function knx_shell_render_navbar() {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $rendered = true;

        $theme = function_exists('knx_shell_get_theme') ? knx_shell_get_theme() : [];
        $brand = function_exists('knx_shell_get_brand') ? knx_shell_get_brand() : [];
        $actions = function_exists('knx_shell_get_actions') ? knx_shell_get_actions() : [];

        $theme = wp_parse_args($theme, knx_shell_default_settings());
        $brand = wp_parse_args($brand, [
            'name' => get_bloginfo('name') ?: 'KNX Shell',
            'logo_url' => '',
            'home_url' => home_url('/'),
            'app_label' => '',
            'initials' => 'KNX',
        ]);

        $actions = wp_parse_args($actions, [
            'action_label' => '',
            'action_url' => '',
            'action_icon' => '',
            'action_style' => 'text',
            'cta_label' => 'Login',
            'cta_url' => '/login',
            'cta_icon' => 'fa-solid fa-arrow-right-to-bracket',
            'cta_style' => 'light',
        ]);

        $legacy_cta_labels = ['Get Estimate', 'Get Started'];

        if (empty($actions['cta_label']) || in_array((string) $actions['cta_label'], $legacy_cta_labels, true)) {
            $actions['cta_label'] = 'Login';
            $actions['cta_url'] = '/login';
            $actions['cta_icon'] = 'fa-solid fa-arrow-right-to-bracket';
            $actions['cta_style'] = 'light';
        }

        $enabled = apply_filters('knx_shell_navbar_enabled', $theme['enabled'] === '1');

        if (!$enabled) {
            return;
        }

        $session = function_exists('knx_shell_get_session') ? knx_shell_get_session() : false;
        $is_logged = (bool) $session;
        $display_name = $is_logged && function_exists('knx_shell_user_display_name') ? knx_shell_user_display_name() : '';
        $role = function_exists('knx_shell_current_role') ? knx_shell_current_role() : '';

        $nav_items = function_exists('knx_shell_get_nav_items') ? knx_shell_get_nav_items('all') : [];

        $style_vars = sprintf(
            '--knx-shell-primary:%s;--knx-shell-accent:%s;--knx-shell-text:%s;--knx-shell-muted:%s;--knx-shell-button-bg:%s;--knx-shell-button-text:%s;--knx-shell-button-opacity:%s;--knx-shell-button-bg-rgb:%s;--knx-shell-nav-gradient-1-rgb:%s;--knx-shell-nav-gradient-2-rgb:%s;--knx-shell-nav-gradient-3-rgb:%s;--knx-shell-nav-opacity:%s;--knx-shell-nav-border-opacity:%s;--knx-shell-sidebar-bg-rgb:%s;--knx-shell-sidebar-text:%s;--knx-shell-sidebar-opacity:%s;--knx-shell-sidebar-border-opacity:%s;--knx-shell-bg:%s;--knx-shell-radius:%s;--knx-shell-blur:%s;--knx-shell-glass-opacity:%s;--knx-shell-logo-height-desktop:%s;--knx-shell-logo-height-mobile:%s;--knx-shell-logo-max-width:%s;--knx-shell-drawer-logo-size:%s;--knx-shell-drawer-logo-bg:%s;--knx-shell-drawer-logo-text:%s;',
            esc_attr($theme['primary']),
            esc_attr($theme['accent']),
            esc_attr($theme['text']),
            esc_attr($theme['muted']),
            esc_attr($theme['button_bg']),
            esc_attr($theme['button_text']),
            esc_attr($theme['button_opacity']),
            esc_attr(function_exists('knx_shell_hex_to_rgb') ? knx_shell_hex_to_rgb($theme['button_bg']) : '255, 255, 255'),
            esc_attr(function_exists('knx_shell_hex_to_rgb') ? knx_shell_hex_to_rgb($theme['navbar_gradient_1']) : '30, 99, 200'),
            esc_attr(function_exists('knx_shell_hex_to_rgb') ? knx_shell_hex_to_rgb($theme['navbar_gradient_2']) : '21, 87, 187'),
            esc_attr(function_exists('knx_shell_hex_to_rgb') ? knx_shell_hex_to_rgb($theme['navbar_gradient_3']) : '15, 78, 168'),
            esc_attr($theme['navbar_opacity']),
            esc_attr($theme['navbar_border_opacity']),
            esc_attr(function_exists('knx_shell_hex_to_rgb') ? knx_shell_hex_to_rgb($theme['sidebar_bg']) : '255, 255, 255'),
            esc_attr($theme['sidebar_text']),
            esc_attr($theme['sidebar_opacity']),
            esc_attr($theme['sidebar_border_opacity']),
            esc_attr($theme['bg']),
            esc_attr($theme['radius']),
            esc_attr($theme['blur']),
            esc_attr($theme['glass_opacity']),
            esc_attr($theme['logo_height_desktop']),
            esc_attr($theme['logo_height_mobile']),
            esc_attr($theme['logo_max_width']),
            esc_attr($theme['drawer_logo_size']),
            esc_attr($theme['drawer_logo_bg']),
            esc_attr($theme['drawer_logo_text'])
        );

        $root_class = implode(' ', [
            'knx-shell-root',
            'knx-shell-root--' . sanitize_key($theme['design']),
            'knx-shell-layout--' . sanitize_key($theme['layout']),
            'knx-shell-drawer--' . sanitize_key($theme['drawer_side']),
            'knx-shell-position--' . sanitize_key($theme['position_mode']),
            'knx-shell-hamburger--' . sanitize_key($theme['hamburger_style']),
            $theme['is_sticky'] === '1' ? 'knx-shell-sticky' : 'knx-shell-static',
        ]);

        echo '<link rel="stylesheet" href="' . esc_url(KNX_SHELL_URL . 'inc/modules/navbar/navbar-style.css?v=' . KNX_SHELL_VERSION) . '">';
        echo '<script src="' . esc_url(KNX_SHELL_URL . 'inc/modules/navbar/navbar-script.js?v=' . KNX_SHELL_VERSION) . '" defer></script>';
        ?>

        <div class="<?php echo esc_attr($root_class); ?>" style="<?php echo esc_attr($style_vars); ?>">
            <nav class="knx-shell-nav" id="knxShellNav" aria-label="Global navigation">
                <div class="knx-shell-nav__inner">
                    <div class="knx-shell-nav__left">
                        <a class="knx-shell-brand" href="<?php echo esc_url($brand['home_url']); ?>" aria-label="<?php echo esc_attr($brand['name']); ?>">
                            <?php if (!empty($brand['logo_url'])): ?>
                                <span class="knx-shell-brand__logo">
                                    <img src="<?php echo esc_url($brand['logo_url']); ?>" alt="<?php echo esc_attr($brand['name']); ?>" loading="eager" decoding="async">
                                </span>
                            <?php else: ?>
                                <span class="knx-shell-brand__initials"><?php echo esc_html($brand['initials']); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>

                    <?php if (!empty($nav_items)): ?>
                        <div class="knx-shell-nav__links" aria-label="Main navigation">
                            <?php foreach ($nav_items as $item): ?>
                                <?php if (is_array($item)) knx_shell_render_desktop_nav_item($item); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="knx-shell-nav__actions">
                        <?php if ($actions['action_style'] !== 'hidden' && !empty($actions['action_label']) && !empty($actions['action_url'])): ?>
                            <a class="knx-shell-action knx-shell-action--<?php echo esc_attr($actions['action_style']); ?>" href="<?php echo esc_url(knx_shell_normalize_url($actions['action_url'])); ?>">
                                <?php if (!empty($actions['action_icon'])): ?>
                                    <i class="<?php echo esc_attr($actions['action_icon']); ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                                <span><?php echo esc_html($actions['action_label']); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($actions['cta_style'] !== 'hidden' && !empty($actions['cta_label']) && !empty($actions['cta_url'])): ?>
                            <a class="knx-shell-cta knx-shell-cta--<?php echo esc_attr($actions['cta_style']); ?>" href="<?php echo esc_url(knx_shell_normalize_url($actions['cta_url'])); ?>">
                                <span><?php echo esc_html($actions['cta_label']); ?></span>
                                <?php if (!empty($actions['cta_icon'])): ?>
                                    <i class="<?php echo esc_attr($actions['cta_icon']); ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($is_logged): ?>
                            <div class="knx-shell-user-wrap" id="knxShellUserWrap">
                                <button type="button" class="knx-shell-user-btn" id="knxShellUserToggle" aria-expanded="false" aria-controls="knxShellUserMenu" aria-label="Open account menu">
                                    <span class="knx-shell-user-btn__avatar"><?php echo esc_html(strtoupper(substr($display_name ?: 'U', 0, 1))); ?></span>
                                    <span class="knx-shell-user-btn__copy">
                                        <strong><?php echo esc_html($display_name ?: 'Account'); ?></strong>
                                        <?php if ($role !== ''): ?>
                                            <small><?php echo esc_html(str_replace('_', ' ', $role)); ?></small>
                                        <?php endif; ?>
                                    </span>
                                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                </button>

                                <div class="knx-shell-user-menu" id="knxShellUserMenu" aria-hidden="true">
                                    <a href="<?php echo esc_url(site_url('/profile')); ?>" class="knx-shell-user-menu__item">
                                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                                        <span>Profile</span>
                                    </a>

                                    <form method="post" class="knx-shell-user-menu__logout">
                                        <?php wp_nonce_field('knx_logout_action', 'knx_logout_nonce'); ?>
                                        <button type="submit" name="knx_logout" class="knx-shell-user-menu__item">
                                            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <button type="button" class="knx-shell-menu-btn knx-shell-menu-btn--mobile" id="knxShellMobileToggle" aria-expanded="false" aria-controls="knxShellDrawer" aria-label="Open navigation">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="knx-shell-overlay" id="knxShellOverlay" hidden></div>

            <aside class="knx-shell-drawer" id="knxShellDrawer" aria-hidden="true" aria-label="Navigation menu">
                <div class="knx-shell-drawer__panel">
                    <div class="knx-shell-drawer__header">
                        <div class="knx-shell-drawer__brand">
                            <?php if (!empty($brand['logo_url'])): ?>
                                <span class="knx-shell-drawer__logo">
                                    <img src="<?php echo esc_url($brand['logo_url']); ?>" alt="<?php echo esc_attr($brand['name']); ?>">
                                </span>
                            <?php else: ?>
                                <span class="knx-shell-drawer__logo"><?php echo esc_html($brand['initials']); ?></span>
                            <?php endif; ?>

                            <div>
                                <strong><?php echo esc_html($brand['name']); ?></strong>
                                <?php if (!empty($brand['app_label'])): ?>
                                    <span><?php echo esc_html($brand['app_label']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button type="button" class="knx-shell-drawer__close" id="knxShellDrawerClose" aria-label="Close menu">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="knx-shell-drawer__body">
                        <?php if (!empty($nav_items)): ?>
                            <div class="knx-shell-drawer__section">
                                <span class="knx-shell-drawer__label">Menu</span>

                                <?php foreach ($nav_items as $item): ?>
                                    <?php if (is_array($item)) knx_shell_render_drawer_nav_item($item); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="knx-shell-drawer__footer">
                        <?php if ($actions['action_style'] !== 'hidden' && !empty($actions['action_label']) && !empty($actions['action_url'])): ?>
                            <a class="knx-shell-drawer__phone" href="<?php echo esc_url(knx_shell_normalize_url($actions['action_url'])); ?>">
                                <?php if (!empty($actions['action_icon'])): ?>
                                    <i class="<?php echo esc_attr($actions['action_icon']); ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                                <span><?php echo esc_html($actions['action_label']); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($actions['cta_style'] !== 'hidden' && !empty($actions['cta_label']) && !empty($actions['cta_url'])): ?>
                            <a class="knx-shell-login knx-shell-login--drawer" href="<?php echo esc_url(knx_shell_normalize_url($actions['cta_url'])); ?>">
                                <span><?php echo esc_html($actions['cta_label']); ?></span>
                                <?php if (!empty($actions['cta_icon'])): ?>
                                    <i class="<?php echo esc_attr($actions['cta_icon']); ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
        <?php
    }
}