import React from 'react';
import Link from 'next/link';

const SectionTwo = ({ data }) => {
    // Determine content blocks and tags from data
    const contentBlocks = data?.sec2Content || [];
    const tags = data?.sec2Tags || [];
    const title = data?.sec2Title;

    return (
        <>
            {/* Header Section */}
            {title && (
                <div className="header-landing-section section-margin-top wow fadeInUp" data-wow-delay=".2s">
                    <h2 className="text-center text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
                        {title}
                    </h2>
                </div>
            )}

            {/* Content Section */}
            <div className="max-w-[75rem] px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto wow fadeInUp" data-wow-delay=".4s">
                <div className="max-w-1xl mx-auto">
                    {/* Content Blocks */}
                    <div className="space-y-5 md:space-y-8">
                        {contentBlocks.map((block, index) => {
                            switch (block.type) {
                                case 'paragraph':
                                    return (
                                        <p key={index} className="mt-3 text-lg dark:text-white dark:text-neutral-400">
                                            {block.content}
                                        </p>
                                    );
                                case 'heading':
                                    return (
                                        <div key={index} className="space-y-3">
                                            <h3 className="text-2xl font-semibold dark:text-white">
                                                {block.text}
                                            </h3>
                                        </div>
                                    );
                                case 'quote':
                                    return (
                                        <blockquote key={index} className="text-center p-4 sm:px-7">
                                            <p className="text-xl font-medium text-gray-800 md:text-2xl md:leading-normal xl:text-2xl xl:leading-normal dark:text-neutral-200">
                                                {block.text}
                                            </p>
                                            {block.author && (
                                                <p className="mt-3 text-lg dark:text-white dark:text-neutral-400">
                                                    {block.author}
                                                </p>
                                            )}
                                        </blockquote>
                                    );
                                case 'image':
                                    return block.image ? (
                                        <figure key={index}>
                                            <img
                                                className="w-full h-auto max-h-[600px] object-cover rounded-xl"
                                                src={block.image}
                                                alt={block.caption || 'Blog Image'}
                                            />
                                            {block.caption && (
                                                <figcaption className="mt-3 text-sm text-center text-gray-500 dark:text-neutral-500">
                                                    {block.caption}
                                                </figcaption>
                                            )}
                                        </figure>
                                    ) : null;
                                case 'list':
                                    return Array.isArray(block.items) && block.items.length > 0 ? (
                                        <ul key={index} className="list-disc list-outside space-y-5 ps-5 text-lg dark:text-white dark:text-neutral-400">
                                            {block.items.map((item, i) => (
                                                <li key={i} className="ps-2">
                                                    {item.text}
                                                </li>
                                            ))}
                                        </ul>
                                    ) : null;
                                default:
                                    return null;
                            }
                        })}

                        {/* Tags */}
                        {tags && tags.length > 0 && (
                            <div>
                                {tags.map((tag, index) => (
                                    <Link
                                        key={index}
                                        href={tag.url || '#'}
                                        className="section-two-tag m-1 inline-flex items-center gap-1.5 py-2 px-3 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                                    >
                                        {tag.label}
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
};

export default SectionTwo;
