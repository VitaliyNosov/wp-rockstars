import React, { useState } from 'react';

const SectionThree = ({ data }) => {
    const [activeTab, setActiveTab] = useState(0);

    const header = data?.sec3Header;
    const title = data?.sec3Title;
    const tabs = data?.sec3Tabs || [];

    if (!tabs || tabs.length === 0) return null;

    return (
        <>
            {/* Header */}
            {header && (
                <div className="header-landing-section wow fadeInUp" data-wow-delay=".2s">
                    <h2 className="text-center text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
                        {header}
                    </h2>
                </div>
            )}

            {/* Features */}
            <div className="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
                <div className="relative p-6 md:p-16">
                    {/* Grid */}
                    <div className="relative z-10 lg:grid lg:grid-cols-12 lg:gap-16 lg:items-center">

                        {/* Right Content (Tabs Navigation) */}
                        <div className="mb-10 lg:mb-0 lg:col-span-6 lg:col-start-8 lg:order-2">
                            {title && (
                                <h2 className="text-2xl dark:text-white font-bold sm:text-3xl dark:text-neutral-200">
                                    {title}
                                </h2>
                            )}

                            {/* Tab Navs */}
                            <nav className="grid gap-4 mt-5 md:mt-10 wow fadeInUp" data-wow-delay=".4s" aria-label="Tabs" role="tablist">
                                {tabs.map((tab, index) => {
                                    const isActive = index === activeTab;
                                    return (
                                        <button
                                            key={index}
                                            type="button"
                                            onClick={() => setActiveTab(index)}
                                            className={`text-start hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 p-4 md:p-5 rounded-xl dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 border-[1px] border-[#E5E7EB] ${isActive
                                                    ? 'bg-white shadow-md border-blue-600 dark:bg-neutral-700 dark:border-blue-500'
                                                    : 'dark:border-neutral-700'
                                                }`}
                                            role="tab"
                                            aria-selected={isActive}
                                        >
                                            <span className="flex gap-x-6">
                                                <span className={`shrink-0 mt-2 size-6 md:size-7 flex items-center justify-center ${isActive
                                                        ? 'text-blue-600 dark:text-blue-500'
                                                        : 'dark:text-neutral-200'
                                                    }`}>
                                                    {/* Render SVG Icon from string */}
                                                    <span dangerouslySetInnerHTML={{ __html: tab.icon }} />
                                                </span>
                                                <span className="grow">
                                                    <span className={`block text-lg font-semibold ${isActive
                                                            ? 'text-blue-600 dark:text-blue-500'
                                                            : 'dark:text-neutral-200'
                                                        }`}>
                                                        {tab.title}
                                                    </span>
                                                    <span className={`block mt-1 ${isActive
                                                            ? 'text-blue-600 dark:text-blue-500'
                                                            : 'dark:text-neutral-200'
                                                        }`}>
                                                        {tab.desc}
                                                    </span>
                                                </span>
                                            </span>
                                        </button>
                                    );
                                })}
                            </nav>
                        </div>

                        {/* Left Content (Images) */}
                        <div className="lg:col-span-6">
                            <div className="relative wow fadeInUp" data-wow-delay=".6s">
                                {/* Tab Content */}
                                <div>
                                    {tabs.map((tab, index) => (
                                        <div key={index} className={index === activeTab ? '' : 'hidden'} role="tabpanel">
                                            <img
                                                className="shadow-xl shadow-gray-200 rounded-xl dark:shadow-gray-900/20"
                                                src={tab.image}
                                                alt={tab.title}
                                            />
                                        </div>
                                    ))}
                                </div>

                                {/* SVG Element */}
                                <div className="hidden absolute top-0 end-0 translate-x-20 md:block lg:translate-x-20">
                                    <svg className="w-16 h-auto text-orange-500" width="121" height="135" viewBox="0 0 121 135" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 16.4754C11.7688 27.4499 21.2452 57.3224 5 89.0164" stroke="currentColor" strokeWidth="10" strokeLinecap="round" />
                                        <path d="M33.6761 112.104C44.6984 98.1239 74.2618 57.6776 83.4821 5" stroke="currentColor" strokeWidth="10" strokeLinecap="round" />
                                        <path d="M50.5525 130C68.2064 127.495 110.731 117.541 116 78.0874" stroke="currentColor" strokeWidth="10" strokeLinecap="round" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Background Color */}
                    <div className="absolute inset-0 grid grid-cols-12 size-full">
                        <div className="col-span-full lg:col-span-7 lg:col-start-6 bg-gray-100 w-full h-5/6 rounded-xl sm:h-3/4 lg:h-full dark:bg-neutral-800"></div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default SectionThree;
