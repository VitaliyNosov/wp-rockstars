// Navigation utilities for sticky header and mobile menu

export function initStickyHeader() {
    if (typeof window === 'undefined') return;

    const handleScroll = () => {
        const header = document.querySelector('.header, .site-header');
        if (!header) return;

        const sticky = header.offsetTop;

        if (window.pageYOffset > sticky) {
            header.classList.add('sticky');
        } else {
            header.classList.remove('sticky');
        }
    };

    window.addEventListener('scroll', handleScroll);

    return () => window.removeEventListener('scroll', handleScroll);
}

export function initMobileMenu() {
    if (typeof window === 'undefined') return;

    const navbarToggler = document.querySelector('#navbarToggler');
    const navbarCollapse = document.querySelector('#navbarCollapse');

    if (!navbarToggler || !navbarCollapse) return;

    const toggleMenu = () => {
        navbarToggler.classList.toggle('navbarTogglerActive');
        navbarCollapse.classList.toggle('hidden');
    };

    navbarToggler.addEventListener('click', toggleMenu);

    // Close navbar when a link is clicked
    const navLinks = document.querySelectorAll('#navbarCollapse ul li:not(.submenu-item) a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navbarToggler.classList.remove('navbarTogglerActive');
            navbarCollapse.classList.add('hidden');
        });
    });

    return () => {
        navbarToggler.removeEventListener('click', toggleMenu);
    };
}

export function initSubmenu() {
    if (typeof window === 'undefined') return;

    const submenuItems = document.querySelectorAll('.submenu-item');

    submenuItems.forEach(el => {
        const link = el.querySelector('a');
        const submenu = el.querySelector('.submenu');

        if (link && submenu) {
            link.addEventListener('click', (e) => {
                // Only toggle on mobile
                if (window.innerWidth < 1024) {
                    e.preventDefault();
                    submenu.classList.toggle('hidden');
                }
            });
        }
    });
}
