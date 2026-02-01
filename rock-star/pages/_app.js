
import '../styles/style.sass';
import '../styles/common/style.css';
import '../styles/tailwind.css';
import '../styles/style-mod.sass';
import '../styles/Quiz.scss';
import Layout from '../components/Layout';
import Script from 'next/script';
import { useEffect } from 'react';
import { Inter } from 'next/font/google';
import { ApolloProvider } from "@apollo/client";
import client from "../lib/apolloClient";
import NextNProgress from 'nextjs-progressbar';
import CookieConsent from "react-cookie-consent";
import { initAnalytics } from '../lib/analytics';

const inter = Inter({
  subsets: ['latin'],
  weight: ['100', '200', '300', '400', '500', '600', '700', '800', '900'],
  display: 'swap',
});

function MyApp({ Component, pageProps }) {
  useEffect(() => {
    // Initialize GLightbox when it's loaded
    if (typeof window !== 'undefined' && window.GLightbox) {
      const lightbox = window.GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
      });
    }

    // Initialize WOW.js
    if (typeof window !== 'undefined') {
      const WOW = require('wowjs');
      new WOW.WOW({
        live: false // Disable MutationObserver to prevent console warnings
      }).init();
    }
  }, []);

  return (
    <div className={inter.className}>
      <NextNProgress color="#4A6CF7" options={{ showSpinner: false }} />
      <CookieConsent
        location="bottom"
        buttonText="Accept"
        cookieName="myAwesomeCookieName2"
        buttonStyle={{ color: "#ffffff", fontSize: "14px", background: "#4A6CF7", borderRadius: "8px", fontWeight: "500", padding: "10px 20px" }}
        expires={150}
        containerClasses="cookie-consent-container"
        disableStyles={true}
        onAccept={() => {
          initAnalytics();
        }}
      >
        <span className="font-semibold block mb-1 text-base text-gray-900 dark:text-white">Cookies Policy</span>
        We use cookies to improve your experience.
      </CookieConsent>
      <style jsx global>{`
        .cookie-consent-container {
          position: fixed !important;
          bottom: 20px !important;
          left: 20px !important;
          width: 380px !important;
          background: white !important;
          color: #1f2937 !important;
          padding: 20px !important;
          border-radius: 0.375rem !important;
          box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
          z-index: 2147483647 !important;
          display: flex !important;
          flex-direction: column !important;
          gap: 10px !important;
          border: 1px solid #e5e7eb !important;
        }
        .dark .cookie-consent-container {
          background: #000000 !important;
          color: #f3f4f6 !important;
          border-color: #2e2e2e !important;
        }
        @media (max-width: 640px) {
          .cookie-consent-container {
            width: calc(100% - 40px) !important;
            bottom: 20px !important;
            left: 20px !important;
          }
        }
      `}</style>

      {/* External CSS Libraries */}
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"
      />
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.css"
      />
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
      />

      {/* External JS Libraries */}
      <Script
        src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js"
        strategy="beforeInteractive"
      />

      <Script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
        strategy="lazyOnload"
        onLoad={() => {
          // Override default alert with SweetAlert2
          if (typeof window !== 'undefined' && window.Swal) {
            window.alert = function (message) {
              window.Swal.fire({
                toast: true,
                position: 'bottom-start',
                icon: false,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                  popup: 'custom-toast'
                }
              });
            };
          }
        }}
      />

      <Script
        src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"
        strategy="lazyOnload"
        onLoad={() => {
          if (typeof window !== 'undefined' && window.GLightbox) {
            const lightbox = window.GLightbox({
              selector: '.glightbox',
              touchNavigation: true,
              loop: true,
              autoplayVideos: true
            });
          }
        }}
      />

      <Script
        src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"
        strategy="lazyOnload"
      />

      <Script
        src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"
        strategy="lazyOnload"
      />

      {/* Flatpickr */}
      <Script
        src="https://cdn.jsdelivr.net/npm/flatpickr"
        strategy="lazyOnload"
      />
      <Script
        src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"
        strategy="lazyOnload"
      />

      <ApolloProvider client={client}>
        <Layout>
          <Component {...pageProps} />
        </Layout>
      </ApolloProvider>
    </div>
  );
}

export default MyApp;
