import Head from 'next/head';

export default function TestPosts() {
    return (
        <>
            <Head>
                <title>Test Posts | RockStars</title>
            </Head>
            <div className="container pt-[150px] pb-[120px]">
                <h1 className="text-3xl font-bold text-black dark:text-white mb-4">Test Posts Page</h1>
                <p className="text-body-color">This is a test page to verify routing works.</p>
            </div>
        </>
    );
}
