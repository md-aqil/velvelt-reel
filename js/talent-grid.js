document.addEventListener('DOMContentLoaded', function() {
    const section = document.getElementById('velvet-talent-section');
    if (!section) return;

    const searchInput = section.querySelector('#velvet-talent-search-input');
    const countrySelect = section.querySelector('#velvet-talent-country-select');
    const searchBtn = section.querySelector('#velvet-talent-search-btn');
    const filterBtns = section.querySelectorAll('.velvet-filter-btn');
    const talentCards = section.querySelectorAll('.velvet-talent-card');
    const noResults = section.querySelector('#velvet-no-results');

    let activeCategory = 'all';
    let debounceTimer = null;

    function applyFilters() {
        const searchText = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const selectedCountry = countrySelect ? countrySelect.value.toLowerCase() : 'all';
        let visibleCount = 0;

        talentCards.forEach(function(card) {
            const cardRole = card.getAttribute('data-role');
            const cardCountry = card.getAttribute('data-country') || '';
            const cardSearchData = card.getAttribute('data-search') || '';

            // Category match check
            const matchesCategory = (activeCategory === 'all' || cardRole === activeCategory);

            // Country match check
            const matchesCountry = (selectedCountry === 'all' || cardCountry === selectedCountry);

            // Search text match check (checks title, role, country)
            const matchesSearch = (!searchText || cardSearchData.includes(searchText));

            if (matchesCategory && matchesCountry && matchesSearch) {
                visibleCount++;
                card.style.display = 'block';
                requestAnimationFrame(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                });
            } else {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.96)';
                setTimeout(() => {
                    if (card.style.opacity === '0') {
                        card.style.display = 'none';
                    }
                }, 200);
            }
        });

        if (noResults) {
            noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Category button click listener
    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.getAttribute('data-filter');
            applyFilters();
        });
    });

    // Real-time search input with debounce
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applyFilters, 150);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });
    }

    // Country select dropdown change
    if (countrySelect) {
        countrySelect.addEventListener('change', applyFilters);
    }

    // Search button click
    if (searchBtn) {
        searchBtn.addEventListener('click', applyFilters);
    }
});
