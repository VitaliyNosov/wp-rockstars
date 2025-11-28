// Scroll effects utilities

export function initScrollToTop() {
    if (typeof window === 'undefined') return;

    const handleScroll = () => {
        const backToTop = document.querySelector('.back-to-top');
        if (!backToTop) return;

        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
            backToTop.style.display = 'flex';
        } else {
            backToTop.style.display = 'none';
        }
    };

    window.addEventListener('scroll', handleScroll);

    // Smooth scroll to top
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', () => {
            scrollTo(document.documentElement);
        });
    }

    return () => window.removeEventListener('scroll', handleScroll);
}

// Smooth scroll function
export function scrollTo(element, to = 0, duration = 500) {
    const start = element.scrollTop;
    const change = to - start;
    const increment = 20;
    let currentTime = 0;

    const animateScroll = () => {
        currentTime += increment;
        const val = easeInOutQuad(currentTime, start, change, duration);
        element.scrollTop = val;

        if (currentTime < duration) {
            setTimeout(animateScroll, increment);
        }
    };

    animateScroll();
}

// Easing function
function easeInOutQuad(t, b, c, d) {
    t /= d / 2;
    if (t < 1) return c / 2 * t * t + b;
    t--;
    return -c / 2 * (t * (t - 2) - 1) + b;
}
