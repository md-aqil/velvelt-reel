document.addEventListener('DOMContentLoaded', function() {
    const section = document.getElementById('velvet-talent-section');
    if (!section) return;

    const filterBtns = section.querySelectorAll('.velvet-filter-btn');
    const talentCards = section.querySelectorAll('.velvet-talent-card');

    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            talentCards.forEach(function(card) {
                const cardRole = card.getAttribute('data-role');

                if (filterValue === 'all' || cardRole === filterValue) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 50);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
});
