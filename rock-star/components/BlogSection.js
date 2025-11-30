export default function BlogSection({ posts }) {
    if (!posts || posts.length === 0) {
        return null;
    }

    return (
        <section id="blog" className="bg-primary bg-opacity-5 pt-[120px] pb-20">
            <div className="container">
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="mx-auto max-w-[570px] text-center mb-[100px] wow fadeInUp" data-wow-delay=".1s">
                            <h2 className="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                                Our latest blogs
                            </h2>
                            <p className="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed">
                                Check out our latest articles and useful tips
                            </p>
                        </div>
                    </div>
                </div>

                <div className="flex flex-wrap mx-[-16px] justify-center">
                    {posts.map((post, index) => {
                        const delay = 0.1 + index * 0.05;

                        return (
                            <div key={post.id} className="w-full md:w-2/3 lg:w-1/2 xl:w-1/3 px-4">
                                <div
                                    className="relative bg-white dark:bg-dark shadow-one rounded-md overflow-hidden mb-10 wow fadeInUp"
                                    data-wow-delay={`${delay}s`}
                                >
                                    <a href={post.permalink} className="w-full block relative">
                                        <span className="absolute top-6 right-6 bg-primary rounded-full inline-flex items-center justify-center py-2 px-4 font-semibold text-sm text-white">
                                            {post.categoryName}
                                        </span>
                                        {post.featuredImage ? (
                                            <img
                                                src={post.featuredImage}
                                                alt={post.title}
                                                className="w-full"
                                            />
                                        ) : (
                                            <div className="w-full h-[200px] bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <span className="text-gray-400">No Image</span>
                                            </div>
                                        )}
                                    </a>

                                    <div className="p-6 sm:p-8 md:py-8 md:px-6 lg:p-8 xl:py-8 xl:px-5 2xl:p-8">
                                        <h3>
                                            <a
                                                href={post.permalink}
                                                className="font-bold text-black dark:text-white text-xl sm:text-2xl block mb-4 hover:text-primary dark:hover:text-primary"
                                            >
                                                {post.title}
                                            </a>
                                        </h3>

                                        <p className="text-base text-body-color font-medium pb-6 mb-6 border-b border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                                            {post.excerpt}
                                        </p>

                                        <div className="flex items-center">
                                            <div className="flex items-center pr-5 mr-5 xl:pr-3 2xl:pr-5 xl:mr-3 2xl:mr-5 border-r border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                                                <div className="max-w-[40px] w-full h-[40px] rounded-full overflow-hidden mr-4">
                                                    {post.authorAvatar ? (
                                                        <img
                                                            src={post.authorAvatar}
                                                            alt={post.authorName}
                                                            className="w-full"
                                                        />
                                                    ) : (
                                                        <div className="w-full h-full bg-gray-300 dark:bg-gray-600"></div>
                                                    )}
                                                </div>
                                                <div className="w-full">
                                                    <h4 className="text-sm font-medium text-dark dark:text-white mb-1">
                                                        Автор:{' '}
                                                        <a
                                                            href={post.authorUrl}
                                                            className="text-dark dark:text-white hover:text-primary dark:hover:text-primary"
                                                        >
                                                            {post.authorName}
                                                        </a>
                                                    </h4>
                                                    <p className="text-xs text-body-color">
                                                        {post.authorDescription}
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="inline-block">
                                                <h4 className="text-sm font-medium text-dark dark:text-white mb-1">
                                                    Дата
                                                </h4>
                                                <p className="text-xs text-body-color">
                                                    {post.date}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
