import { gql } from '@apollo/client';
import client from '../lib/apolloClient';

export default function DebugPages({ pages, testSlugResult }) {
    return (
        <div style={{ padding: 40, background: '#fff', color: '#000' }}>
            <h1>Deep Debug: Slug Resolver</h1>

            <div style={{ marginBottom: 40, padding: 20, border: '2px solid blue' }}>
                <h2>Slug Test Results for name="landing-page-test"</h2>
                <pre>{JSON.stringify(testSlugResult, null, 2)}</pre>

                {testSlugResult && testSlugResult.length > 0 ? (
                    <h3 style={{ color: 'green' }}>SUCCESS: Found via pages query</h3>
                ) : (
                    <h3 style={{ color: 'red' }}>FAILURE: Not found via pages query</h3>
                )}
            </div>

            <h2>All Pages List</h2>
            <table border="1" cellPadding="10">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>URI</th>
                    </tr>
                </thead>
                <tbody>
                    {pages.map(page => (
                        <tr key={page.id}>
                            <td>{page.id}</td>
                            <td>{page.title}</td>
                            <td>{page.slug}</td>
                            <td>{page.uri}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export async function getServerSideProps() {
    const GET_DEBUG = gql`
    query GetDebug {
      pages(first: 20) {
        nodes {
          id
          title
          slug
          uri
        }
      }
      testSlug: pages(where: {name: "landing-page-test"}) {
        nodes {
          id
          title
          slug
        }
      }
    }
  `;

    try {
        const { data } = await client.query({
            query: GET_DEBUG,
            fetchPolicy: 'no-cache'
        });

        return {
            props: {
                pages: data?.pages?.nodes || [],
                testSlugResult: data?.testSlug?.nodes || null,
            },
        };
    } catch (error) {
        console.error("Error debugging:", error);
        return {
            props: { pages: [], testSlugResult: error.message },
        };
    }
}
