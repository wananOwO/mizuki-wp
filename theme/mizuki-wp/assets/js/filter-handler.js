/**
 * Unified filter handler for Mizuki WP theme
 * Handles tag/category filtering for Friends, Projects, Skills, and Timeline pages
 *
 * @package Mizuki
 */

(function () {
	'use strict';

	/**
	 * Initialize filter tabs functionality
	 * @param {boolean} reset - Whether to reset existing initialization
	 */
	function initFilterTabs(reset) {
		const containers = document.querySelectorAll('.filter-tabs');

		containers.forEach(function (container) {
			// Skip if already initialized (unless reset)
			if (!reset && container.dataset.initialized) return;
			container.dataset.initialized = 'true';

			const tabs = container.querySelectorAll('.filter-tabs-item');
			const filterAttr = tabs[0] ? tabs[0].dataset.filterAttr : null;

			if (!filterAttr) return;

			// Find filterable items
			const dataSelector = '[data-' + filterAttr + ']';
			const parent = container.closest('.card-base') || document;
			const items = parent.querySelectorAll(dataSelector);
			const noResults = parent.querySelector('#no-results');
			const itemsContainer = parent.querySelector('[id$="-grid"], [id$="-list"]');

			if (items.length === 0) return;

			// Add click handler to each tab
			tabs.forEach(function (tab) {
				tab.addEventListener('click', function () {
					// Update active state
					tabs.forEach(function (t) {
						t.classList.remove('active');
					});
					tab.classList.add('active');

					const activeValue = tab.dataset.filterValue || 'all';
					let visibleCount = 0;

					// Filter items
					items.forEach(function (item) {
						const itemValue = item.dataset[filterAttr];
						let match = false;

						if (activeValue === 'all') {
							match = true;
						} else if (itemValue) {
							// Support comma-separated values (for tags)
							const values = itemValue.split(',').map(v => v.trim());
							match = values.indexOf(activeValue) !== -1;
						}

						if (match) {
							item.classList.remove('filtered-out');
							item.style.display = '';
							visibleCount++;
						} else {
							item.classList.add('filtered-out');
							item.style.display = 'none';
						}
					});

					// Show/hide no results message
					if (noResults) {
						if (visibleCount === 0) {
							noResults.classList.remove('hidden');
							if (itemsContainer) {
								itemsContainer.classList.add('hidden');
							}
						} else {
							noResults.classList.add('hidden');
							if (itemsContainer) {
								itemsContainer.classList.remove('hidden');
							}
						}
					}
				});
			});
		});
	}

	/**
	 * Initialize search functionality for friends page
	 */
	function initFriendsSearch() {
		const searchInput = document.getElementById('friend-search');
		const friendsGrid = document.getElementById('friends-grid');
		const noResults = document.getElementById('no-results');

		if (!searchInput || !friendsGrid) return;

		const friendCards = friendsGrid.querySelectorAll('.friend-card');
		const tagFilters = document.querySelectorAll('.filter-tag');
		let currentTag = 'all';

		// Search handler
		searchInput.addEventListener('input', function (e) {
			const searchTerm = e.target.value.toLowerCase();
			filterFriends(searchTerm, currentTag);
		});

		// Tag filter handler
		tagFilters.forEach(function (button) {
			button.addEventListener('click', function () {
				// Update active state
				tagFilters.forEach(function (btn) {
					btn.classList.remove('active');
				});
				button.classList.add('active');

				currentTag = button.dataset.tag || 'all';
				const searchTerm = searchInput.value.toLowerCase();
				filterFriends(searchTerm, currentTag);
			});
		});

		// Combined filter function
		function filterFriends(searchTerm, tag) {
			let visibleCount = 0;

			friendCards.forEach(function (card) {
				const title = (card.dataset.title || '').toLowerCase();
				const desc = (card.dataset.desc || '').toLowerCase();
				const tags = card.dataset.tags || '';

				const matchesSearch = !searchTerm ||
					title.indexOf(searchTerm) >= 0 ||
					desc.indexOf(searchTerm) >= 0;

				const matchesTag = tag === 'all' ||
					tags.split(',').map(t => t.trim()).indexOf(tag) >= 0;

				if (matchesSearch && matchesTag) {
					card.style.display = '';
					visibleCount++;
				} else {
					card.style.display = 'none';
				}
			});

			// Toggle no results message
			if (visibleCount === 0) {
				noResults.classList.remove('hidden');
				friendsGrid.classList.add('hidden');
			} else {
				noResults.classList.add('hidden');
				friendsGrid.classList.remove('hidden');
			}
		}
	}

	/**
	 * Main initialization function
	 */
	function init() {
		// Initialize filter tabs (for projects, skills, timeline)
		if (document.querySelector('.filter-tabs')) {
			initFilterTabs(false);
		}

		// Initialize friends search (if on friends page)
		if (document.querySelector('.friends-page')) {
			initFriendsSearch();
		}
	}

	// Run on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	// Expose for external use (e.g., AJAX page loads)
	window.mizukiInitFilters = function() {
		initFilterTabs(true);
		initFriendsSearch();
	};
})();
