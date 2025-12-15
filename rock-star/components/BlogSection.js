import Link from 'next/link';
import PostCard from './PostCard';

export default function BlogSection({ posts }) {
    if (!posts || posts.length === 0) {
        return null;
    }

    return (
        <section id="blog" className="bg-primary bg-opacity-5 pt-[120px] pb-20">
            <div className="container">
                <div className="flex flex-wrap mx-[-16px]">
                    <div className="w-full px-4">
                        <div className="mx-auto max-w-[570px] text-center mb-[100px]">
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
                    {posts.map((post) => (
                        <PostCard key={post.id} post={post} />
                    ))}
                </div>
            </div>
        </section>
    );
}
