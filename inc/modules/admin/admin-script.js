// KNX Shell - Admin Settings

(function ($) {
  'use strict';

  function normalizeHex(value) {
    if (!value) return '';

    value = String(value).trim();

    if (value.charAt(0) !== '#') {
      value = '#' + value;
    }

    value = value.toUpperCase();

    if (/^#[0-9A-F]{3}$/.test(value)) {
      return '#' + value.charAt(1) + value.charAt(1) + value.charAt(2) + value.charAt(2) + value.charAt(3) + value.charAt(3);
    }

    if (/^#[0-9A-F]{6}$/.test(value)) {
      return value;
    }

    return '';
  }

  function hexToRgbArray(hex) {
    hex = normalizeHex(hex);

    if (!hex) {
      return [0, 0, 0];
    }

    return [
      parseInt(hex.slice(1, 3), 16),
      parseInt(hex.slice(3, 5), 16),
      parseInt(hex.slice(5, 7), 16)
    ];
  }

  function hexToRgb(hex) {
    const rgb = hexToRgbArray(hex);
    return 'rgb(' + rgb[0] + ', ' + rgb[1] + ', ' + rgb[2] + ')';
  }

  function syncColorField($field, source, forceNormalize) {
    const $picker = $field.find('[data-knx-color-picker]');
    const $hex = $field.find('[data-knx-color-hex]');
    const $rgb = $field.find('[data-knx-color-rgb]');

    let rawValue = source === 'picker' ? $picker.val() : $hex.val();
    let hex = normalizeHex(rawValue);

    if (!hex && !forceNormalize) {
      $rgb.val('');
      return;
    }

    if (!hex) {
      hex = normalizeHex($picker.val()) || '#000000';
    }

    $picker.val(hex.toLowerCase());
    $hex.val(hex);
    $rgb.val(hexToRgb(hex));
  }

  function refreshMenuIndexes() {
    $('#knxShellMenuBuilder [data-menu-card]').each(function (menuIndex) {
      const $card = $(this);

      $card.find('input, select').each(function () {
        const $field = $(this);
        const name = $field.attr('name');

        if (!name) return;

        const updated = name.replace(/menu_items\[\d+\]/, 'menu_items[' + menuIndex + ']');
        $field.attr('name', updated);
      });

      $card.find('[data-submenu-row]').each(function (childIndex) {
        $(this).find('input, select').each(function () {
          const $field = $(this);
          const name = $field.attr('name');

          if (!name) return;

          const updated = name.replace(/\[children\]\[\d+\]/, '[children][' + childIndex + ']');
          $field.attr('name', updated);
        });
      });
    });
  }

  function menuTemplate(index) {
    return '' +
      '<div class="knx-shell-menu-card" data-menu-card>' +
        '<div class="knx-shell-menu-card__top">' +
          '<strong>Menu Item</strong>' +
          '<button type="button" class="button-link-delete" data-remove-menu>Remove</button>' +
        '</div>' +
        '<div class="knx-shell-admin__grid">' +
          '<label><span>Label</span><input type="text" name="menu_items[' + index + '][label]" value=""></label>' +
          '<label><span>URL</span><input type="text" name="menu_items[' + index + '][url]" value=""></label>' +
          '<label><span>Icon Class</span><input type="text" name="menu_items[' + index + '][icon]" value="" placeholder="fa-solid fa-house"></label>' +
          '<label><span>Priority</span><input type="number" name="menu_items[' + index + '][priority]" value="' + ((index + 1) * 10) + '"></label>' +
          '<label><span>Visibility</span><select name="menu_items[' + index + '][visibility]"><option value="all">All</option><option value="logged_in">Logged In</option><option value="logged_out">Logged Out</option></select></label>' +
          '<label><span>Placement</span><select name="menu_items[' + index + '][placement]"><option value="all">All</option><option value="desktop">Desktop Only</option><option value="drawer">Drawer Only</option></select></label>' +
          '<label><span>Target</span><select name="menu_items[' + index + '][target]"><option value="self">Same Tab</option><option value="blank">New Tab</option></select></label>' +
        '</div>' +
        '<div class="knx-shell-submenu-wrap" data-submenu-wrap>' +
          '<div class="knx-shell-submenu-wrap__head">' +
            '<span>Submenus</span>' +
            '<button type="button" class="button" data-add-submenu>Add Submenu</button>' +
          '</div>' +
        '</div>' +
      '</div>';
  }

  function submenuTemplate(menuIndex, childIndex) {
    return '' +
      '<div class="knx-shell-submenu-row" data-submenu-row>' +
        '<input type="text" name="menu_items[' + menuIndex + '][children][' + childIndex + '][label]" value="" placeholder="Submenu label">' +
        '<input type="text" name="menu_items[' + menuIndex + '][children][' + childIndex + '][url]" value="" placeholder="/submenu-url">' +
        '<input type="text" name="menu_items[' + menuIndex + '][children][' + childIndex + '][icon]" value="" placeholder="Icon class">' +
        '<select name="menu_items[' + menuIndex + '][children][' + childIndex + '][target]"><option value="self">Same</option><option value="blank">New</option></select>' +
        '<button type="button" class="button-link-delete" data-remove-submenu>Remove</button>' +
      '</div>';
  }

  function restoreScroll() {
    const savedY = sessionStorage.getItem('knx_shell_admin_scroll_y');

    if (!savedY) return;

    sessionStorage.removeItem('knx_shell_admin_scroll_y');

    setTimeout(function () {
      window.scrollTo(0, parseInt(savedY, 10) || 0);
    }, 80);
  }

  function storeScrollBeforeSubmit() {
    $('#knxShellSettingsForm').on('submit', function () {
      sessionStorage.setItem('knx_shell_admin_scroll_y', String(window.scrollY || 0));
    });
  }

  function rememberOpenPanels() {
    const storageKey = 'knx_shell_open_panels';

    function saveOpenPanels() {
      const openIds = [];

      $('.knx-shell-admin__panel').each(function () {
        if (this.open && this.id) {
          openIds.push(this.id);
        }
      });

      localStorage.setItem(storageKey, JSON.stringify(openIds));
    }

    function restoreOpenPanels() {
      let openIds = null;

      try {
        openIds = JSON.parse(localStorage.getItem(storageKey) || 'null');
      } catch (e) {
        openIds = null;
      }

      if (!Array.isArray(openIds)) return;

      $('.knx-shell-admin__panel').each(function () {
        if (!this.id) return;
        this.open = openIds.indexOf(this.id) !== -1;
      });
    }

    restoreOpenPanels();

    $(document).on('toggle', '.knx-shell-admin__panel', function () {
      saveOpenPanels();
    });
  }

  $(function () {
    const $logoInput = $('#knxShellLogoUrl');
    const $logoPreview = $('#knxShellLogoPreview');
    const $uploadBtn = $('#knxShellUploadLogo');
    const $removeBtn = $('#knxShellRemoveLogo');

    let mediaFrame = null;

    function updateLogoPreview(url) {
      if (!url) {
        $logoPreview.removeClass('has-logo');
        $logoPreview.find('img').remove();
        return;
      }

      $logoPreview.addClass('has-logo');

      if ($logoPreview.find('img').length) {
        $logoPreview.find('img').attr('src', url);
      } else {
        $logoPreview.append('<img src="' + url + '" alt="">');
      }
    }

    $uploadBtn.on('click', function (event) {
      event.preventDefault();

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: 'Select Shell Logo',
        button: {
          text: 'Use this logo'
        },
        multiple: false
      });

      mediaFrame.on('select', function () {
        const attachment = mediaFrame.state().get('selection').first().toJSON();

        if (!attachment || !attachment.url) {
          return;
        }

        $logoInput.val(attachment.url);
        updateLogoPreview(attachment.url);
      });

      mediaFrame.open();
    });

    $removeBtn.on('click', function (event) {
      event.preventDefault();

      $logoInput.val('');
      updateLogoPreview('');
    });

    $logoInput.on('input', function () {
      updateLogoPreview($(this).val());
    });

    $('[data-knx-color-field]').each(function () {
      syncColorField($(this), 'hex', true);
    });

    $(document).on('input change', '[data-knx-color-picker]', function () {
      syncColorField($(this).closest('[data-knx-color-field]'), 'picker', true);
    });

    $(document).on('input', '[data-knx-color-hex]', function () {
      syncColorField($(this).closest('[data-knx-color-field]'), 'hex', false);
    });

    $(document).on('blur change', '[data-knx-color-hex]', function () {
      syncColorField($(this).closest('[data-knx-color-field]'), 'hex', true);
    });

    $('#knxShellAddMenu').on('click', function () {
      const index = $('#knxShellMenuBuilder [data-menu-card]').length;
      $('#knxShellMenuBuilder').append(menuTemplate(index));
      refreshMenuIndexes();
    });

    $(document).on('click', '[data-remove-menu]', function () {
      $(this).closest('[data-menu-card]').remove();
      refreshMenuIndexes();
    });

    $(document).on('click', '[data-add-submenu]', function () {
      const $card = $(this).closest('[data-menu-card]');
      const menuIndex = $('#knxShellMenuBuilder [data-menu-card]').index($card);
      const childIndex = $card.find('[data-submenu-row]').length;

      $card.find('[data-submenu-wrap]').append(submenuTemplate(menuIndex, childIndex));
      refreshMenuIndexes();
    });

    $(document).on('click', '[data-remove-submenu]', function () {
      $(this).closest('[data-submenu-row]').remove();
      refreshMenuIndexes();
    });

    storeScrollBeforeSubmit();
    rememberOpenPanels();
    restoreScroll();
  });
})(jQuery);