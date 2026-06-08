# KNX Shell

Reusable global UI shell for KNX-based WordPress products.

KNX Shell provides a customizable, global navbar system that can be reused across different product verticals such as remodeling, home services, marketplaces, delivery, taxis, CRM portals, internal dashboards, and other KNX-based applications.

The plugin is intentionally product-agnostic. It should not contain hardcoded business-specific labels such as a remodeling company name, service names, phone number, or estimate CTA. Those values are configurable from the WordPress admin panel.

---

## Purpose

KNX Shell exists to provide a consistent global navigation layer for modular WordPress products.

It handles:

- Global navbar rendering
- Desktop navigation
- Mobile drawer navigation
- Custom menu and submenu builder
- Branding and logo controls
- Header action and CTA controls
- Theme colors and gradients
- Hamburger style options
- Drawer/sidebar appearance
- Sticky or static behavior
- Push-content or overlay mode
- Compatibility with KNX Auth & Core when available

---

## Plugin Philosophy

This plugin should remain reusable and product-neutral.

Do not hardcode project-specific business logic into KNX Shell.

Good examples:

- `Login`
- `Contact`
- `Services`
- `Projects`
- Custom menu labels configured through admin
- Custom colors configured through admin
- Filters for product-specific extensions

Avoid hardcoding:

- Specific company names
- Specific phone numbers
- Specific service categories
- Specific marketplace categories
- Specific customer flows
- Specific product names

Product-specific plugins should customize KNX Shell through admin settings or filters.

---

## Current Core Features

### 1. Global Navbar

The theme can render the navbar with:

```php
if (function_exists('knx_shell_render_navbar')) {
    knx_shell_render_navbar();
}
```

Recommended placement is inside the theme header, immediately after the opening site wrapper.

Example:

```php
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <?php
    if (function_exists('knx_shell_render_navbar')) {
        knx_shell_render_navbar();
    }
    ?>
```

---

### 2. Admin Dashboard

The WordPress admin panel includes a builder-style dashboard for configuring the shell.

Sections include:

- Overview
- Branding
- Menu Builder
- Header Actions
- Mobile Drawer
- Theme Colors
- Advanced

Each section is collapsible and includes its own Save button so the user does not have to scroll to the bottom of the page.

The admin UI preserves scroll position after saving.

---

### 3. Branding Controls

The admin panel supports:

- Brand name
- Optional app label
- Initials fallback
- Logo URL
- WordPress media logo upload
- Home URL
- Desktop logo height
- Mobile logo height
- Logo max width
- Drawer logo size
- Drawer logo background color
- Drawer logo text color

The frontend logo wrapper is intentionally minimal so the logo can visually fill more of the navbar height.

If a logo still appears too small, the image file may contain transparent padding. In that case, use a cropped PNG/SVG or adjust the logo height and max width from the admin panel.

---

### 4. Menu Builder

Menus are fully editable from the plugin admin panel.

Each menu item supports:

- Label
- URL
- Icon class
- Priority
- Visibility
- Placement
- Target
- Submenus

Visibility options:

- All
- Logged In
- Logged Out

Placement options:

- All
- Desktop Only
- Drawer Only

Target options:

- Same Tab
- New Tab

Each submenu supports:

- Label
- URL
- Icon class
- Target

Menus are stored in the `knx_shell_settings` option under `menu_items`.

---

### 5. Header Actions

The header supports one optional action link and one primary CTA.

Default primary CTA:

- Label: `Login`
- URL: `/login`
- Icon: `fa-solid fa-arrow-right-to-bracket`
- Style: `Light`

The code includes legacy fallback protection so older saved labels like `Get Estimate` or `Get Started` are automatically treated as `Login` unless changed manually.

Action link fields:

- Action label
- Action URL
- Action icon
- Action style

CTA fields:

- CTA label
- CTA URL
- CTA icon
- CTA style

CTA style options:

- Light
- Solid
- Outline
- Hidden

Action style options:

- Text
- Pill
- Hidden

---

### 6. Mobile Drawer

The mobile drawer renders the same global menu items and submenus.

The drawer supports:

- Left drawer
- Right drawer
- Bottom sheet
- Drawer logo holder customization
- Drawer background color
- Drawer text color
- Drawer opacity
- Drawer border opacity

The drawer no longer shows a hardcoded fallback label like `Navigation`. If the app label is empty, no subtitle is rendered.

---

### 7. Hamburger Styles

