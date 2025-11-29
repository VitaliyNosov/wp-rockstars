import React from 'react';

const AboutSection = ({ title, subtitle, features, image }) => {
    return (
        <section id="about" className="pt-[120px] bg-white dark:bg-dark">
            <div className="container">
                <div className="pb-[100px] border-b border-body-color/[.15] dark:border-white/[.15]">
                    <div className="flex flex-wrap items-center mx-[-16px]">
                        <div className="w-full lg:w-1/2 px-4">
                            <div className="mb-12 lg:mb-0 max-w-[570px] wow fadeInUp" data-wow-delay=".15s">
                                {title && (
                                    <h2 className="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] lg:text-4xl xl:text-[45px] leading-tight mb-6">
                                        {title}
                                    </h2>
                                )}

                                {subtitle && (
                                    <p className="font-medium text-body-color text-base sm:text-lg leading-relaxed mb-11">
                                        {subtitle}
                                    </p>
                                )}

                                {features && features.length > 0 && (
                                    <div className="flex flex-wrap mx-[-12px]">
                                        {features.map((feature, index) => (
                                            <div key={index} className="w-full sm:w-1/2 lg:w-full xl:w-1/2 px-3">
                                                <p className="flex items-center text-body-color text-lg font-medium mb-5">
                                                    <span className="w-[30px] h-[30px] flex items-center justify-center rounded-md bg-primary bg-opacity-10 text-primary mr-4">
                                                        <svg width="16" height="13" viewBox="0 0 16 13" className="fill-current">
                                                            <path d="M5.8535 12.6631C5.65824 12.8584 5.34166 12.8584 5.1464 12.6631L0.678505 8.1952C0.483242 7.99994 0.483242 7.68336 0.678505 7.4881L2.32921 5.83739C2.52467 5.64193 2.84166 5.64216 3.03684 5.83791L5.14622 7.95354C5.34147 8.14936 5.65859 8.14952 5.85403 7.95388L13.3797 0.420561C13.575 0.22513 13.8917 0.225051 14.087 0.420383L15.7381 2.07143C15.9333 2.26669 15.9333 2.58327 15.7381 2.77854L5.8535 12.6631Z" />
                                                        </svg>
                                                    </span>
                                                    {feature.featureText}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="w-full lg:w-1/2 px-4">
                            <div className="text-center lg:text-right wow fadeInUp" data-wow-delay=".2s">
                                {image && (
                                    <img src={image} alt="about-image" className="max-w-full mx-auto lg:mr-0" />
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default AboutSection;
