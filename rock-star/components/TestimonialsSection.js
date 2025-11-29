import React from 'react';

const TestimonialsSection = ({ title, description, testimonials }) => {
    return (
        <section className="relative z-10 pt-[120px] pb-20 bg-primary bg-opacity-[3%]">
            <div className="container">
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="mx-auto max-w-[570px] text-center mb-[100px]">
                            {title && (
                                <h2 className="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                                    {title}
                                </h2>
                            )}
                            {description && (
                                <p className="text-body-color text-base md:text-lg leading-relaxed">
                                    {description}
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {testimonials && testimonials.length > 0 && (
                    <div className="flex flex-wrap mx-[-16px]">
                        {testimonials.map((item, index) => {
                            const delay = 0.1 + index * 0.05;
                            const rating = parseInt(item.rating) || 5;

                            return (
                                <div key={index} className="w-full md:w-1/2 lg:w-1/3 px-4">
                                    <div
                                        className="shadow-one bg-white dark:bg-[#1D2144] rounded-md p-8 lg:px-5 xl:px-8 mb-10 wow fadeInUp"
                                        data-wow-delay={`${delay}s`}
                                    >
                                        <div className="flex items-center mb-5">
                                            {[...Array(5)].map((_, i) => (
                                                <span key={i} className="text-yellow mr-1 block">
                                                    <svg width="18" height="16" viewBox="0 0 18 16" className="fill-current">
                                                        <path d="M9.09815 0.361679L11.1054 6.06601H17.601L12.3459 9.59149L14.3532 15.2958L9.09815 11.7703L3.84309 15.2958L5.85035 9.59149L0.595291 6.06601H7.0909L9.09815 0.361679Z" />
                                                    </svg>
                                                </span>
                                            ))}
                                        </div>

                                        <p className="text-base text-body-color dark:text-white leading-relaxed pb-8 border-b border-body-color dark:border-white border-opacity-10 mb-8">
                                            "{item.text}"
                                        </p>

                                        <div className="flex items-center">
                                            {item.photo && (
                                                <div className="rounded-full overflow-hidden max-w-[50px] w-full h-[50px] mr-4">
                                                    <img src={item.photo} alt={item.name} />
                                                </div>
                                            )}
                                            <div className="w-full">
                                                <h5 className="text-lg lg:text-base xl:text-lg text-dark dark:text-white font-semibold mb-1">
                                                    {item.name}
                                                </h5>
                                                <p className="text-sm text-body-color">
                                                    {item.position}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </section>
    );
};

export default TestimonialsSection;
