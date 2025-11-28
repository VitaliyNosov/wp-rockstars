// const fetch = require('node-fetch');

async function fetchData() {
    const query = `
    query GetPageData {
      nodeByUri(uri: "/") {
        ... on Page {
          id
          title
          heroSection {
            title
            description
          }
          featuresSection {
            featuresSectionTitle
            featuresList {
              featureTitle
            }
          }
        }
      }
    }
  `;

    try {
        const response = await fetch('http://localhost:8081/graphql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ query }),
        });

        const data = await response.json();
        console.log(JSON.stringify(data, null, 2));
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

fetchData();
