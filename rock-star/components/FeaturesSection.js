import React from 'react';

const FeaturesSection = ({ title, description, features }) => {
    return (
        <section id="features" className="bg-primary bg-opacity-[3%] pt-[120px] pb-[50px]">
            <div className="container">
                {(title || description) && (
                    <div className="flex flex-wrap mx-[-16px]">
                        <div className="w-full px-4">
                            <div className="mx-auto max-w-[570px] text-center mb-[100px] wow fadeInUp" data-wow-delay=".1s">
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
                )}

                {features && features.length > 0 && (
                    <div className="flex flex-wrap mx-[-16px]">
                        {features.map((feature, i) => (
                            <div key={i} className="w-full md:w-1/2 lg:w-1/3 px-4">
                                <div className="mb-[70px] wow fadeInUp" data-wow-delay={`.${10 + i * 5}s`}>
                                    <div
                                        className="w-[70px] h-[70px] flex items-center justify-center rounded-md bg-primary bg-opacity-10 mb-10 text-primary"
                                        dangerouslySetInnerHTML={{ __html: feature.featureIconSvg }}
                                    />
                                    <h3 className="font-bold text-black dark:text-white text-xl sm:text-2xl lg:text-xl xl:text-2xl mb-5">
                                        {feature.featureTitle}
                                    </h3>
                                    <p className="text-body-color text-base leading-relaxed font-medium pr-[10px]">
                                        {feature.featureDescription}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
};

export default FeaturesSection;
