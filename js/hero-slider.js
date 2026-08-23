document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('velvet-hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.velvet-slide');
    const dots = slider.querySelectorAll('.velvet-dot');
    const prevBtn = slider.querySelector('.velvet-arrow-prev');
    const nextBtn = slider.querySelector('.velvet-arrow-next');
    let currentIndex = 0;
    let autoplayTimer = null;
    const intervalTime = 7000; // 7 seconds

    // Inline Hero Title Word Rotating Animation
    const heroWords = slider.querySelectorAll('.velvet-hero-word');
    if (heroWords.length > 1) {
        let wordIndex = 0;
        setInterval(() => {
            const currentW = heroWords[wordIndex];
            wordIndex = (wordIndex + 1) % heroWords.length;
            const nextW = heroWords[wordIndex];

            currentW.classList.remove('is-active');
            currentW.classList.add('is-out');

            setTimeout(() => {
                currentW.classList.remove('is-out');
            }, 600);

            nextW.classList.add('is-active');
        }, 2200);
    }

    function goToSlide(index) {
        if (index < 0) {
            index = slides.length - 1;
        } else if (index >= slides.length) {
            index = 0;
        }

        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
                const video = slide.querySelector('video');
                if (video) {
                    video.currentTime = 0;
                    video.play().catch(function() {});
                }
            } else {
                slide.classList.remove('active');
            }
        });

        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        currentIndex = index;
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(nextSlide, intervalTime);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            prevSlide();
            startAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            nextSlide();
            startAutoplay();
        });
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', function() {
            const targetIndex = parseInt(this.getAttribute('data-slide-target'), 10);
            goToSlide(targetIndex);
            startAutoplay();
        });
    });

    // Touch Swipe Support for Mobile
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    slider.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const threshold = 50;
        if (touchEndX < touchStartX - threshold) {
            nextSlide();
            startAutoplay();
        } else if (touchEndX > touchStartX + threshold) {
            prevSlide();
            startAutoplay();
        }
    }

    // Pause autoplay on mouse enter, resume on mouse leave
    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);

    // Initialize first slide and autoplay
    goToSlide(0);
    startAutoplay();
});
