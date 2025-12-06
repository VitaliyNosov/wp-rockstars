// Slider functionality from custom.js
import { useEffect, useRef } from 'react';

export function useSlider(sliderId = 'sliderTrack') {
    const trackRef = useRef(null);
    const indexRef = useRef(0);
    const intervalRef = useRef(null);

    useEffect(() => {
        const track = document.getElementById(sliderId);
        if (!track) return;

        trackRef.current = track;
        // Ensure we only count ORIGINAL slides, not clones from previous renders
        // Array.from(track.children) might include old clones if React didn't strip them (though it usually does)
        // logic: filter out any that have class 'clone' just in case.
        const slides = Array.from(track.children).filter(child => !child.classList.contains('clone'));
        const gapPercent = 2;
        let slideWidthPercent = 38.46;
        let visibleSlidesCount = 2.6;
        let isDragging = false;
        let startPos = 0;

        function updateSizes() {
            if (window.innerWidth <= 640) {
                // Mobile: 1 slide
                slideWidthPercent = 100;
                visibleSlidesCount = 1;
                track.style.gap = '0px'; // Explicitly 0
            } else if (window.innerWidth <= 1600) {
                // Standard Desktop (1024px - 1600px): 2.6 slides, Gap 30px
                slideWidthPercent = 38.46;
                visibleSlidesCount = 2.6;
                track.style.gap = '30px';
            } else {
                // Large Desktop (> 1600px): 2.6 slides, Gap 100px (Huge buffer)
                slideWidthPercent = 38.46;
                visibleSlidesCount = 2.6;
                track.style.gap = '100px'; // 100px to definitely hide artifacts
            }
        }

        function cloneSlides() {
            const clones = track.querySelectorAll('.clone');
            clones.forEach(c => c.remove());

            if (slides.length === 0) return;

            // We need enough clones to cover the visible area + buffer for smooth infinite scroll.
            // If we have few slides (e.g. 2), we might need more than just visibleSlidesCount clones.
            // Let's clone enough to cover at least 2x the visible count to be safe.
            const clonesCount = Math.ceil(visibleSlidesCount * 2) + 2;

            for (let i = 0; i < clonesCount; i++) {
                const slideToClone = slides[i % slides.length];
                if (slideToClone) {
                    const clone = slideToClone.cloneNode(true);
                    clone.classList.add('clone');
                    track.appendChild(clone);
                }
            }
        }

        function updatePosition(animate = true) {
            if (!slides[0]) return;

            // Получаем реальную ширину слайда
            const slideWidth = slides[0].offsetWidth;

            // Вычисляем gap в пикселях. 
            // Gap = 30px для десктопа (чтобы совпадал с padding 30px и скрывал предыдущий слайд)
            let gap = 0;
            if (window.innerWidth > 1600) {
                gap = 100;
            } else if (window.innerWidth > 640) {
                gap = 30;
            }

            const slideWithGap = slideWidth + gap;
            const shift = indexRef.current * slideWithGap;

            track.style.transition = animate ? 'transform 0.6s ease' : 'none';
            track.style.transform = `translateX(-${shift}px)`;
        }

        function nextSlide() {
            // If we are at the end (mimicking the start), swap to start instantly
            // This is the "Teleport" check.
            // visual check: indexRef.current === slides.length is the start of clones (which looks like start of original)
            if (indexRef.current >= slides.length) {
                indexRef.current = 0;
                updatePosition(false); // No animation snap
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        indexRef.current++;
                        updatePosition(true);
                    });
                });
            } else {
                indexRef.current++;
                updatePosition(true);
            }
        }

        function prevSlide() {
            if (indexRef.current <= 0) {
                // We are at start. Teleport to end (start of clones)
                indexRef.current = slides.length;
                updatePosition(false); // No animation snap
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        indexRef.current--;
                        updatePosition(true);
                    });
                });
            } else {
                indexRef.current--;
                updatePosition(true);
            }
        }

        function resetInterval() {
            clearInterval(intervalRef.current);
            // intervalRef.current = setInterval(nextSlide, 6000);
        }

        // Initialize
        updateSizes();
        cloneSlides();
        updatePosition();

        // Event listeners handlers
        const handleNextClick = () => {
            nextSlide();
            resetInterval();
        };

        const handlePrevClick = () => {
            prevSlide();
            resetInterval();
        };

        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const sliderContainer = document.getElementById('sliderContainer');

        if (nextBtn) {
            nextBtn.addEventListener('click', handleNextClick);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', handlePrevClick);
        }

        // Touch events
        function touchStart(event) {
            startPos = event.touches[0].clientX;
            isDragging = true;
            track.style.transition = 'none';
            resetInterval();
        }

        function touchMove(event) {
            if (!isDragging) return;
            const currentPos = event.touches[0].clientX;
            const diff = startPos - currentPos;

            if (!slides[0]) return;
            const slideWidth = slides[0].offsetWidth;
            let gap = 0;
            if (window.innerWidth > 1600) {
                gap = 100;
            } else if (window.innerWidth > 640) {
                gap = 30;
            }
            const slideWithGap = slideWidth + gap;

            let movePx = indexRef.current * slideWithGap + diff;
            track.style.transform = `translateX(-${movePx}px)`;
        }

        function touchEnd(event) {
            if (!isDragging) return;
            isDragging = false;
            const endPos = event.changedTouches[0].clientX;
            const diff = startPos - endPos;
            const threshold = 50;

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            } else {
                updatePosition();
            }
        }

        if (sliderContainer) {
            sliderContainer.addEventListener('touchstart', touchStart);
            sliderContainer.addEventListener('touchmove', touchMove);
            sliderContainer.addEventListener('touchend', touchEnd);
            sliderContainer.addEventListener('touchcancel', touchEnd);
        }

        // Auto-play
        // intervalRef.current = setInterval(nextSlide, 6000);

        // Resize handler
        const handleResize = () => {
            updateSizes();
            cloneSlides();
            updatePosition(false);
        };
        window.addEventListener('resize', handleResize);

        // Cleanup
        return () => {
            clearInterval(intervalRef.current);
            window.removeEventListener('resize', handleResize);
            if (sliderContainer) {
                sliderContainer.removeEventListener('touchstart', touchStart);
                sliderContainer.removeEventListener('touchmove', touchMove);
                sliderContainer.removeEventListener('touchend', touchEnd);
                sliderContainer.removeEventListener('touchcancel', touchEnd);
            }
            if (nextBtn) {
                nextBtn.removeEventListener('click', handleNextClick);
            }
            if (prevBtn) {
                prevBtn.removeEventListener('click', handlePrevClick);
            }
        };
    }, [sliderId]);
}
