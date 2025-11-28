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
        const slides = Array.from(track.children);
        const gapPercent = 2;
        let slideWidthPercent = 38.46;
        let visibleSlidesCount = 2.6;
        let isDragging = false;
        let startPos = 0;

        function updateSizes() {
            if (window.innerWidth <= 640) {
                slideWidthPercent = 100;
                visibleSlidesCount = 1;
                track.style.gap = '0';
            } else {
                slideWidthPercent = 38.46;
                visibleSlidesCount = 2.6;
                track.style.gap = '2%';
            }
        }

        function cloneSlides() {
            const clones = track.querySelectorAll('.clone');
            clones.forEach(c => c.remove());

            for (let i = 0; i < Math.ceil(visibleSlidesCount); i++) {
                const clone = slides[i].cloneNode(true);
                clone.classList.add('clone');
                track.appendChild(clone);
            }
        }

        function updatePosition(animate = true) {
            const slideWithGap = slideWidthPercent + (window.innerWidth <= 640 ? 0 : gapPercent);
            const shift = indexRef.current * slideWithGap;
            track.style.transition = animate ? 'transform 0.6s ease' : 'none';
            track.style.transform = `translateX(${-shift}%)`;
        }

        function nextSlide() {
            indexRef.current++;
            if (indexRef.current > slides.length) {
                updatePosition(false);
                indexRef.current = 0;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        updatePosition(true);
                        indexRef.current++;
                    });
                });
            } else {
                updatePosition();
            }
        }

        function prevSlide() {
            indexRef.current--;
            if (indexRef.current < 0) {
                updatePosition(false);
                indexRef.current = slides.length;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        updatePosition(true);
                        indexRef.current--;
                    });
                });
            } else {
                updatePosition();
            }
        }

        function resetInterval() {
            clearInterval(intervalRef.current);
            intervalRef.current = setInterval(nextSlide, 6000);
        }

        // Initialize
        updateSizes();
        cloneSlides();
        updatePosition();

        // Event listeners
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const sliderContainer = document.getElementById('sliderContainer');

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetInterval();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetInterval();
            });
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
            const containerWidth = sliderContainer.offsetWidth;
            let diffPercent = (diff / containerWidth) * 100;
            let slideWithGap = slideWidthPercent + (window.innerWidth <= 640 ? 0 : gapPercent);
            let movePercent = indexRef.current * slideWithGap + diffPercent;

            if (movePercent < 0) movePercent = 0;
            if (movePercent > slides.length * slideWithGap) movePercent = slides.length * slideWithGap;

            track.style.transform = `translateX(${-movePercent}%)`;
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
        intervalRef.current = setInterval(nextSlide, 6000);

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
        };
    }, [sliderId]);
}
