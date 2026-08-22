document.addEventListener('DOMContentLoaded', function() {
    const section = document.getElementById('velvet-testimonials-section');
    if (!section) return;

    const track = section.querySelector('.velvet-testimonials-track');
    const cards = section.querySelectorAll('.velvet-testimonial-card');
    const prevBtn = section.querySelector('.velvet-testi-prev');
    const nextBtn = section.querySelector('.velvet-testi-next');
    const dotsContainer = section.querySelector('.velvet-testi-dots');

    if (!track || cards.length === 0) return;

    let currentIndex = 0;
    let cardsPerView = getCardsPerView();
    let maxIndex = Math.max(0, cards.length - cardsPerView);
    let autoplayTimer = null;

    function getCardsPerView() {
        const width = window.innerWidth;
        if (width <= 767) return 1;
        if (width <= 1024) return 2;
        return 3;
    }

    function createDots() {
        dotsContainer.innerHTML = '';
        const totalDots = maxIndex + 1;

        for (let i = 0; i < totalDots; i++) {
            const dot = document.createElement('button');
            dot.className = 'velvet-testi-dot' + (i === currentIndex ? ' active' : '');
            dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            dot.addEventListener('click', () => {
                goToSlide(i);
                startAutoplay();
            });
            dotsContainer.appendChild(dot);
        }
    }

    function updateTrackPosition() {
        if (!cards[0]) return;
        const gap = 24;
        const cardWidth = cards[0].getBoundingClientRect().width;
        const moveAmount = (cardWidth + gap) * currentIndex;
        track.style.transform = `translateX(-${moveAmount}px)`;

        // Update dots
        const dots = dotsContainer.querySelectorAll('.velvet-testi-dot');
        dots.forEach((dot, index) => {
            if (index === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function goToSlide(index) {
        if (index < 0) {
            currentIndex = maxIndex;
        } else if (index > maxIndex) {
            currentIndex = 0;
        } else {
            currentIndex = index;
        }
        updateTrackPosition();
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(nextSlide, 6000);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            startAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAutoplay();
        });
    }

    // Touch Swipe Support
    let startX = 0;
    let endX = 0;

    section.addEventListener('touchstart', (e) => {
        startX = e.changedTouches[0].screenX;
    }, { passive: true });

    section.addEventListener('touchend', (e) => {
        endX = e.changedTouches[0].screenX;
        if (endX < startX - 40) {
            nextSlide();
            startAutoplay();
        } else if (endX > startX + 40) {
            prevSlide();
            startAutoplay();
        }
    }, { passive: true });

    // Handle Resize
    window.addEventListener('resize', () => {
        cardsPerView = getCardsPerView();
        maxIndex = Math.max(0, cards.length - cardsPerView);
        if (currentIndex > maxIndex) {
            currentIndex = maxIndex;
        }
        createDots();
        updateTrackPosition();
    });

    // Pause on Hover
    section.addEventListener('mouseenter', stopAutoplay);
    section.addEventListener('mouseleave', startAutoplay);

    // Init
    createDots();
    updateTrackPosition();
    startAutoplay();
});
