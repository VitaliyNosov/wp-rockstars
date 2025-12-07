import Link from 'next/link';
import Image from 'next/image';

export default function PostCard({ post }) {
    const { title, excerpt, slug, date, featuredImage, categories, author } = post;
    const category = categories?.nodes?.[0]?.name;
    const categorySlug = categories?.nodes?.[0]?.slug;
    const authorName = author?.node?.name;
    const authorAvatar = author?.node?.avatar?.url;
    const authorDescription = author?.node?.description;

    // Format date
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });

    return (
        <div className="w-full md:w-2/3 lg:w-1/2 xl:w-1/3 px-4">
            <div className="relative bg-white dark:bg-dark shadow-one rounded-md overflow-hidden mb-10 wow fadeInUp" data-wow-delay=".1s">
                <div className="relative w-full overflow-hidden" style={{ height: '220px' }}>
                    {category && categorySlug && (
                        <Link
                            href={`/category/${categorySlug}`}
                            className="absolute top-6 right-6 bg-primary rounded-full inline-flex items-center justify-center py-2 px-4 font-semibold text-sm text-white z-10 hover:bg-opacity-80 transition"
                        >
                            {category}
                        </Link>
                    )}
                    <Link href={`/posts/${slug}`} className="block w-full h-full relative">
                        {featuredImage?.node?.sourceUrl ? (
                            <Image
                                src={featuredImage.node.sourceUrl}
                                alt={title}
                                fill
                                sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                className="object-cover transition-transform duration-300 hover:scale-110"
                            />
                        ) : (
                            <div className="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span className="text-gray-400">No Image</span>
                            </div>
                        )}
                    </Link>
                </div>
                <div className="p-6 sm:p-8 md:py-8 md:px-6 lg:p-8 xl:py-8 xl:px-5 2xl:p-8">
                    <h3>
                        <Link href={`/posts/${slug}`} className="font-bold text-black dark:text-white text-xl sm:text-2xl block mb-4 hover:text-primary dark:hover:text-primary line-clamp-2 min-h-[64px]">
                            {title}
                        </Link>
                    </h3>
                    <div
                        className="text-base text-body-color font-medium pb-6 mb-6 border-b border-body-color border-opacity-10 dark:border-white dark:border-opacity-10 line-clamp-3 min-h-[72px]"
                        dangerouslySetInnerHTML={{ __html: excerpt }}
                    />
                    <div className="flex items-center">
                        <div className="flex items-center pr-5 mr-5 xl:pr-3 2xl:pr-5 xl:mr-3 2xl:mr-5 border-r border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                            {authorAvatar && (
                                <div className="max-w-[40px] w-full h-[40px] rounded-full overflow-hidden mr-4 relative">
                                    <Image
                                        src={authorAvatar}
                                        alt={authorName || 'Author'}
                                        width={40}
                                        height={40}
                                        className="object-cover"
                                    />
                                </div>
                            )}
                            <div className="w-full">
                                <h4 className="text-sm font-medium text-dark dark:text-white mb-1">
                                    By
                                    <span className="text-dark dark:text-white hover:text-primary dark:hover:text-primary ml-1">
                                        {authorName}
                                    </span>
                                </h4>
                                <p className="text-xs text-body-color line-clamp-1">{authorDescription}</p>
                            </div>
                        </div>
                        <div className="inline-block">
                            <h4 className="text-sm font-medium text-dark dark:text-white mb-1">Date</h4>
                            <p className="text-xs text-body-color">{formattedDate}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
