import { gql } from '@apollo/client';
import client from '../lib/apolloClient';
import HeroSection from '../components/HeroSection';
import FeaturesSection from '../components/FeaturesSection';
import VideoSection from '../components/VideoSection';

export default function Home({ heroData, featuresData, videoData }) {
  return (
    <>
      <HeroSection
        title={heroData?.title}
        description={heroData?.description}
        button1Text={heroData?.button1Text}
        button1Url={heroData?.button1Url}
        button2Text={heroData?.button2Text}
        button2Url={heroData?.button2Url}
      />
      <FeaturesSection
        title={featuresData?.featuresSectionTitle}
        description={featuresData?.featuresSectionDescription}
        features={featuresData?.featuresList}
      />
      <VideoSection
        title={videoData?.videoSectionTitle}
        description={videoData?.videoSectionDescription}
        videoUrl={videoData?.videoYoutubeUrl}
        previewImage={videoData?.videoPreviewImage}
        backgroundShape={videoData?.videoBackgroundShape}
      />
    </>
  );
}

export async function getStaticProps() {
  const GET_PAGE_DATA = gql`
    query GetPageData {
      nodeByUri(uri: "/") {
        ... on Page {
          heroSection {
            title
            description
            button1Text
            button1Url
            button2Text
            button2Url
          }
          featuresSection {
            featuresSectionTitle
            featuresSectionDescription
            featuresList {
              featureIconSvg
              featureTitle
              featureDescription
            }
          }
          videoSection {
            videoSectionTitle
            videoSectionDescription
            videoPreviewImage
            videoYoutubeUrl
            videoBackgroundShape
          }
        }
      }
    }
  `;

  try {
    const { data } = await client.query({
      query: GET_PAGE_DATA,
    });

    return {
      props: {
        heroData: data?.nodeByUri?.heroSection || null,
        featuresData: data?.nodeByUri?.featuresSection || null,
        videoData: data?.nodeByUri?.videoSection || null,
      },
      revalidate: 10,
    };
  } catch (error) {
    console.error("Error fetching page data:", error);
    return {
      props: {
        heroData: null,
        featuresData: null,
        videoData: null,
      },
      revalidate: 10,
    };
  }
}