(() => {
	'use strict';

	const roots = document.querySelectorAll('[data-cb-dictionary-search][data-live-search="1"]');
	if (!roots.length) {
		return;
	}

	const findTarget = (source) => Array.from(document.querySelectorAll('[data-cb-dictionary-results]'))
		.find((candidate) => candidate.dataset.cbDictionaryResults === source);

	roots.forEach((root) => {
		const form = root.querySelector('.cb-dictionary-search__form');
		const input = root.querySelector('.cb-dictionary-search__input');
		if (!form || !input) {
			return;
		}

		const source = root.dataset.cbDictionarySearch || 'default';
		const endpoint = root.dataset.endpoint || '';
		const minChars = Math.max(1, Math.min(10, Number.parseInt(root.dataset.minChars || '2', 10) || 2));
		const loadingLabel = root.dataset.loadingLabel || 'Searching…';
		const noResultsLabel = root.dataset.noResultsLabel || 'No matching dictionary entries found.';
		const errorLabel = root.dataset.errorLabel || 'Live search is temporarily unavailable. Submit the form to search.';
		let debounceTimer = 0;
		let request = null;
		let activeIndex = -1;

		const parts = () => {
			const target = findTarget(source);
			if (!target) {
				return null;
			}

			const list = target.querySelector('.cb-dictionary-search-results__items');
			const status = target.querySelector('[data-cb-dictionary-search-status]');
			const count = target.querySelector('[data-cb-dictionary-search-count]');
			if (!list || !status || !count) {
				return null;
			}

			return { target, list, status, count };
		};

		const options = (list) => Array.from(list.querySelectorAll('.cb-dictionary-search-results__item'));

		const setExpanded = (target, expanded) => {
			input.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			target.hidden = !expanded;
		};

		const clearActive = (list) => {
			options(list).forEach((option) => option.setAttribute('aria-selected', 'false'));
			activeIndex = -1;
			input.removeAttribute('aria-activedescendant');
		};

		const setActive = (list, index) => {
			const items = options(list);
			if (!items.length) {
				clearActive(list);
				return;
			}

			activeIndex = Math.max(0, Math.min(items.length - 1, index));
			items.forEach((option, optionIndex) => {
				option.setAttribute('aria-selected', optionIndex === activeIndex ? 'true' : 'false');
			});

			const active = items[activeIndex];
			if (active.id) {
				input.setAttribute('aria-activedescendant', active.id);
			}
			active.scrollIntoView({ block: 'nearest' });
		};

		const resetResults = () => {
			if (request) {
				request.abort();
				request = null;
			}
			window.clearTimeout(debounceTimer);
			const current = parts();
			if (!current) {
				return;
			}
			current.list.replaceChildren();
			current.status.textContent = '';
			current.count.textContent = '';
			current.count.hidden = true;
			current.target.removeAttribute('aria-busy');
			clearActive(current.list);
			setExpanded(current.target, false);
		};

		const createText = (className, value) => {
			const node = document.createElement('span');
			node.className = className;
			node.textContent = value;
			return node;
		};

		const renderItems = (payload, current) => {
			const items = Array.isArray(payload.items) ? payload.items : [];
			const showExcerpt = current.target.dataset.showExcerpt === '1';
			const showCount = current.target.dataset.showCount === '1';

			current.list.replaceChildren();
			clearActive(current.list);
			current.status.textContent = '';
			current.count.textContent = '';
			current.count.hidden = true;

			if (!items.length) {
				current.status.textContent = noResultsLabel;
				setExpanded(current.target, true);
				return;
			}

			items.forEach((item, index) => {
				if (!item || !item.title || !item.permalink) {
					return;
				}

				const option = document.createElement('li');
				option.className = 'cb-dictionary-search-results__item';
				option.id = `${current.list.id}-option-${index + 1}`;
				option.setAttribute('role', 'option');
				option.setAttribute('aria-selected', 'false');

				const link = document.createElement('a');
				link.className = 'cb-dictionary-search-results__link';
				link.href = item.permalink;
				link.tabIndex = -1;
				link.append(createText('cb-dictionary-search-results__title', item.title));

				if (showExcerpt && item.excerpt) {
					link.append(createText('cb-dictionary-search-results__excerpt', item.excerpt));
				}

				option.append(link);
				current.list.append(option);
			});

			if (!options(current.list).length) {
				current.status.textContent = noResultsLabel;
			}

			if (showCount && options(current.list).length) {
				current.count.textContent = payload.count_label || String(options(current.list).length);
				current.count.hidden = false;
			}

			setExpanded(current.target, true);
		};

		const search = async (term) => {
			const current = parts();
			if (!endpoint || !current) {
				return;
			}

			if (request) {
				request.abort();
			}

			const controller = new AbortController();
			request = controller;
			current.status.textContent = loadingLabel;
			current.count.textContent = '';
			current.count.hidden = true;
			current.list.replaceChildren();
			clearActive(current.list);
			current.target.setAttribute('aria-busy', 'true');
			setExpanded(current.target, true);

			const limit = Math.max(1, Math.min(100, Number.parseInt(current.target.dataset.limit || '30', 10) || 30));
			const url = new URL(endpoint, window.location.origin);
			url.searchParams.set('q', term);
			url.searchParams.set('limit', String(limit));

			try {
				const response = await fetch(url.toString(), {
					method: 'GET',
					headers: { Accept: 'application/json' },
					signal: controller.signal,
					credentials: 'same-origin',
				});
				if (!response.ok) {
					throw new Error(`Dictionary search request failed with ${response.status}`);
				}

				const payload = await response.json();
				if (input.value.trim() !== term) {
					return;
				}
				renderItems(payload, current);
			} catch (error) {
				if (error && error.name === 'AbortError') {
					return;
				}
				current.status.textContent = errorLabel;
				current.count.textContent = '';
				current.count.hidden = true;
				current.list.replaceChildren();
				clearActive(current.list);
				setExpanded(current.target, true);
			} finally {
				current.target.removeAttribute('aria-busy');
				if (request === controller) {
					request = null;
				}
			}
		};

		input.addEventListener('input', () => {
			const term = input.value.trim();
			window.clearTimeout(debounceTimer);
			if (term.length < minChars) {
				resetResults();
				return;
			}
			debounceTimer = window.setTimeout(() => search(term), 250);
		});

		input.addEventListener('keydown', (event) => {
			const current = parts();
			if (!current) {
				return;
			}
			const items = options(current.list);

			if (event.key === 'ArrowDown' && items.length) {
				event.preventDefault();
				setActive(current.list, activeIndex < items.length - 1 ? activeIndex + 1 : 0);
				return;
			}
			if (event.key === 'ArrowUp' && items.length) {
				event.preventDefault();
				setActive(current.list, activeIndex > 0 ? activeIndex - 1 : items.length - 1);
				return;
			}
			if (event.key === 'Enter' && activeIndex >= 0 && items[activeIndex]) {
				const link = items[activeIndex].querySelector('a[href]');
				if (link) {
					event.preventDefault();
					window.location.assign(link.href);
				}
				return;
			}
			if (event.key === 'Escape') {
				clearActive(current.list);
				setExpanded(current.target, false);
			}
		});

		input.addEventListener('focus', () => {
			const current = parts();
			if (current && (options(current.list).length || current.status.textContent)) {
				setExpanded(current.target, true);
			}
		});

		document.addEventListener('pointerdown', (event) => {
			const current = parts();
			if (!current || root.contains(event.target) || current.target.contains(event.target)) {
				return;
			}
			clearActive(current.list);
			setExpanded(current.target, false);
		});
	});
})();
