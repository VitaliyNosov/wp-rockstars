// Custom hook to initialize all WordPress scripts
import { useEffect } from 'react';
import { initStickyHeader, initMobileMenu, initSubmenu } from './navigation';
import { initScrollToTop } from './scrollEffects';
import { initFAQ } from './faq';

export function useWordPressScripts() {
    useEffect(() => {
        // Initialize all scripts after component mounts
        const cleanupSticky = initStickyHeader();
        const cleanupMobile = initMobileMenu();
        const cleanupScroll = initScrollToTop();

        initSubmenu();
        initFAQ();

        // Cleanup on unmount
        return () => {
            if (cleanupSticky) cleanupSticky();
            if (cleanupMobile) cleanupMobile();
            if (cleanupScroll) cleanupScroll();
        };
    }, []);
}
