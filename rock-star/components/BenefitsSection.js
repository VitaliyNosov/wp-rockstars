import React from 'react';

const BenefitsSection = ({ image, benefits }) => {
    return (
        <section className="pt-[100px] pb-[120px] bg-white dark:bg-dark">
            <div className="container">
                <div className="flex flex-wrap items-center mx-[-16px]">
                    <div className="w-full lg:w-1/2 px-4">
                        <div className="text-center lg:text-left mb-12 lg:mb-0 wow fadeInUp" data-wow-delay=".15s">
                            {image && (
                                <img src={image} alt="about image" className="max-w-full mx-auto lg:ml-0" />
                            )}
                        </div>
                    </div>

                    <div className="w-full lg:w-1/2 px-4">
                        <div className="max-w-[470px] wow fadeInUp" data-wow-delay=".2s">
                            {benefits && benefits.length > 0 && (
                                <>
                                    {benefits.map((benefit, index) => (
                                        <div key={index} className="mb-9">
                                            {benefit.benefitTitle && (
                                                <h3 className="font-bold text-black dark:text-white text-xl sm:text-2xl lg:text-xl xl:text-2xl mb-4">
                                                    {benefit.benefitTitle}
                                                </h3>
                                            )}
                                            {benefit.benefitDescription && (
                                                <p className="text-body-color text-base sm:text-lg leading-relaxed font-medium">
                                                    {benefit.benefitDescription}
                                                </p>
                                            )}
                                        </div>
                                    ))}
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default BenefitsSection;
