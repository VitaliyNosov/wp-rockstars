import { gql } from '@apollo/client';
import client from '../lib/apolloClient';
import PostCard from '../components/PostCard';
import Link from 'next/link';
import Head from 'next/head';

const POSTS_PER_PAGE = 9;

const GET_POSTS = gql`
  query GetPosts($first: Int, $last: Int, $after: String, $before: String) {
    posts(first: $first, last: $last, after: $after, before: $before) {
      pageInfo {
        hasNextPage
        hasPreviousPage
        startCursor
        endCursor
      }
      nodes {
        id
        title
        excerpt
        slug
        date
        featuredImage {
          node {
            sourceUrl
          }
        }
        categories {
          nodes {
            name
            slug
          }
        }
        author {
          node {
            name
            avatar {
              url
            }
            description
          }
        }
      }
    }
  }
`;

export default function BlogGrids({ posts, pageInfo, error }) {
    if (error) {
        return (
            <div className="container pt-[150px] pb-[120px] text-center">
                <h2 className="text-2xl font-bold text-red-500">Error loading posts</h2>
                <p className="text-body-color">{error}</p>
            </div>
        );
    }

    return (
        <>
            <Head>
                <title>Blog Grids | RockStars</title>
                <meta name="description" content="Our latest blog posts" />
            </Head>

            {/* Page Title Section */}
            <section className="relative z-10 pt-[150px] overflow-hidden">
                <div className="container">
                    <div className="flex flex-wrap items-center mx-[-16px]">
                        <div className="w-full md:w-8/12 lg:w-7/12 px-4">
                            <div className="max-w-[570px] mb-12 md:mb-0">
                                <h1 className="font-bold text-black dark:text-white text-2xl sm:text-3xl mb-5">Blog Grids</h1>
                                <p className="font-medium text-base text-body-color leading-relaxed">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. In varius eros eget sapien consectetur ultrices.
                                    Ut quis dapibus libero.
                                </p>
                            </div>
                        </div>
                        <div className="w-full md:w-4/12 lg:w-5/12 px-4">
                            <div className="text-end">
                                <ul className="flex items-center md:justify-end">
                                    <li className="flex items-center">
                                        <Link href="/" className="font-medium text-base text-body-color pr-1 hover:text-primary">Home</Link>
                                        <span className="block w-2 h-2 border-t-2 border-r-2 border-body-color rotate-45 mr-3"></span>
                                    </li>
                                    <li className="font-medium text-base text-primary">Blog Grids</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <span className="absolute top-0 left-0 z-[-1]">
                        <svg width="287" height="254" viewBox="0 0 287 254" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.1" d="M286.5 0.5L-14.5 254.5V69.5L286.5 0.5Z" fill="url(#paint0_linear_111:578)" />
                            <defs>
                                <linearGradient id="paint0_linear_111:578" x1="-40.5" y1="117" x2="301.926" y2="-97.1485" gradientUnits="userSpaceOnUse">
                                    <stop stopColor="#4A6CF7" />
                                    <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </span>
                    <span className="absolute right-0 top-0 z-[-1]">
                        <svg width="628" height="258" viewBox="0 0 628 258" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.1" d="M669.125 257.002L345.875 31.9983L524.571 -15.8832L669.125 257.002Z" fill="url(#paint0_linear_0:1)" />
                            <path opacity="0.1" d="M0.0716344 182.78L101.988 -15.0769L142.154 81.4093L0.0716344 182.78Z" fill="url(#paint1_linear_0:1)" />
                            <defs>
                                <linearGradient id="paint0_linear_0:1" x1="644" y1="221" x2="429.946" y2="37.0429" gradientUnits="userSpaceOnUse">
                                    <stop stopColor="#4A6CF7" />
                                    <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                                </linearGradient>
                                <linearGradient id="paint1_linear_0:1" x1="18.3648" y1="166.016" x2="105.377" y2="32.3398" gradientUnits="userSpaceOnUse">
                                    <stop stopColor="#4A6CF7" />
                                    <stop offset="1" stopColor="#4A6CF7" stopOpacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </span>
                </div>
            </section>

            {/* Blog Section */}
            <section className="pt-[120px] pb-[120px]">
                <div className="container">
                    <div className="flex flex-wrap mx-[-16px] justify-center">
                        {posts.map((post) => (
                            <PostCard key={post.id} post={post} />
                        ))}
                    </div>

                    {/* Pagination */}
                    <div className="w-full">
                        <ul className="flex items-center pt-8 justify-center">
                            {pageInfo?.hasPreviousPage && (
                                <li className="mx-1">
                                    <Link
                                        href={`/blog-grids?before=${pageInfo.startCursor}`}
                                        className="flex items-center justify-center rounded-md bg-body-color bg-opacity-[15%] hover:bg-primary hover:bg-opacity-100 transition hover:text-white text-body-color px-4 text-sm min-w-[36px] h-9"
                                    >
                                        Prev
                                    </Link>
                                </li>
                            )}

                            {pageInfo?.hasNextPage && (
                                <li className="mx-1">
                                    <Link
                                        href={`/blog-grids?after=${pageInfo.endCursor}`}
                                        className="flex items-center justify-center rounded-md bg-body-color bg-opacity-[15%] hover:bg-primary hover:bg-opacity-100 transition hover:text-white text-body-color px-4 text-sm min-w-[36px] h-9"
                                    >
                                        Next
                                    </Link>
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
            </section>
        </>
    );
}

export async function getServerSideProps({ query }) {
    const after = query.after || null;
    const before = query.before || null;

    let variables = {};
    if (before) {
        variables = { last: POSTS_PER_PAGE, before };
    } else if (after) {
        variables = { first: POSTS_PER_PAGE, after };
    } else {
        variables = { first: POSTS_PER_PAGE };
    }

    try {
        const { data } = await client.query({
            query: GET_POSTS,
            variables
        });

        return {
            props: {
                posts: data?.posts?.nodes || [],
                pageInfo: data?.posts?.pageInfo || {},
            }
        };
    } catch (error) {
        console.error('Error fetching posts:', error);
        return {
            props: {
                posts: [],
                pageInfo: {},
                error: error.message
            }
        };
    }
}
