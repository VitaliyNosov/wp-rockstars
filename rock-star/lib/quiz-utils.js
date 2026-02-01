/**
 * Utility functions for the Quiz
 */

/**
 * Validates an email address
 * @param {string} email 
 * @returns {boolean}
 */
export const isValidEmail = (email) => {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
};

/**
 * Applies a phone mask to a string value
 * @param {string} value - Current input value
 * @param {string} mask - Mask pattern (e.g. +7 (999) 999-99-99)
 * @returns {string} - Masked value
 */
export const applyPhoneMask = (value, mask = '+7 (999) 999-99-99') => {
    const digits = value.replace(/\D/g, '');
    if (digits.length === 0) return '';

    let result = '';
    let digitIdx = 0;

    for (let i = 0; i < mask.length; i++) {
        if (mask[i] === '9') {
            if (digitIdx < digits.length) {
                result += digits[digitIdx++];
            } else {
                result += '_';
            }
        } else {
            result += mask[i];
        }
    }

    return result;
};

/**
 * Escapes HTML characters
 * @param {string} unsafe 
 * @returns {string}
 */
export const escapeHtml = (unsafe) => {
    if (typeof unsafe !== 'string') return unsafe;
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};

/**
 * Sanitizes field name for use as ID
 * @param {string} name 
 * @returns {string}
 */
export const sanitizeId = (name) => {
    return 'quiz-field-' + name.replace(/[^a-z0-9]/gi, '-').toLowerCase();
};
