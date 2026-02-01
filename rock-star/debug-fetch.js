
const query = `
query {
  quizSettings {
    steps {
      title
      description
      fields {
        type
        label
        name
      }
    }
  }
}
`;

async function test() {
    try {
        const res = await fetch('http://localhost:8081/graphql', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query })
        });

        const json = await res.json();
        console.log(JSON.stringify(json, null, 2));
    } catch (e) {
        console.error(e);
    }
}

test();
