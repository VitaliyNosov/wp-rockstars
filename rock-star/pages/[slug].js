import { gql } from '@apollo/client';
import client from '../lib/apolloClient';
import LandingTemplate from '../components/Landing/LandingTemplate';
import Head from 'next/head';

export default function GenericPage({ landingData, pageData, debugInfo }) {
    if (debugInfo) {
        return (
            <div style={{ padding: 40, border: '5px solid red' }}>
                <h1>DEBUG MODE: PAGE NOT FOUND (BUT WHY?)</h1>
                <p><strong>Searched Slug:</strong> {debugInfo.searchedSlug}</p>
                <p><strong>Pages Found in List:</strong> {debugInfo.count}</p>
                <p><strong>Available Slugs:</strong></p>
                <div style={{ background: '#eee', padding: 10, wordWrap: 'break-word' }}>
                    {debugInfo.availableSlugs}
                </div>
                {debugInfo.error && <p style={{ color: 'red' }}>Error: {debugInfo.error}</p>}
            </div>
        )
    }

    // 1. If we have landing data, use the Landing Template
    if (landingData) {
        return (
            <>
                <Head>
                    <title>{pageData?.title || 'Landing Page'}</title>
                </Head>
                <LandingTemplate data={landingData} />
            </>
        );
    }

    // 2. If valid generic page but not a landing template
    if (pageData) {
        return (
            <div className="container mx-auto px-4 py-12">
                <Head>
                    <title>{pageData.title}</title>
                </Head>
                <h1 className="text-4xl font-bold mb-6">{pageData.title}</h1>
                <div dangerouslySetInnerHTML={{ __html: pageData.content }} />
            </div>
        );
    }

    // 3. Fallback (should be handled by debugInfo if something went wrong)
    return <div>Page Not Found</div>;
}

export async function getServerSideProps(context) {
    const { slug } = context.params;

    // WORKAROUND: Direct lookup by URI or Key-Value "where" args is failing.
    // We fetch a list of pages and find the matching slug manually.
    const GET_PAGE_DATA = gql`
    query GetPageData {
      pages(first: 100) {
        nodes {
          id
          title
          slug
          content
          # Landing Page Data Fields
          landingData {
            heroTitle
            heroDescription
            heroBtn1Text
            heroBtn1Url
            heroBtn2Text
            heroBtn2Url
            heroImage
            heroShowReviews
            heroReview1Rating
            heroReview1Count
            heroReview2Rating
            heroReview2Count
            sec2Title
            sec2Content {
                type
                content
                text
                author
                image
                caption
                items
            }
            sec2Tags {
                label
                url
            }
            sec3Header
            sec3Title
            sec3Tabs {
                title
                desc
                icon
                image
            }
            sec4Header
            sec4Cards {
                icon
                title
                desc
            }
            sec5Title
            sec5Faqs {
                icon
                question
                answer
            }
            sec6Title
            sec6Desc
            sec6Cards {
                title
                priceMonthly
                priceAnnual
                desc
                features {
                    text
                }
                btnText
                btnUrl
                isPopular
            }
            sec7Title
            sec7Desc
            sec7ReadMoreText
            sec7ReadMoreUrl
            sec7CategoryId
            sec8Title
            sec8Placeholder
            sec8BtnText
            subscribeNonce
          }
        }
      }
    }
    `;

    console.log("Looking for Slug:", slug);
    try {
        const { data } = await client.query({
            query: GET_PAGE_DATA,
            fetchPolicy: 'no-cache'
        });

        // Manual Filter
        // Note: slugs are usually lower case. We ensure robust comparison.
        const node = data?.pages?.nodes?.find(p => p.slug === slug);

        if (!node) {
            console.log("Page not found in list for Slug:", slug);
            return {
                notFound: true,
            };
        }

        // Check if it has substantial landing data
        const isLanding = node.landingData && (node.landingData.heroTitle || node.landingData.heroDescription);

        let landingData = isLanding ? node.landingData : null;

        // Fetch Blog Posts if valid category ID exists in landingData
        if (landingData && landingData.sec7CategoryId) {
            try {
                const GET_BLOG_POSTS = gql`
                    query GetBlogPosts($catId: Int!) {
                        posts(where: { categoryId: $catId }, first: 3) {
                            nodes {
                                title
                                excerpt
                                link
                                featuredImage {
                                    node {
                                        sourceUrl
                                    }
                                }
                                author {
                                    node {
                                        name
                                        avatar {
                                            url
                                        }
                                    }
                                }
                            }
                        }
                    }
                `;

                const { data: postsData } = await client.query({
                    query: GET_BLOG_POSTS,
                    variables: { catId: parseInt(landingData.sec7CategoryId) },
                    fetchPolicy: 'no-cache'
                });

                if (postsData?.posts?.nodes) {
                    landingData = {
                        ...landingData,
                        sec7Posts: postsData.posts.nodes.map(post => ({
                            title: post.title,
                            excerpt: post.excerpt ? post.excerpt.replace(/(<([^>]+)>)/gi, "").substring(0, 100) + '...' : '', // Simple strip tags
                            link: post.link,
                            thumbnail: post.featuredImage?.node?.sourceUrl,
                            authorName: post.author?.node?.name,
                            authorAvatar: post.author?.node?.avatar?.url
                        }))
                    };
                }

            } catch (err) {
                console.error("Error fetching blog posts:", err);
            }
        }

        return {
            props: {
                landingData: landingData,
                pageData: {
                    title: node.title,
                    content: node.content
                },
            },
        };
    } catch (error) {
        console.error("Error fetching page data:", error);
        return {
            props: {
                debugInfo: {
                    error: error.message
                }
            }
        };
    }
}
