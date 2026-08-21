/**
 * cPanel-Inspired Grid Dashboard Interactivity for sasPanel
 * Strictly Vanilla JavaScript - Zero Heavy Dependencies
 */

(() => {
	const STORAGE_KEY = 'saspanel_cpanel_collapsed_categories';

	function initCpanelDashboard() {
		const dashboard = document.querySelector('.cpanel-dashboard-container');
		if (!dashboard) return;

		const searchInput = document.getElementById('cpanelSearchInput');
		const searchClearBtn = document.getElementById('cpanelSearchClear');
		const noResultsBox = document.getElementById('cpanelNoResults');
		const resetSearchBtn = document.getElementById('cpanelResetSearch');
		const btnExpandAll = document.getElementById('cpanelExpandAll');
		const btnCollapseAll = document.getElementById('cpanelCollapseAll');
		const categoryCards = document.querySelectorAll('.cpanel-category-card');
		const toolItems = document.querySelectorAll('.cpanel-tool-item');

		// 1. Restore Collapsed Categories from localStorage
		let collapsedCategories = [];
		try {
			const saved = localStorage.getItem(STORAGE_KEY);
			if (saved) {
				collapsedCategories = JSON.parse(saved);
			}
		} catch (_e) {
			collapsedCategories = [];
		}

		categoryCards.forEach((card) => {
			const catId = card.getAttribute('data-category-id');
			if (catId && collapsedCategories.includes(catId)) {
				card.classList.add('is-collapsed');
			}

			// Add click listener to header
			const header = card.querySelector('.cpanel-category-header');
			if (header) {
				header.addEventListener('click', () => {
					// Toggle collapsed class
					card.classList.toggle('is-collapsed');
					saveCategoryStates();
				});
			}
		});

		function saveCategoryStates() {
			const collapsed = [];
			document.querySelectorAll('.cpanel-category-card.is-collapsed').forEach((card) => {
				const catId = card.getAttribute('data-category-id');
				if (catId) collapsed.push(catId);
			});
			try {
				localStorage.setItem(STORAGE_KEY, JSON.stringify(collapsed));
			} catch (_e) {}
		}

		// 2. Expand All & Collapse All Buttons
		if (btnExpandAll) {
			btnExpandAll.addEventListener('click', () => {
				categoryCards.forEach((card) => {
					card.classList.remove('is-collapsed');
				});
				saveCategoryStates();
			});
		}

		if (btnCollapseAll) {
			btnCollapseAll.addEventListener('click', () => {
				categoryCards.forEach((card) => {
					card.classList.add('is-collapsed');
				});
				saveCategoryStates();
			});
		}

		// 3. Store Original Tool Texts for Highlighting
		toolItems.forEach((item) => {
			const nameEl = item.querySelector('.cpanel-tool-name');
			const descEl = item.querySelector('.cpanel-tool-desc');
			if (nameEl) {
				item._originalName = nameEl.textContent;
			}
			if (descEl) {
				item._originalDesc = descEl.textContent;
			}
		});

		// 4. Live Search Filtering Function
		function performSearch(query) {
			const term = query.trim().toLowerCase();
			let totalVisibleTools = 0;

			if (term.length > 0) {
				if (searchClearBtn) searchClearBtn.style.display = 'flex';
			} else {
				if (searchClearBtn) searchClearBtn.style.display = 'none';
			}

			categoryCards.forEach((card) => {
				const items = card.querySelectorAll('.cpanel-tool-item');
				let visibleInCategory = 0;

				items.forEach((item) => {
					const name = item._originalName || '';
					const desc = item._originalDesc || '';
					const keywords = item.getAttribute('data-keywords') || '';
					const combined = `${name} ${desc} ${keywords}`.toLowerCase();

					const nameEl = item.querySelector('.cpanel-tool-name');
					const descEl = item.querySelector('.cpanel-tool-desc');

					if (term === '' || combined.includes(term)) {
						item.style.display = 'flex';
						visibleInCategory++;
						totalVisibleTools++;

						// Highlight matches
						if (term !== '' && nameEl) {
							nameEl.innerHTML = highlightMatch(name, term);
						} else if (nameEl) {
							nameEl.textContent = name;
						}

						if (term !== '' && descEl && desc) {
							descEl.innerHTML = highlightMatch(desc, term);
						} else if (descEl && desc) {
							descEl.textContent = desc;
						}
					} else {
						item.style.display = 'none';
						if (nameEl) nameEl.textContent = name;
						if (descEl && desc) descEl.textContent = desc;
					}
				});

				// Category count badge
				const countBadge = card.querySelector('.cpanel-category-count');
				if (countBadge) {
					if (term === '') {
						countBadge.textContent = items.length.toString();
					} else {
						countBadge.textContent = `${visibleInCategory} / ${items.length}`;
					}
				}

				// If category has no matches, hide it during search
				if (visibleInCategory === 0 && term !== '') {
					card.style.display = 'none';
				} else {
					card.style.display = 'block';
					// Auto expand categories during active search
					if (term !== '') {
						card.classList.remove('is-collapsed');
					}
				}
			});

			// Toggle No Results View
			if (noResultsBox) {
				if (totalVisibleTools === 0 && term !== '') {
					noResultsBox.classList.add('is-visible');
				} else {
					noResultsBox.classList.remove('is-visible');
				}
			}
		}

		function highlightMatch(text, term) {
			const index = text.toLowerCase().indexOf(term);
			if (index === -1) return escapeHtml(text);
			const before = text.substring(0, index);
			const match = text.substring(index, index + term.length);
			const after = text.substring(index + term.length);
			return `${escapeHtml(before)}<mark class="cp-highlight">${escapeHtml(match)}</mark>${escapeHtml(after)}`;
		}

		function escapeHtml(str) {
			return str
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}

		if (searchInput) {
			searchInput.addEventListener('input', (e) => {
				performSearch(e.target.value);
			});
		}

		if (searchClearBtn) {
			searchClearBtn.addEventListener('click', () => {
				if (searchInput) {
					searchInput.value = '';
					performSearch('');
					searchInput.focus();
				}
			});
		}

		if (resetSearchBtn) {
			resetSearchBtn.addEventListener('click', () => {
				if (searchInput) {
					searchInput.value = '';
					performSearch('');
					searchInput.focus();
				}
			});
		}

		// 5. Global Keyboard Shortcuts (/ or Ctrl+K to search, Esc to clear)
		document.addEventListener('keydown', (e) => {
			const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
			const isInputActive =
				activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select';

			// Press / or Ctrl+K / Cmd+K to focus search input
			if (
				(e.key === '/' && !isInputActive) ||
				((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k')
			) {
				e.preventDefault();
				if (searchInput) {
					searchInput.focus();
					searchInput.select();
				}
			}

			// Press Esc to clear search
			if (e.key === 'Escape' && isInputActive && document.activeElement === searchInput) {
				if (searchInput.value !== '') {
					searchInput.value = '';
					performSearch('');
				}
				searchInput.blur();
			}
		});

		// 6. Animate Quota Progress Meters
		const meterFills = document.querySelectorAll('.cpanel-meter-fill');
		setTimeout(() => {
			meterFills.forEach((fill) => {
				const pct = fill.getAttribute('data-percentage');
				if (pct !== null) {
					fill.style.width = `${Math.min(Math.max(parseFloat(pct), 0), 100)}%`;
				}
			});
		}, 100);
	}

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initCpanelDashboard);
	} else {
		initCpanelDashboard();
	}
})();
