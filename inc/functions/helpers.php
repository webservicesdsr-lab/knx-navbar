<?php
if (!defined('ABSPATH')) exit;

/**
 * KNX Shell - Helper Functions
 *
 * Reusable global shell helpers for KNX-based WordPress products.
 */

if (!function_exists('knx_shell_default_menu_items')) {
    /**
     * Return default editable menu items.
     *
     * @return array
     */
    function knx_shell_default_menu_items() {
        return [
            [
                'label'      => 'Services',
                'url'        => '/services',
                'icon'       => '',
                'visibility' => 'all',
                'placement'  => 'all',
                'target'     => 'self',
                'priority'   => 10,
                'children'   => [
                    [
                        'label'  => 'Service One',
                        'url'    => '/services/service-one',
                        'icon'   => '',
                        'target' => 'self',
                    ],
                    [
                        'label'  => 'Service Two',
                        'url'    => '/services/service-two',
                        'icon'   => '',
                        'target' => 'self',
                    ],
                ],
            ],
            [
                'label'      => 'Projects',
                'url'        => '/projects',
                'icon'       => '',
                'visibility' => 'all',
                'placement'  => 'all',
                'target'     => 'self',
                'priority'   => 20,
                'children'   => [],
            ],
            [
                'label'      => 'About',
                'url'        => '/about',
                'icon'       => '',
                'visibility' => 'all',
                'placement'  => 'all',
                'target'     => 'self',
                'priority'   => 30,
                'children'   => [],
            ],
        ];
    }
}

if (!function_exists('knx_shell_default_settings')) {
    /**
     * Return default shell settings.
     *
     * @return array
     */
    function knx_shell_default_settings() {
        return [
            'enabled'       => '1',
            'design'        => 'solid',
            'layout'        => 'left',
            'drawer_side'   => 'right',
            'position_mode' => 'push',
            'is_sticky'     => '1',

            'brand_name'    => get_bloginfo('name') ?: 'KNX Shell',
            'app_label'     => '',
            'initials'      => 'KNX',
            'logo_url'      => '',
            'home_url'      => home_url('/'),

            'logo_height_desktop' => '72px',
            'logo_height_mobile'  => '64px',
            'logo_max_width'      => '260px',

            'drawer_logo_size' => '64px',
            'drawer_logo_bg'   => '#1E63C8',
            'drawer_logo_text' => '#ffffff',

            'hamburger_style' => 'basic',

            'action_label' => 'Contact',
            'action_url'   => '/contact',
            'action_icon'  => 'fa-solid fa-phone',
            'action_style' => 'text',

            'cta_label' => 'Login',
            'cta_url'   => '/login',
            'cta_icon'  => 'fa-solid fa-arrow-right-to-bracket',
            'cta_style' => 'light',

            'menu_items' => knx_shell_default_menu_items(),

            'primary' => '#1E63C8',
            'accent'  => '#0B1F3A',
            'text'    => '#ffffff',
            'muted'   => '#D6E6FF',

            'button_bg'      => '#ffffff',
            'button_text'    => '#1E63C8',
            'button_opacity' => '1.00',

            'navbar_gradient_1' => '#1E63C8',
            'navbar_gradient_2' => '#1557BB',
            'navbar_gradient_3' => '#0F4EA8',
            'navbar_opacity'    => '1.00',
            'navbar_border_opacity' => '0.20',

            'sidebar_bg'      => '#ffffff',
            'sidebar_text'    => '#0B1F3A',
            'sidebar_opacity' => '1.00',
            'sidebar_border_opacity' => '0.28',

            'bg'            => '#f7f8f9',
            'radius'        => '22px',
            'blur'          => '20px',
            'glass_opacity' => '0.78',
        ];
    }
}

