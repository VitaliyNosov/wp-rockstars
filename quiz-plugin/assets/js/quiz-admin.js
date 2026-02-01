(function ($) {
    'use strict';

    /**
     * Quiz Admin - Icon Preview Logic
     */
    const QuizAdmin = {
        init: function () {
            // Watch for changes on any select within Carbon Fields container
            $(document).on('change', 'select[name*="option_icon"]', this.handleIconChange.bind(this));

            // Periodically check for new fields (Carbon Fields is React-based)
            this.startObserver();

            // Initial run
            setTimeout(() => this.updateAllPreviews(), 1000);
        },

        handleIconChange: function (e) {
            const $select = $(e.target);
            this.updatePreview($select);
        },

        updatePreview: function ($select) {
            const iconName = $select.val();
            let $preview = $select.next('.quiz-admin-icon-preview');

            if (!$preview.length) {
                $preview = $('<div class="quiz-admin-icon-preview" data-quiz-internal="true" style="margin-top: 10px; display: flex; align-items: center; gap: 10px; padding: 10px; background: #f0f0f1; border-radius: 4px; border: 1px solid #dcdcde;"></div>');
                $select.after($preview);
            }

            // Optimization: Only update if value changed or preview is empty
            const lastVal = $preview.data('last-val');
            if (lastVal === iconName && $preview.find('i').length) return;
            $preview.data('last-val', iconName);

            if (!iconName) {
                $preview.html('<span style="color: #646970; font-size: 12px;">No icon selected</span>');
                return;
            }

            // Create Icon HTML
            $preview.html(`
                <div style="color: #2271b1; display: flex; align-items: center; justify-content: center; background: #fff; padding: 5px; border-radius: 4px; border: 1px solid #c3c4c7;">
                    <i data-lucide="${iconName}" style="width: 24px; height: 24px;"></i>
                </div>
                <span style="font-weight: 600; color: #1d2327;">${iconName}</span>
            `);

            // Re-run Lucide
            if (window.lucide) {
                window.lucide.createIcons();
            }
        },

        updateAllPreviews: function () {
            $('select[name*="option_icon"]').each((i, el) => {
                this.updatePreview($(el));
            });
        },

        startObserver: function () {
            const targetNode = document.getElementById('wpbody-content');
            if (!targetNode) return;

            this.observer = new MutationObserver((mutations) => {
                let shouldUpdate = false;
                for (const mutation of mutations) {
                    // Only update if nodes were added and they aren't our internal previews
                    if (mutation.addedNodes.length) {
                        const hasExternalNodes = Array.from(mutation.addedNodes).some(node => {
                            if (node.nodeType !== 1) return false;
                            return !node.classList.contains('quiz-admin-icon-preview') &&
                                !node.hasAttribute('data-quiz-internal');
                        });

                        if (hasExternalNodes) {
                            shouldUpdate = true;
                            break;
                        }
                    }
                }

                if (shouldUpdate) {
                    clearTimeout(this.updateTimer);
                    this.updateTimer = setTimeout(() => this.updateAllPreviews(), 250);
                }
            });

            this.observer.observe(targetNode, { childList: true, subtree: true });
        }
    };

    $(document).ready(function () {
        QuizAdmin.init();
    });

})(jQuery);
