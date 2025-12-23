import React, { useEffect } from 'react';
import Script from 'next/script';
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
    // Re-init preline and Manage Body Class
    useEffect(() => {
        // 1. Add class to body
        document.body.classList.add('landing-page-id');

        // 2. Init Preline
        const initPreline = () => {
            if (typeof window !== 'undefined' && window.HSStaticMethods) {
                try {
                    window.HSStaticMethods.autoInit();
                } catch (e) {
                    // console.log('Preline init error', e);
                }
            }
        };

        // Delay init to wait for DOM - increase delay slightly
        const timeoutId = setTimeout(initPreline, 600);

        // Cleanup
        return () => {
            clearTimeout(timeoutId);
            document.body.classList.remove('landing-page-id');
        };
    }, []);

    if (!data) return <div>Loading...</div>;

    return (
        <div
            className={`${styles.landingWrapper} landing-template-wrapper landing-margin-class`}
        >
            <Script
                src="/js/preline.js"
                strategy="afterInteractive"
                onLoad={() => {
                    setTimeout(() => {
                        if (window.HSStaticMethods) {
                            try {
                                window.HSStaticMethods.autoInit();
                            } catch (e) {
                                console.log('Preline Init Skipped', e);
                            }
                        }
                    }, 500);
                }}
            />

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
