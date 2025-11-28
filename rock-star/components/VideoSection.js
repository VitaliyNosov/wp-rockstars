import React, { useEffect } from 'react';

const VideoSection = ({ title, description, videoUrl, previewImage, backgroundShape }) => {

    useEffect(() => {
        // Re-initialize GLightbox if needed, though _app.js handles global init.
        // Sometimes dynamic content needs re-init.
        if (typeof window !== 'undefined' && window.GLightbox) {
            window.GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                autoplayVideos: true
            });
        }
    }, []);

    return (
        <section className="relative z-10 py-[120px] bg-white dark:bg-dark">
            <div className="container">
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="mx-auto max-w-[570px] text-center mb-20 wow fadeInUp" data-wow-delay=".1s">
                            {title && (
                                <h2 className="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                                    {title}
                                </h2>
                            )}
                            {description && (
                                <p className="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed">
                                    {description}
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="mx-auto max-w-[770px] rounded-md overflow-hidden wow fadeInUp" data-wow-delay=".15s">
                            <div className="relative items-center justify-center">
                                {videoUrl && previewImage ? (
                                    <>
                                        <img src={previewImage} alt="video preview" className="w-full h-full object-cover object-center" />
                                        <div className="absolute w-full h-full top-0 right-0 flex items-center justify-center">
                                            <a
                                                href={videoUrl}
                                                className="glightbox w-[70px] h-[70px] rounded-full flex items-center justify-center bg-white bg-opacity-75 text-primary hover:bg-opacity-100 transition"
                                                data-type="video"
                                                data-source="youtube"
                                                aria-label="Play video"
                                            >
                                                <svg width="16" height="18" viewBox="0 0 16 18" className="fill-current" aria-hidden="true" focusable="false">
                                                    <path d="M15.5 8.13397C16.1667 8.51888 16.1667 9.48112 15.5 9.86602L2 17.6603C1.33333 18.0452 0.499999 17.564 0.499999 16.7942L0.5 1.20577C0.5 0.43597 1.33333 -0.0451549 2 0.339745L15.5 8.13397Z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </>
                                ) : videoUrl ? (
                                    <a
                                        href={videoUrl}
                                        className="glightbox inline-block px-4 py-2 bg-primary text-white rounded"
                                        data-type="video"
                                        data-source="youtube"
                                        aria-label="Play video"
                                    >
                                        Смотреть видео
                                    </a>
                                ) : (
                                    <p>Видео не задано.</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {backgroundShape && (
                <div className="absolute bottom-0 left-0 right-0 z-[-1]">
                    <img src={backgroundShape} alt="background shape" className="w-full" />
                </div>
            )}
        </section>
    );
};

export default VideoSection;
