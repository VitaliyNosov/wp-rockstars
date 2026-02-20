const API_URL = 'http://localhost:8081/graphql';

// 1. Query Chat Status (Online/Offline)
const GET_CHAT_STATUS = `
query GetChatStatus {
  chatStatus {
    isOnline
    offlineMsg
  }
}
`;

// 2. Query Chat History
const GET_CHAT_HISTORY = `
query GetChatHistory($sessionId: String!) {
  chatHistory(sessionId: $sessionId) {
    sessionId
    messages {
      role
      text
      attachmentUrl
      isVoice
      isVideo
      timestamp
    }
  }
}
`;

// 3. Mutation Send Message
const SEND_CHAT_MESSAGE = `
mutation SendChatMessage($input: SendChatMessageInput!) {
  sendChatMessage(input: $input) {
    success
    message
    sentMessage {
      text
      attachmentUrl
    }
  }
}
`;

async function testChat() {
    const testSessionId = 'debug-session-' + Math.random().toString(36).substring(7);

    try {
        console.log('--- Testing chatStatus ---');
        const res1 = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: GET_CHAT_STATUS })
        });
        const json1 = await res1.json();
        console.log('Status Response:', JSON.stringify(json1.data?.chatStatus, null, 2));

        console.log('\n--- Testing sendChatMessage ---');
        const variables = {
            input: {
                sessionId: testSessionId,
                name: 'Debug User',
                email: 'debug@example.com',
                message: 'Hello from React Debug Script!'
            }
        };
        const res2 = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: SEND_CHAT_MESSAGE, variables })
        });
        const json2 = await res2.json();
        console.log('Full Send Response:', JSON.stringify(json2, null, 2));
        if (json2.errors) {
            console.error('GraphQL Errors:', JSON.stringify(json2.errors, null, 2));
        }

        console.log('\n--- Testing chatHistory ---');
        const res3 = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                query: GET_CHAT_HISTORY,
                variables: { sessionId: testSessionId }
            })
        });
        const json3 = await res3.json();
        console.log('History Response:', JSON.stringify(json3.data?.chatHistory, null, 2));

    } catch (e) {
        console.error('Error during testing:', e);
    }
}

testChat();
