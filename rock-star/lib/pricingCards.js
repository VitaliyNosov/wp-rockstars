// Pricing card selector functionality

export function initPricingCards() {
    if (typeof window === 'undefined') return;

    const cards = document.querySelectorAll('.pricing-card, [class*="border"][class*="rounded-xl"]');
    if (cards.length === 0) return;

    let selectedCard = document.querySelector('[class*="border-2"][class*="border-blue-600"]');
    const selectedBorderClasses = ['border-2', 'border-blue-600'];

    function selectCard(clickedCard) {
        // Remove selection from current card
        if (selectedCard) {
            selectedCard.classList.remove(...selectedBorderClasses);
            selectedCard.classList.add('border', 'border-gray-200');

            if (selectedCard.classList.contains('dark:border-neutral-800') ||
                document.documentElement.classList.contains('dark')) {
                selectedCard.classList.add('dark:border-neutral-800');
            }

            // Reset button
            const prevButton = selectedCard.querySelector('a');
            if (prevButton) {
                prevButton.className = 'mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white dark:text-white shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800';
            }

            // Remove badge
            const prevBadge = selectedCard.querySelector('.custom-color-bage');
            if (prevBadge && prevBadge.parentElement) {
                prevBadge.parentElement.remove();
            }
        }

        // Apply selection to clicked card
        clickedCard.classList.remove('border', 'border-gray-200');
        clickedCard.classList.add(...selectedBorderClasses);

        // Update button
        const button = clickedCard.querySelector('a');
        if (button) {
            button.className = 'mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none';
        }

        // Add badge
        const title = clickedCard.querySelector('h4');
        if (title && !clickedCard.querySelector('.custom-color-bage')) {
            const badge = document.createElement('p');
            badge.className = 'mb-3';
            badge.innerHTML = '<span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-lg text-xs uppercase font-semibold bg-blue-100 text-blue-800 dark:bg-blue-600 dark:text-white custom-color-bage">Your choice</span>';
            title.parentNode.insertBefore(badge, title);
        }

        selectedCard = clickedCard;
    }

    // Add click handlers
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => selectCard(card));
    });
}