if (!function_exists('knx_shell_theme_default_settings')) {
    /**
     * Return only visual theme defaults.
     *
     * @return array
     */
    function knx_shell_theme_default_settings() {
        $defaults = knx_shell_default_settings();

        return [
            'enabled'       => $defaults['enabled'],
            'design'        => $defaults['design'],
            'layout'        => $defaults['layout'],
            'drawer_side'   => $defaults['drawer_side'],
            'position_mode' => $defaults['position_mode'],
            'is_sticky'     => $defaults['is_sticky'],
            'hamburger_style' => $defaults['hamburger_style'],

            'logo_height_desktop' => $defaults['logo_height_desktop'],
            'logo_height_mobile'  => $defaults['logo_height_mobile'],
            'logo_max_width'      => $defaults['logo_max_width'],

            'drawer_logo_size' => $defaults['drawer_logo_size'],
            'drawer_logo_bg'   => $defaults['drawer_logo_bg'],
            'drawer_logo_text' => $defaults['drawer_logo_text'],

            'primary' => $defaults['primary'],
            'accent'  => $defaults['accent'],
            'text'    => $defaults['text'],
            'muted'   => $defaults['muted'],

            'button_bg'      => $defaults['button_bg'],
            'button_text'    => $defaults['button_text'],
            'button_opacity' => $defaults['button_opacity'],

            'navbar_gradient_1' => $defaults['navbar_gradient_1'],
            'navbar_gradient_2' => $defaults['navbar_gradient_2'],
            'navbar_gradient_3' => $defaults['navbar_gradient_3'],
            'navbar_opacity'    => $defaults['navbar_opacity'],
            'navbar_border_opacity' => $defaults['navbar_border_opacity'],

            'sidebar_bg'      => $defaults['sidebar_bg'],
            'sidebar_text'    => $defaults['sidebar_text'],
            'sidebar_opacity' => $defaults['sidebar_opacity'],
            'sidebar_border_opacity' => $defaults['sidebar_border_opacity'],

            'bg'            => $defaults['bg'],
            'radius'        => $defaults['radius'],
            'blur'          => $defaults['blur'],
            'glass_opacity' => $defaults['glass_opacity'],
        ];
    }
}

if (!function_exists('knx_shell_get_settings')) {
    /**
     * Return saved shell settings merged with defaults.
     *
     * @return array
     */
    function knx_shell_get_settings() {
        $defaults = knx_shell_default_settings();
        $settings = get_option('knx_shell_settings', []);

        if (!is_array($settings)) {
            $settings = [];
        }

        $settings = wp_parse_args($settings, $defaults);

        if (empty($settings['menu_items']) || !is_array($settings['menu_items'])) {
            $settings['menu_items'] = $defaults['menu_items'];
        }

        $legacy_cta_labels = ['Get Estimate', 'Get Started'];

        if (isset($settings['cta_label']) && in_array((string) $settings['cta_label'], $legacy_cta_labels, true)) {
            $settings['cta_label'] = 'Login';
            $settings['cta_url'] = '/login';
            $settings['cta_icon'] = 'fa-solid fa-arrow-right-to-bracket';
            $settings['cta_style'] = 'light';
        }

        return $settings;
    }
}

if (!function_exists('knx_shell_hex_to_rgb')) {
    /**
     * Convert hex color to RGB string.
     *
     * @param string $hex Hex color.
     * @return string
     */
    function knx_shell_hex_to_rgb($hex) {
        $hex = sanitize_hex_color($hex);

        if (!$hex) {
            return '0, 0, 0';
        }

        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
    }
}

if (!function_exists('knx_shell_sanitize_px')) {
    /**
     * Sanitize pixel value.
     *
     * @param mixed  $value Input value.
     * @param string $fallback Fallback.
     * @return string
     */
    function knx_shell_sanitize_px($value, $fallback = '20px') {
        $value = is_string($value) ? trim($value) : '';

        if (preg_match('/^\d{1,3}px$/', $value)) {
            return $value;
        }

        return $fallback;
    }
}

if (!function_exists('knx_shell_get_session')) {
    /**
     * Return current KNX session when KNX Auth & Core is active.
     *
     * @return object|false
     */
    function knx_shell_get_session() {
        if (function_exists('knx_get_session')) {
            return knx_get_session();
        }

        return false;
    }
}

