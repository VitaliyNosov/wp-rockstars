import React, { useState, useEffect, useRef } from 'react';
import { useSlider } from '../lib/useSlider';
import PortfolioModal from './PortfolioModal';

const PortfolioSlider = ({ slides }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [currentUrl, setCurrentUrl] = useState('');
    const containerRef = useRef(null);

    // Don't render if no slides
    if (!slides || slides.length === 0) {
        return null;
    }

    // Initialize slider with custom hook
    useSlider('sliderTrack');

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setCurrentUrl('');
    };

    // Event delegation for handling clicks on both original and cloned slides
    useEffect(() => {
        const container = containerRef.current;
        if (!container) return;

        const handleClick = (e) => {
            // Find the closest anchor element
            const anchor = e.target.closest('a.slide');
            if (anchor) {
                e.preventDefault();
                const url = anchor.getAttribute('href');
                if (url && url !== '#') {
                    setCurrentUrl(url);
                    setIsModalOpen(true);
                }
            }
        };

        container.addEventListener('click', handleClick);

        return () => {
            container.removeEventListener('click', handleClick);
        };
    }, []);

    return (
        <section className="portfolio-section">
            {/* Title */}
            <div className="portfolio-name text-center mb-12">
                <h1 className="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                    Portfolio
                </h1>
            </div>

            {/* Slider Container */}
            <div className="portfolio-slider-block"></div>

            <div className="slider-container" id="sliderContainer" ref={containerRef}>
                <div id="sliderTrack" className="slider-track">
                    {slides.map((slide, index) => (
                        <a
                            key={index}
                            href={slide.slideUrl || '#'}
                            className="slide"
                        >
                            <img
                                src={slide.slideImage}
                                alt={slide.slideAlt || `Portfolio slide ${index + 1}`}
                            />
                        </a>
                    ))}
                </div>
            </div>

            {/* Navigation Buttons */}
            <div className="slider-buttons flex justify-center gap-4">
                <button
                    id="prevBtn"
                    aria-label="Предыдущий слайд"
                    className="w-12 h-12 rounded-full border-2 border-gray-800 dark:border-white text-gray-800 dark:text-white bg-transparent hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-black transition flex items-center justify-center font-bold text-xl cursor-pointer"
                >
                    &lt;
                </button>
                <button
                    id="nextBtn"
                    aria-label="Следующий слайд"
                    className="w-12 h-12 rounded-full border-2 border-gray-800 dark:border-white text-gray-800 dark:text-white bg-transparent hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-black transition flex items-center justify-center font-bold text-xl cursor-pointer"
                >
                    &gt;
                </button>
            </div>

            <PortfolioModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                url={currentUrl}
            />
        </section>
    );
};

export default PortfolioSlider;