The hamburger toggle uses three lines.

Available styles:

- Basic Lines
- Glass Box
- Minimal Circle
- Solid Button

CSS root classes:

```text
knx-shell-hamburger--basic
knx-shell-hamburger--glass
knx-shell-hamburger--circle
knx-shell-hamburger--solid
```

The open state transforms the first and third lines into an X while hiding the center line.

---

### 8. Theme Controls

The Theme Colors panel supports manual hex editing and color picker selection.

Editable fields include:

- Primary color
- Accent color
- Navbar text color
- Muted text color
- Page background
- Button background
- Button text
- Button opacity
- Navbar gradient color 1
- Navbar gradient color 2
- Navbar gradient color 3
- Navbar opacity
- Navbar border opacity
- Sidebar background
- Sidebar text
- Sidebar opacity
- Sidebar border opacity

The hex inputs are editable manually. Partial values are not force-normalized while typing.

---

## Frontend Design Modes

### Solid Pro

Solid Pro is the main premium global style.

It provides:

- Full-width gradient navbar
- Clean desktop layout
- Logo left by default
- Centered desktop menu
- Right-side action and CTA
- Mobile centered logo behavior
- Drawer-based mobile menu

### Liquid Glass

Liquid Glass keeps a more translucent floating-card look.

### Dark Command

Dark Command is intended for dashboard/admin-style products.

---

## Files

Expected plugin structure:

```text
knx-shell/
├── knx-shell.php
└── inc/
    ├── functions/
    │   └── helpers.php
    └── modules/
        ├── admin/
        │   ├── admin-settings.php
        │   ├── admin-style.css
        │   └── admin-script.js
        └── navbar/
            ├── navbar.php
            ├── navbar-style.css
            └── navbar-script.js
```

---

## Filters

KNX Shell should remain extendable by other plugins.

Available filters:

```php
apply_filters('knx_shell_brand', $brand);
apply_filters('knx_shell_actions', $actions);
apply_filters('knx_shell_theme', $theme);
apply_filters('knx_shell_nav_items', $items);
apply_filters('knx_shell_navbar_enabled', $enabled);
```

### Example: Add a Product-Specific Menu Item

```php
add_filter('knx_shell_nav_items', function ($items) {
    $items[] = [
        'label'      => 'Dashboard',
        'url'        => '/dashboard',
        'icon'       => 'fa-solid fa-table-columns',
        'visibility' => 'logged_in',
        'placement'  => 'all',
        'target'     => 'self',
        'priority'   => 90,
        'children'   => [],
    ];

    return $items;
});
```

### Example: Override Branding

```php
add_filter('knx_shell_brand', function ($brand) {
    $brand['name'] = 'My Product';
    $brand['home_url'] = home_url('/');

    return $brand;
});
```

---

## KNX Auth & Core Compatibility

KNX Shell can work alone, but it is designed to integrate with KNX Auth & Core when available.

If `knx_get_session()` exists, KNX Shell uses it to detect:

- Logged-in state
- Current user display name
- Current role
- Role-aware menu visibility

When KNX Auth & Core is not active, the navbar still renders, but session-aware features are limited.

---

## Development Notes

- Keep KNX Shell global and reusable.
- Do not hardcode business-specific content.
- Use admin settings and filters for project-specific customization.
- Preserve `knx_shell_nav_items`, `knx_shell_brand`, `knx_shell_actions`, and `knx_shell_theme` filters.
- Keep code comments in English.
- Keep chat/project documentation in Spanish when working with the current user.
- Avoid using `wp_footer` fallbacks.
- The frontend currently prints CSS/JS directly from the render function for compatibility with the current project approach.

---

## Recommended Next Steps

After KNX Shell is stable, the next product layer should configure it for the active product.

For Remodel North Texas, the product-specific plugin or settings should define:

- Brand logo
- Brand colors
- Public menu items
- Login URL
- Customer dashboard URL
- Contractor/vendor dashboard URL
- CRM portal links
- Estimate or lead capture flow

KNX Shell should provide the global UI container. Product-specific plugins should provide business logic.

---

## Status

Current status:

```text
KNX Shell Builder
- Global navbar system
- Editable menus and submenus
- Editable desktop and mobile layout
- Editable colors
- Editable drawer logo holder
- Three-line hamburger
- Login CTA default
- Collapsible admin dashboard sections
- Section-level save buttons
```

This README represents the current development direction and should be kept updated as the plugin evolves.
