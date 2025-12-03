import React, { useState } from 'react';

const PricingSection = ({ title, description, monthlyLabel, yearlyLabel, pricingPlans, enabled }) => {
    const [isYearly, setIsYearly] = useState(false);

    if (!enabled) {
        return null;
    }

    return (
        <section id="pricing" className="relative z-20 pt-[120px] pb-20 bg-white dark:bg-dark mt-[-40px]">
            <div className="container">
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="mx-auto max-w-[655px] text-center mb-[100px] wow fadeInUp" data-wow-delay=".1s">
                            {title && (
                                <h2 className="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                                    {title}
                                </h2>
                            )}
                            {description && (
                                <p className="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed max-w-[570px] mx-auto">
                                    {description}
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Monthly/Yearly Toggle */}
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="flex justify-center mb-16 wow fadeInUp" data-wow-delay=".1s">
                            <span className="text-dark dark:text-white text-base font-semibold mr-4 cursor-pointer">
                                {monthlyLabel || 'Monthly'}
                            </span>
                            <label htmlFor="togglePlan" className="flex items-center cursor-pointer">
                                <div className="relative">
                                    <input
                                        id="togglePlan"
                                        type="checkbox"
                                        className="sr-only"
                                        checked={isYearly}
                                        onChange={(e) => setIsYearly(e.target.checked)}
                                    />
                                    <div className="w-14 h-5 bg-[#1D2144] rounded-full shadow-inner"></div>
                                    <div className={`dot absolute w-7 h-7 bg-primary rounded-full shadow-switch-1 top-[-4px] transition-all flex items-center justify-center ${isYearly ? 'left-7' : 'left-0'}`}>
                                        <span className="active w-4 h-4 rounded-full bg-white"></span>
                                    </div>
                                </div>
                            </label>
                            <span className="text-dark dark:text-white text-base font-semibold ml-4 cursor-pointer">
                                {yearlyLabel || 'Yearly'}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Pricing Plans */}
                {pricingPlans && pricingPlans.length > 0 && (
                    <div className="flex flex-wrap mx-[-16px]">
                        {pricingPlans.map((plan, index) => {
                            const delay = 0.1 + index * 0.05;

                            return (
                                <div key={index} className="w-full md:w-1/2 lg:w-1/3 px-4">
                                    <div
                                        className="relative z-10 bg-white dark:bg-[#1D2144] shadow-signUp px-8 py-10 rounded-md mb-10 wow fadeInUp overflow-visible"
                                        data-wow-delay={`${delay}s`}
                                    >
                                        {plan.isPopular && (
                                            <div className="custom-bage-position absolute top-0 left-[20px] mt-[-16px]">
                                                <span className="bg-primary text-white px-4 py-2 rounded-full text-sm font-semibold block">
                                                    Popular
                                                </span>
                                            </div>
                                        )}

                                        <div className="flex justify-between items-center">
                                            <h3 className="font-bold text-black dark:text-white text-3xl mb-2 price">
                                                $<span className="amount">{isYearly ? plan.yearlyPrice : plan.monthlyPrice}</span>
                                                <span className="text-dark dark:text-body-color time">{isYearly ? '/yr' : '/mo'}</span>
                                            </h3>
                                            <h4 className="text-white font-bold text-xl mb-2">{plan.name}</h4>
                                        </div>

                                        {plan.description && (
                                            <p className="text-base text-body-color mb-7">{plan.description}</p>
                                        )}

                                        <div className="border-b border-body-color dark:border-white border-opacity-10 dark:border-opacity-10 pb-8 mb-8">
                                            <a
                                                href={plan.buttonUrl || '#'}
                                                className="font-semibold text-base text-white bg-primary w-full flex items-center justify-center rounded-md p-3 hover:shadow-signUp hover:bg-opacity-80 transition duration-300 ease-in-out"
                                            >
                                                {plan.buttonText || 'Start Free Trial'}
                                            </a>
                                        </div>

                                        {/* Features List */}
                                        {plan.features && plan.features.length > 0 && (
                                            <div>
                                                {plan.features.map((feature, fIndex) => (
                                                    <div key={fIndex} className={`flex items-center ${fIndex < plan.features.length - 1 ? 'mb-3' : ''}`}>
                                                        <span className="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                                                            {feature.status === 'included' ? (
                                                                <svg width="8" height="6" viewBox="0 0 8 6" className="fill-current">
                                                                    <path d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                                                                </svg>
                                                            ) : (
                                                                <svg width="8" height="8" viewBox="0 0 8 8" className="fill-current stroke-current">
                                                                    <path d="M1.40102 0.95486C1.27421 0.828319 1.07219 0.828354 0.945421 0.954965C0.818519 1.08171 0.818519 1.28389 0.945421 1.41063L0.945612 1.41083L3.54915 4.00184L0.955169 6.60202C0.955106 6.60209 0.95504 6.60215 0.954978 6.60222C0.828263 6.72897 0.82833 6.93101 0.955169 7.05769C1.01288 7.11533 1.09989 7.15024 1.17815 7.15024C1.25641 7.15024 1.34342 7.11533 1.40113 7.05769L1.29513 6.95156L1.40113 7.05769L4.00493 4.45706L6.59917 7.0575L6.59936 7.05769C6.65707 7.11533 6.74408 7.15024 6.82234 7.15024C6.9006 7.15024 6.98761 7.11533 7.04532 7.05769C7.17215 6.93102 7.17222 6.729 7.04553 6.60224C7.04546 6.60217 7.04539 6.6021 7.04532 6.60202L4.46051 4.00165L7.05507 1.4009C7.05511 1.40085 7.05516 1.4008 7.05521 1.40076L7.05526 1.40071L6.94907 1.29477L1.40102 0.95486ZM1.40102 0.95486C1.40106 0.954895 1.40109 0.95493 1.40113 0.954965L1.40102 0.95486Z" strokeWidth="0.3" />
                                                                </svg>
                                                            )}
                                                        </span>
                                                        <p className="text-base font-medium text-body-color m-0">{feature.text}</p>
                                                    </div>
                                                ))}
                                            </div>
                                        )}

                                        {/* Decorative SVG */}
                                        <div className="absolute bottom-0 right-0 z-[-1]">
                                            <svg width="179" height="158" viewBox="0 0 179 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.5" d="M75.0002 63.256C115.229 82.3657 136.011 137.496 141.374 162.673C150.063 203.47 207.217 197.755 202.419 167.738C195.393 123.781 137.273 90.3579 75.0002 63.256Z" fill="url(#paint0_linear_70:153)" />
                                                <path opacity="0.3" d="M178.255 0.150879C129.388 56.5969 134.648 155.224 143.387 197.482C157.547 265.958 65.9705 295.709 53.1024 246.401C34.2588 174.197 100.939 83.7223 178.255 0.150879Z" fill="url(#paint1_linear_70:153)" />
                                                <defs>
                                                    <linearGradient id="paint0_linear_70:153" x1="69.6694" y1="29.9033" x2="196.108" y2="83.2919" gradientUnits="userSpaceOnUse">
                                                        <stop stopColor="#4A6CF7" stopOpacity="0.62" />
                                                        <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_70:153" x1="165.348" y1="-75.4466" x2="-3.75136" y2="103.645" gradientUnits="userSpaceOnUse">
                                                        <stop stopColor="#4A6CF7" stopOpacity="0.62" />
                                                        <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* Decorative SVG Bottom Left */}
            <div className="absolute left-0 bottom-0 z-[-1]">
                <svg width="239" height="601" viewBox="0 0 239 601" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="-184.451" y="600.973" width="196" height="541.607" rx="2" transform="rotate(-128.7 -184.451 600.973)" fill="url(#paint0_linear_93:235)" />
                    <rect opacity="0.3" x="-188.201" y="385.272" width="59.7544" height="541.607" rx="2" transform="rotate(-128.7 -188.201 385.272)" fill="url(#paint1_linear_93:235)" />
                    <defs>
                        <linearGradient id="paint0_linear_93:235" x1="-90.1184" y1="420.414" x2="-90.1184" y2="1131.65" gradientUnits="userSpaceOnUse">
                            <stop stopColor="#4A6CF7" />
                            <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                        </linearGradient>
                        <linearGradient id="paint1_linear_93:235" x1="-159.441" y1="204.714" x2="-159.441" y2="915.952" gradientUnits="userSpaceOnUse">
                            <stop stopColor="#4A6CF7" />
                            <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </section>
    );
};

export default PricingSection;
