
import '../styles/style.sass';
import '../styles/common/style.css';
import '../styles/style-mod.sass';
import Layout from '../components/Layout';
import Script from 'next/script';
import { useEffect } from 'react';
import { Inter } from 'next/font/google';
import { ApolloProvider } from "@apollo/client";
import client from "../lib/apolloClient";

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
      new WOW.WOW().init();
    }
  }, []);

  return (
    <div className={inter.className}>
      {/* External CSS Libraries */}
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"
      />
      <link
        rel="stylesheet"
        href="https://cdn.plyr.io/3.7.8/plyr.css"
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

      <ApolloProvider client={client}>
        <Layout>
          <Component {...pageProps} />
        </Layout>
      </ApolloProvider>
    </div>
  );
}

export default MyApp;
