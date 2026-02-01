import { gql } from '@apollo/client';

/**
 * GraphQL Query to fetch quiz settings and structure
 */
export const GET_QUIZ_SETTINGS = gql`
  query GetQuizSettings {
    quizSettings {
      accentColor
      btnNext
      btnPrev
      btnSubmit
      fontFamily
      nonce
      steps {
        title
        description
        fields {
          type
          label
          name
          placeholder
          required
          purpose
          options {
            label
            value
            icon
            image
          }
          # Specific fields for different types
          rows
          fileTypes
          maxSize
          min
          max
          step
          prefix
          suffix
          defaultValue
          mask
          onLabel
          offLabel
          defaultState
          content
          layout
        }
      }
    }
  }
`;

/**
 * GraphQL Mutation to submit quiz results
 */
export const SUBMIT_QUIZ = gql`
  mutation SubmitQuiz($input: SubmitQuizInput!) {
    submitQuiz(input: $input) {
      success
      message
      submissionId
    }
  }
`;

/**
 * Helper to fetch quiz settings
 * @param {ApolloClient} client 
 * @returns {Promise<Object>}
 */
export async function fetchQuizSettings(client) {
  try {
    const { data } = await client.query({
      query: GET_QUIZ_SETTINGS,
      fetchPolicy: 'network-only', // Ensure we get fresh data/nonce
    });
    return data?.quizSettings || null;
  } catch (error) {
    console.error('Error fetching quiz settings:', error);
    return null;
  }
}
