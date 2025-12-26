import React, { useEffect } from 'react';
import styles from '../../styles/Landing.module.sass';
import HeroSection from './HeroSection';
import SectionTwo from './SectionTwo';
import SectionThree from './SectionThree';
import SectionFour from './SectionFour';
import SectionFive from './SectionFive';
import SectionSix from './SectionSix';
import SectionSeven from './SectionSeven';
import SectionEight from './SectionEight';

// We will import sub-components here as we create them
// import TabsSection from './TabsSection';
// import PricingSection from './PricingSection';

const LandingTemplate = ({ data }) => {
    // Manage Body Class
    useEffect(() => {
        // 1. Add class to body
        document.body.classList.add('landing-page-id');

        // Cleanup
        return () => {
            document.body.classList.remove('landing-page-id');
        };
    }, []);

    if (!data) return <div>Loading...</div>;

    return (
        <div
            className={`${styles.landingWrapper} landing-template-wrapper landing-margin-class`}
        >

            {/* Hero Section */}
            <section className={styles.heroSection}>
                <HeroSection data={data} />
            </section>

            {/* Section Two (Content) */}
            <SectionTwo data={data} />

            {/* Section Three (Tabs) */}
            <SectionThree data={data} />

            {/* Section Four (Icon Blocks) */}
            <SectionFour data={data} />

            {/* Section Five (FAQ) */}
            <SectionFive data={data} />

            {/* Section Six (Pricing) */}
            <SectionSix data={data} />

            {/* Section Seven (Blog) */}
            <SectionSeven data={data} />

            {/* Section Eight (Subscribe) */}
            <SectionEight data={data} />

            {/* Debug: Dump Data */}
            <pre style={{ display: 'none' }}>{JSON.stringify(data, null, 2)}</pre>
        </div>
    );
};

export default LandingTemplate;
