/**
 * Mobile navigation (burger) for the BMA redesign header.
 */
(function () {
	'use strict';

	var burger = document.getElementById('bma-burger');
	var panel = document.getElementById('bma-mobile-nav');
	if (!burger || !panel) {
		return;
	}

	var panelInner = panel.querySelector('.bma-mobile-nav-panel');
	var openLabel = (window.bmaMobileNav && bmaMobileNav.openLabel) || burger.getAttribute('aria-label') || 'Menü megnyitása';
	var closeLabel = (window.bmaMobileNav && bmaMobileNav.closeLabel) || 'Menü bezárása';
	var lastFocus = null;

	function focusables() {
		if (!panelInner) {
			return [];
		}
		return Array.prototype.slice.call(
			panelInner.querySelectorAll(
				'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
			)
		).filter(function (el) {
			return el.offsetParent !== null || el === document.activeElement;
		});
	}

	function isOpen() {
		return burger.getAttribute('aria-expanded') === 'true';
	}

	function openNav() {
		if (isOpen()) {
			return;
		}
		lastFocus = document.activeElement;
		panel.hidden = false;
		panel.setAttribute('aria-hidden', 'false');
		burger.setAttribute('aria-expanded', 'true');
		burger.setAttribute('aria-label', closeLabel);
		document.body.classList.add('bma-nav-open');
		var items = focusables();
		if (items.length) {
			items[0].focus();
		} else {
			burger.focus();
		}
	}

	function closeNav() {
		if (!isOpen()) {
			return;
		}
		panel.hidden = true;
		panel.setAttribute('aria-hidden', 'true');
		burger.setAttribute('aria-expanded', 'false');
		burger.setAttribute('aria-label', openLabel);
		document.body.classList.remove('bma-nav-open');
		if (lastFocus && typeof lastFocus.focus === 'function') {
			lastFocus.focus();
		} else {
			burger.focus();
		}
	}

	function toggleNav() {
		if (isOpen()) {
			closeNav();
		} else {
			openNav();
		}
	}

	burger.addEventListener('click', function (e) {
		e.preventDefault();
		toggleNav();
	});

	document.addEventListener('keydown', function (e) {
		if (!isOpen()) {
			return;
		}
		if (e.key === 'Escape') {
			e.preventDefault();
			closeNav();
			return;
		}
		if (e.key !== 'Tab') {
			return;
		}
		var items = focusables();
		if (!items.length) {
			return;
		}
		var first = items[0];
		var last = items[items.length - 1];
		if (e.shiftKey && document.activeElement === first) {
			e.preventDefault();
			last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault();
			first.focus();
		}
	});

	panel.addEventListener('click', function (e) {
		if (e.target === panel) {
			closeNav();
		}
	});

	panel.addEventListener('click', function (e) {
		var link = e.target.closest('a');
		if (link && panel.contains(link)) {
			closeNav();
		}
	});

	window.addEventListener('resize', function () {
		if (window.matchMedia('(min-width: 901px)').matches && isOpen()) {
			closeNav();
		}
	});
})();
