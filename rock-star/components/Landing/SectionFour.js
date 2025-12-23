import React from 'react';

const SectionFour = ({ data }) => {
    const header = data?.sec4Header;
    const cards = data?.sec4Cards || [];

    if (!header && (!cards || cards.length === 0)) return null;

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

            {/* Icon Blocks */}
            <div className="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
                <div className="grid sm:grid-cols-2 lg:grid-cols-3 items-center gap-6 md:gap-10">
                    {cards.map((card, index) => (
                        <div
                            key={index}
                            className="size-full bg-white shadow-lg rounded-lg p-5 dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 wow fadeInUp"
                            data-wow-delay="0.1s"
                        >
                            <div className="flex items-center gap-x-4 mb-3">
                                <div className="inline-flex justify-center items-center size-[62px] rounded-full border-4 border-blue-50 bg-blue-100 dark:border-blue-900 dark:bg-blue-800">
                                    <span className="shrink-0 size-6 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                        {/* Render SVG Icon from string */}
                                        <span dangerouslySetInnerHTML={{ __html: card.icon }} />
                                    </span>
                                </div>
                                <div className="shrink-0">
                                    <h3 className="block text-lg font-semibold text-gray-800 dark:text-white">
                                        {card.title}
                                    </h3>
                                </div>
                            </div>
                            <p className="text-gray-600 dark:text-neutral-400">
                                {card.desc}
                            </p>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
};

export default SectionFour;
