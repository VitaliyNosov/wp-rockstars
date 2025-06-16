// pages/_app.js
import '../styles/style.sass';  // Импорт глобальных стилей

function MyApp({ Component, pageProps }) {
  return <Component {...pageProps} />;
}

export default MyApp;
