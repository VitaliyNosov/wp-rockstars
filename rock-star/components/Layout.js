import Header from './Header';
import Footer from './Footer';
import { useWordPressScripts } from '../lib/useWordPressScripts';

export default function Layout({ children }) {
    // Initialize all WordPress scripts
    useWordPressScripts();

    return (
        <div className="site-wrapper">
            <Header />
            <main className="site-main">
                {children}
            </main>
            <Footer />
        </div>
    );
}
