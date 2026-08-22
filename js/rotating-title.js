document.addEventListener('DOMContentLoaded', function() {
    const titleWraps = document.querySelectorAll('.velvet-rotating-title-wrap');
    if (titleWraps.length === 0) return;

    titleWraps.forEach(function(wrap) {
        const words = wrap.querySelectorAll('.velvet-rotating-word');
        if (words.length <= 1) return;

        let currentIndex = 0;
        const rotateInterval = 2500; // Rotate every 2.5s

        setInterval(function() {
            const currentWord = words[currentIndex];
            currentIndex = (currentIndex + 1) % words.length;
            const nextWord = words[currentIndex];

            // Exit animation on current word
            currentWord.classList.remove('is-active');
            currentWord.classList.add('is-out');

            // Reset out class after transition completes
            setTimeout(() => {
                currentWord.classList.remove('is-out');
            }, 600);

            // Enter animation on next word
            nextWord.classList.add('is-active');
        }, rotateInterval);
    });
});
