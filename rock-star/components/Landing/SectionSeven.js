import React from 'react';
import Link from 'next/link';

const SectionSeven = ({ data }) => {
    const title = data?.sec7Title;
    const desc = data?.sec7Desc;
    const readMoreText = data?.sec7ReadMoreText;
    const readMoreUrl = data?.sec7ReadMoreUrl;
    // Assuming posts are passed as sec7Posts. If not, we might need to adjust.
    const posts = data?.sec7Posts || [];

    if (!title && (!posts || posts.length === 0)) return null;

    return (
        <div className="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
            {/* Title */}
            <div className="max-w-2xl mx-auto text-center mb-10 lg:mb-14 wow fadeInUp" data-wow-delay=".2s">
                {title && (
                    <h2 className="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
                        {title}
                    </h2>
                )}
                {desc && (
                    <p className="mt-1 text-gray-600 dark:text-neutral-400">
                        {desc}
                    </p>
                )}
            </div>
            {/* End Title */}

            {/* Grid */}
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {posts.map((post, index) => {
                    // Animation delay logic from PHP: 0.2 + index * 0.1
                    const delay = 0.2 + (index * 0.1);

                    return (
                        <Link
                            key={index}
                            href={post.link || '#'}
                            className="group flex flex-col h-full border border-gray-200 hover:border-transparent hover:shadow-lg focus:outline-hidden focus:border-transparent focus:shadow-lg transition duration-300 rounded-xl p-5 dark:border-neutral-700 wow fadeInUp"
                            data-wow-delay={`${delay}s`}
                        >
                            <div className="aspect-w-16 aspect-h-11">
                                <img
                                    className="w-full object-cover rounded-xl"
                                    src={post.thumbnail || 'https://via.placeholder.com/560x315'}
                                    alt={post.title || 'Blog Image'}
                                />
                            </div>
                            <div className="my-6">
                                <h3 className="text-xl font-semibold dark:text-white dark:text-neutral-300 dark:group-hover:text-white">
                                    {post.title}
                                </h3>
                                <p className="mt-5 text-gray-600 dark:text-neutral-400">
                                    {post.excerpt}
                                </p>
                            </div>
                            <div className="mt-auto flex items-center gap-x-3">
                                <img
                                    className="size-8 rounded-full"
                                    src={post.authorAvatar || 'https://via.placeholder.com/32'}
                                    alt="Avatar"
                                />
                                <div>
                                    <h5 className="text-sm dark:text-white dark:text-neutral-200">
                                        By {post.authorName || 'Unknown'}
                                    </h5>
                                </div>
                            </div>
                        </Link>
                    );
                })}
            </div>
            {/* End Grid */}

            {/* Read More Button */}
            {readMoreText && readMoreUrl && (
                <div className="mt-12 text-center">
                    <Link
                        href={readMoreUrl}
                        className="py-3 px-4 inline-flex items-center gap-x-1 text-sm font-medium rounded-full border border-gray-200 text-blue-600 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-blue-500 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                    >
                        {readMoreText}
                        <svg className="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 18 6-6-6-6" /></svg>
                    </Link>
                </div>
            )}
        </div>
    );
};

export default SectionSeven;
