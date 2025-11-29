import React from 'react';
import { useSlider } from '../lib/useSlider';

const PortfolioSlider = ({ slides }) => {
    // Don't render if no slides
    if (!slides || slides.length === 0) {
        return null;
    }

    // Initialize slider with custom hook
    useSlider('sliderTrack');

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

            <div className="slider-container" id="sliderContainer">
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
        </section>
    );
};

export default PortfolioSlider;
