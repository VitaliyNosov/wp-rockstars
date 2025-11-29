import React from 'react';

const BrandsSection = ({ logos }) => {
    // Don't render if no logos
    if (!logos || logos.length === 0) {
        return null;
    }

    return (
        <section className="pt-16 bg-white dark:bg-dark">
            <div className="container">
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="bg-dark dark:bg-primary dark:bg-opacity-5 rounded-md flex flex-wrap items-center justify-center py-8 px-8 sm:px-10 md:py-[40px] md:px-[50px] xl:p-[50px] 2xl:py-[60px] 2xl:px-[70px] wow fadeInUp" data-wow-delay=".1s">
                            {logos.map((item, index) => (
                                <a
                                    key={index}
                                    href={item.brandLink || '#'}
                                    target="_blank"
                                    rel="nofollow noreferrer"
                                    className="flex items-center justify-center lg:max-w-[130px] xl:max-w-[150px] 2xl:max-w-[160px] mx-3 sm:mx-4 xl:mx-6 2xl:mx-8 py-[15px] grayscale hover:grayscale-0 opacity-70 hover:opacity-100 dark:opacity-60 dark:hover:opacity-100 transition"
                                >
                                    <img
                                        src={item.brandLogo}
                                        alt={item.brandAlt || 'Brand logo'}
                                    />
                                </a>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default BrandsSection;
