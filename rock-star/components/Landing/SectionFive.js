import React from 'react';

const SectionFive = ({ data }) => {
    const title = data?.sec5Title;
    const faqs = data?.sec5Faqs || [];

    if (!title && (!faqs || faqs.length === 0)) return null;

    return (
        <div className="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
            {/* Title */}
            {title && (
                <div className="max-w-2xl mx-auto mb-10 lg:mb-14 wow fadeInUp" data-wow-delay=".2s">
                    <h2 className="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
                        {title}
                    </h2>
                </div>
            )}
            {/* End Title */}

            <div className="max-w-2xl mx-auto divide-y divide-gray-200 dark:divide-neutral-700 wow fadeInUp" data-wow-delay=".4s">
                {faqs.map((faq, index) => (
                    <div key={index} className="py-8 first:pt-0 last:pb-0">
                        <div className="flex gap-x-5">
                            <span className="shrink-0 mt-1 size-6 text-gray-500 dark:text-neutral-500 flex items-center justify-center">
                                {/* Render SVG Icon from string */}
                                <span dangerouslySetInnerHTML={{ __html: faq.icon }} />
                            </span>

                            <div className="grow">
                                <h3 className="md:text-lg font-semibold dark:text-white dark:text-neutral-200">
                                    {faq.question}
                                </h3>
                                <p className="mt-1 text-gray-500 dark:text-neutral-500">
                                    {faq.answer}
                                </p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default SectionFive;
