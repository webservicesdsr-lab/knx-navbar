// KNX Shell - Navbar Script

(function () {
  'use strict';

  function onReady(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback);
      return;
    }

    callback();
  }

  onReady(function () {
    const root = document.querySelector('.knx-shell-root');
    const drawer = document.getElementById('knxShellDrawer');
    const overlay = document.getElementById('knxShellOverlay');
    const mobileToggle = document.getElementById('knxShellMobileToggle');
    const closeBtn = document.getElementById('knxShellDrawerClose');

    const userWrap = document.getElementById('knxShellUserWrap');
    const userToggle = document.getElementById('knxShellUserToggle');
    const userMenu = document.getElementById('knxShellUserMenu');

    if (!root) {
      return;
    }

    let drawerOpen = false;
    let userMenuOpen = false;

    function isMobileNav() {
      return window.innerWidth <= 1120;
    }

    function setDrawerButtonStates(open) {
      if (mobileToggle) {
        mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      }

      if (drawer) {
        drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
      }
    }

    function openDrawer(event) {
      if (event) {
        event.preventDefault();
      }

      if (!drawer || !overlay || drawerOpen) {
        return;
      }

      closeUserMenu();

      drawerOpen = true;
      root.classList.add('is-drawer-open');
      document.body.classList.add('knx-shell-lock');
      overlay.hidden = false;
      setDrawerButtonStates(true);
    }

    function closeDrawer(event) {
      if (event) {
        event.preventDefault();
      }

      if (!drawer || !overlay || !drawerOpen) {
        return;
      }

      drawerOpen = false;
      root.classList.remove('is-drawer-open');
      document.body.classList.remove('knx-shell-lock');
      overlay.hidden = true;
      setDrawerButtonStates(false);
    }

    function toggleDrawer(event) {
      if (drawerOpen) {
        closeDrawer(event);
      } else {
        openDrawer(event);
      }
    }

    function openUserMenu(event) {
      if (event) {
        event.preventDefault();
      }

      if (!userToggle || !userMenu || userMenuOpen) {
        return;
      }

      closeDrawer();

      userMenuOpen = true;
      userMenu.classList.add('is-open');
      userMenu.setAttribute('aria-hidden', 'false');
      userToggle.setAttribute('aria-expanded', 'true');
    }

    function closeUserMenu() {
      if (!userToggle || !userMenu || !userMenuOpen) {
        return;
      }

      userMenuOpen = false;
      userMenu.classList.remove('is-open');
      userMenu.setAttribute('aria-hidden', 'true');
      userToggle.setAttribute('aria-expanded', 'false');
    }

    function toggleUserMenu(event) {
      if (isMobileNav()) {
        openDrawer(event);
        return;
      }

      if (userMenuOpen) {
        closeUserMenu();
      } else {
        openUserMenu(event);
      }
    }

    if (mobileToggle) {
      mobileToggle.addEventListener('click', toggleDrawer);
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeDrawer);
    }

    if (overlay) {
      overlay.addEventListener('click', closeDrawer);
    }

    if (userToggle) {
      userToggle.addEventListener('click', toggleUserMenu);
    }

    document.addEventListener('click', function (event) {
      if (userMenuOpen && userWrap && !userWrap.contains(event.target)) {
        closeUserMenu();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeDrawer();
        closeUserMenu();
      }
    });

    if (drawer) {
      drawer.addEventListener('click', function (event) {
        const link = event.target.closest('a');
        const submitButton = event.target.closest('button[type="submit"]');

        if (link || submitButton) {
          closeDrawer();
        }
      });
    }

    window.addEventListener('resize', function () {
      if (!isMobileNav()) {
        closeDrawer();
      }
    });

    window.KNXShell = window.KNXShell || {};
    window.KNXShell.openNav = openDrawer;
    window.KNXShell.closeNav = closeDrawer;
    window.KNXShell.toggleNav = toggleDrawer;
  });
})();