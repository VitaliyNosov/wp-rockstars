// FAQ Accordion functionality

export function initFAQ() {
    if (typeof window === 'undefined') return;

    const faqItems = document.querySelectorAll('.single-faq');

    faqItems.forEach(el => {
        const btn = el.querySelector('.faq-btn');
        const icon = el.querySelector('.icon');
        const content = el.querySelector('.faq-content');

        if (btn && icon && content) {
            btn.addEventListener('click', () => {
                icon.classList.toggle('rotate-180');
                content.classList.toggle('hidden');
            });
        }
    });
}

// WordPress-style FAQ toggle function (for compatibility)
export function wpToggleFaq(index) {
    if (typeof window === 'undefined') return;

    const currentAnswer = document.getElementById(`wp-answer-${index}`);
    const currentArrow = document.getElementById(`wp-arrow-${index}`);

    if (!currentAnswer || !currentArrow) return;

    // Close all open items
    document.querySelectorAll('.wp-faq-answer.open').forEach(answer => {
        answer.classList.remove('open');
    });

    document.querySelectorAll('.wp-faq-arrow.wp-faq-rotate-180').forEach(arrow => {
        arrow.classList.remove('wp-faq-rotate-180');
    });

    // Open current item if it wasn't open
    if (!currentAnswer.classList.contains('open')) {
        currentAnswer.classList.add('open');
        currentArrow.classList.add('wp-faq-rotate-180');
    }
}

// Make wpToggleFaq available globally for inline onclick handlers
if (typeof window !== 'undefined') {
    window.wpToggleFaq = wpToggleFaq;
}