if (!function_exists('knx_shell_is_logged_in')) {
    /**
     * Check if current visitor has a KNX session.
     *
     * @return bool
     */
    function knx_shell_is_logged_in() {
        return (bool) knx_shell_get_session();
    }
}

if (!function_exists('knx_shell_current_role')) {
    /**
     * Return current KNX role.
     *
     * @return string
     */
    function knx_shell_current_role() {
        $session = knx_shell_get_session();

        if (!$session || empty($session->role)) {
            return '';
        }

        return sanitize_key((string) $session->role);
    }
}

if (!function_exists('knx_shell_user_display_name')) {
    /**
     * Return display name for navbar.
     *
     * @return string
     */
    function knx_shell_user_display_name() {
        $session = knx_shell_get_session();

        if (!$session) {
            return '';
        }

        if (!empty($session->name)) {
            return (string) $session->name;
        }

        if (!empty($session->username)) {
            return (string) $session->username;
        }

        if (!empty($session->email)) {
            return (string) $session->email;
        }

        return 'Account';
    }
}

if (!function_exists('knx_shell_role_allowed')) {
    /**
     * Check if a nav item is allowed for the current role.
     *
     * @param array|string $roles Allowed roles.
     * @return bool
     */
    function knx_shell_role_allowed($roles = []) {
        if (empty($roles)) {
            return true;
        }

        $current = knx_shell_current_role();

        if ($current === '') {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        $roles = array_map('sanitize_key', $roles);

        return in_array($current, $roles, true);
    }
}

if (!function_exists('knx_shell_get_brand')) {
    /**
     * Return shell branding.
     *
     * @return array
     */
    function knx_shell_get_brand() {
        $settings = knx_shell_get_settings();

        $brand = [
            'name'      => $settings['brand_name'],
            'logo_url'  => esc_url_raw($settings['logo_url']),
            'home_url'  => esc_url_raw($settings['home_url']),
            'app_label' => $settings['app_label'],
            'initials'  => $settings['initials'],
        ];

        return apply_filters('knx_shell_brand', $brand);
    }
}

if (!function_exists('knx_shell_get_actions')) {
    /**
     * Return shell header actions.
     *
     * @return array
     */
    function knx_shell_get_actions() {
        $settings = knx_shell_get_settings();

        $actions = [
            'action_label' => $settings['action_label'],
            'action_url'   => $settings['action_url'],
            'action_icon'  => $settings['action_icon'],
            'action_style' => $settings['action_style'],
            'cta_label'    => $settings['cta_label'],
            'cta_url'      => $settings['cta_url'],
            'cta_icon'     => $settings['cta_icon'],
            'cta_style'    => $settings['cta_style'],
        ];

        return apply_filters('knx_shell_actions', $actions);
    }
}

if (!function_exists('knx_shell_get_theme')) {
    /**
     * Return shell design tokens.
     *
     * @return array
     */
    function knx_shell_get_theme() {
        $settings = knx_shell_get_settings();

        $theme = [
            'enabled'       => $settings['enabled'],
            'design'        => sanitize_key($settings['design']),
            'layout'        => sanitize_key($settings['layout']),
            'drawer_side'   => sanitize_key($settings['drawer_side']),
            'position_mode' => sanitize_key($settings['position_mode']),
            'is_sticky'     => (string) $settings['is_sticky'],
            'hamburger_style' => sanitize_key($settings['hamburger_style']),

            'logo_height_desktop' => knx_shell_sanitize_px($settings['logo_height_desktop'], '72px'),
            'logo_height_mobile'  => knx_shell_sanitize_px($settings['logo_height_mobile'], '64px'),
            'logo_max_width'      => knx_shell_sanitize_px($settings['logo_max_width'], '260px'),

            'drawer_logo_size' => knx_shell_sanitize_px($settings['drawer_logo_size'], '64px'),
            'drawer_logo_bg'   => $settings['drawer_logo_bg'],
            'drawer_logo_text' => $settings['drawer_logo_text'],

            'primary' => $settings['primary'],
            'accent'  => $settings['accent'],
            'text'    => $settings['text'],
            'muted'   => $settings['muted'],

            'button_bg'      => $settings['button_bg'],
            'button_text'    => $settings['button_text'],
            'button_opacity' => $settings['button_opacity'],

            'navbar_gradient_1' => $settings['navbar_gradient_1'],
            'navbar_gradient_2' => $settings['navbar_gradient_2'],
            'navbar_gradient_3' => $settings['navbar_gradient_3'],
            'navbar_opacity'    => $settings['navbar_opacity'],
            'navbar_border_opacity' => $settings['navbar_border_opacity'],

            'sidebar_bg'      => $settings['sidebar_bg'],
            'sidebar_text'    => $settings['sidebar_text'],
            'sidebar_opacity' => $settings['sidebar_opacity'],
            'sidebar_border_opacity' => $settings['sidebar_border_opacity'],

            'bg'            => $settings['bg'],
            'radius'        => $settings['radius'],
            'blur'          => $settings['blur'],
            'glass_opacity' => $settings['glass_opacity'],
        ];

        if (!in_array($theme['design'], ['glass', 'solid', 'dark'], true)) {
            $theme['design'] = 'solid';
        }

        if (!in_array($theme['layout'], ['left', 'center', 'split'], true)) {
            $theme['layout'] = 'left';
        }

        if (!in_array($theme['drawer_side'], ['current', 'left', 'right'], true)) {
            $theme['drawer_side'] = 'right';
        }

        if (!in_array($theme['position_mode'], ['push', 'overlay'], true)) {
            $theme['position_mode'] = 'push';
        }

        if (!in_array($theme['hamburger_style'], ['basic', 'glass', 'circle', 'solid'], true)) {
            $theme['hamburger_style'] = 'basic';
        }

        $theme['is_sticky'] = $theme['is_sticky'] === '1' ? '1' : '0';

        return apply_filters('knx_shell_theme', $theme);
    }
}

if (!function_exists('knx_shell_normalize_url')) {
    /**
     * Normalize stored menu URLs.
     *
     * @param string $url URL.
     * @return string
     */
    function knx_shell_normalize_url($url) {
        $url = trim((string) $url);

        if ($url === '') {
            return '#';
        }

        if ($url[0] === '/') {
            return site_url($url);
        }

        return $url;
    }
}

if (!function_exists('knx_shell_get_nav_items')) {
    /**
     * Return filtered nav items.
     *
     * @param string $placement desktop|mobile|drawer|all
     * @return array
     */
    function knx_shell_get_nav_items($placement = 'all') {
        $settings = knx_shell_get_settings();
        $items = isset($settings['menu_items']) && is_array($settings['menu_items'])
            ? $settings['menu_items']
            : knx_shell_default_menu_items();

        $items = apply_filters('knx_shell_nav_items', $items);

        if (!is_array($items)) {
            return [];
        }

        $is_logged = knx_shell_is_logged_in();

        $items = array_filter($items, function ($item) use ($placement, $is_logged) {
            if (!is_array($item)) {
                return false;
            }

            $label = isset($item['label']) ? trim((string) $item['label']) : '';

            if ($label === '') {
                return false;
            }

            $visibility = isset($item['visibility']) ? sanitize_key((string) $item['visibility']) : 'all';

            if ($visibility === 'logged_in' && !$is_logged) {
                return false;
            }

            if ($visibility === 'logged_out' && $is_logged) {
                return false;
            }

            $item_placement = isset($item['placement']) ? sanitize_key((string) $item['placement']) : 'all';

            if ($placement !== 'all' && $item_placement !== 'all' && $item_placement !== $placement) {
                return false;
            }

            $roles = isset($item['roles']) ? $item['roles'] : [];

            return knx_shell_role_allowed($roles);
        });

        usort($items, function ($a, $b) {
            $pa = isset($a['priority']) ? (int) $a['priority'] : 100;
            $pb = isset($b['priority']) ? (int) $b['priority'] : 100;

            return $pa <=> $pb;
        });

        return array_values($items);
    }
}