

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('click', (event) => {
	const link = event.target.closest('a[href]');
	if (!link || event.defaultPrevented || link.target === '_blank' || link.origin !== window.location.origin) {
		return;
	}

	document.documentElement.classList.add('is-navigating');
});

window.addEventListener('pageshow', () => {
	document.documentElement.classList.remove('is-navigating');
});
