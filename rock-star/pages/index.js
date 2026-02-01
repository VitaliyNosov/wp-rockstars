import { gql } from '@apollo/client';
import client from '../lib/apolloClient';
import { fetchQuizSettings } from '../lib/quiz';
import HeroSection from '../components/HeroSection';
import FeaturesSection from '../components/FeaturesSection';
import VideoSection from '../components/VideoSection';
import BrandsSection from '../components/BrandsSection';
import PortfolioSlider from '../components/PortfolioSlider';
import AboutSection from '../components/AboutSection';
import BenefitsSection from '../components/BenefitsSection';
import TestimonialsSection from '../components/TestimonialsSection';
import PricingSection from '../components/PricingSection';
import BlogSection from '../components/BlogSection';
import { QuizProvider } from '../components/Quiz/QuizContext';
import QuizModal from '../components/Quiz/QuizModal';
import ContactSection from '../components/ContactSection';

export default function Home({ heroData, featuresData, videoData, brandsData, portfolioData, aboutData, benefitsData, testimonialsData, pricingData, blogPosts, quizSettings }) {
  return (
    <QuizProvider settings={quizSettings}>
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
      <BrandsSection
        logos={brandsData?.brandLogosList}
      />
      <PortfolioSlider
        slides={portfolioData?.portfolioSlides?.length > 0 ? portfolioData.portfolioSlides : [
          {
            slideImage: 'https://via.placeholder.com/600x400',
            slideUrl: 'https://example.com',
            slideAlt: 'Test Slide 1'
          },
          {
            slideImage: 'https://via.placeholder.com/600x400',
            slideUrl: 'https://google.com',
            slideAlt: 'Test Slide 2'
          }
        ]}
      />
      <AboutSection
        title={aboutData?.title}
        subtitle={aboutData?.subtitle}
        image={aboutData?.image}
        features={aboutData?.features}
      />
      <BenefitsSection
        image={benefitsData?.image}
        benefits={benefitsData?.benefits}
      />
      <TestimonialsSection
        title={testimonialsData?.title}
        description={testimonialsData?.description}
        testimonials={testimonialsData?.testimonials}
      />
      <PricingSection
        enabled={pricingData?.enabled}
        title={pricingData?.title}
        description={pricingData?.description}
        monthlyLabel={pricingData?.monthlyLabel}
        yearlyLabel={pricingData?.yearlyLabel}
        pricingPlans={pricingData?.pricingPlans}
      />
      <BlogSection
        posts={blogPosts}
      />
      <ContactSection />

      <QuizModal />
    </QuizProvider>
  );
}

export async function getServerSideProps() {
  const GET_PAGE_DATA = gql`
    query GetPageData {
      posts(first: 3, where: {categoryName: "lasts-posts"}) {
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
      testimonialsSection {
        title
        description
        testimonials {
          rating
          text
          photo
          name
          position
        }
      }
      pricingSection {
        enabled
        title
        description
        monthlyLabel
        yearlyLabel
        pricingPlans {
          name
          monthlyPrice
          yearlyPrice
          description
          buttonText
          buttonUrl
          isPopular
          features {
            text
            status
          }
        }
      }
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
          brandsSection {
            brandLogosList {
              brandLogo
              brandLink
              brandAlt
            }
          }
          portfolioSection {
            portfolioSlides {
              slideImage
              slideUrl
              slideAlt
            }
          }
          aboutSection {
            title
            subtitle
            image
            features {
              featureText
            }
          }
          benefitsSection {
            image
            benefits {
              benefitTitle
              benefitDescription
            }
          }
        }
      }
    }
  `;

  try {
    const { data } = await client.query({
      query: GET_PAGE_DATA,
    });

    const posts = data?.posts?.nodes?.map(post => ({
      id: post.id,
      title: post.title,
      excerpt: post.excerpt,
      slug: post.slug,
      date: post.date,
      featuredImage: post.featuredImage,
      categories: post.categories,
      author: post.author,
    })) || [];

    // Fetch Quiz Settings
    const quizSettings = await fetchQuizSettings(client);

    return {
      props: {
        heroData: data?.nodeByUri?.heroSection || null,
        featuresData: data?.nodeByUri?.featuresSection || null,
        videoData: data?.nodeByUri?.videoSection || null,
        brandsData: data?.nodeByUri?.brandsSection || null,
        portfolioData: data?.nodeByUri?.portfolioSection || null,
        aboutData: data?.nodeByUri?.aboutSection || null,
        benefitsData: data?.nodeByUri?.benefitsSection || null,
        testimonialsData: data?.testimonialsSection || null,
        pricingData: data?.pricingSection || null,
        blogPosts: posts,
        quizSettings: quizSettings,
      },
    };
  } catch (error) {
    console.error("Error fetching page data:", error);
    return {
      props: {
        heroData: null,
        featuresData: null,
        videoData: null,
        brandsData: null,
        portfolioData: null,
        aboutData: null,
        benefitsData: null,
        testimonialsData: null,
        pricingData: null,
        blogPosts: [],
        quizSettings: null,
      },
    };
  }
}