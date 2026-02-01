
// Using native fetch (Node 18+)

const API_URL = 'http://localhost:8081/graphql';

const GET_SETTINGS_QUERY = `
query GetQuizSettings {
  quizSettings {
    nonce
  }
}
`;

const SUBMIT_MUTATION = `
mutation SubmitQuiz($input: SubmitQuizInput!) {
  submitQuiz(input: $input) {
    success
    message
    submissionId
  }
}
`;

async function run() {
    try {
        // 1. Get Nonce
        console.log('Fetching nonce...');
        const res1 = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: GET_SETTINGS_QUERY })
        });
        const json1 = await res1.json();
        const nonce = json1.data?.quizSettings?.nonce;

        if (!nonce) {
            console.error('Failed to get nonce:', JSON.stringify(json1, null, 2));
            return;
        }
        console.log('Got nonce:', nonce);

        // 2. Submit
        console.log('Submitting quiz...');
        const variables = {
            input: {
                clientMutationId: 'debug-test-' + Date.now(),
                nonce: nonce,
                answers: [
                    { name: "test_field", value: "debug_value" }
                ]
            }
        };

        const res2 = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: SUBMIT_MUTATION, variables })
        });
        const json2 = await res2.json();
        console.log('Submission Response:', JSON.stringify(json2, null, 2));

    } catch (e) {
        console.error('Error:', e);
    }
}

run();
