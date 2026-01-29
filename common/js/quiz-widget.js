/**
 * Quiz Widget - JavaScript Logic (Dynamic Version)
 */

(function () {
    'use strict';

    class QuizWidget {
        constructor() {
            this.currentStep = 1;
            this.totalSteps = window.QUIZ_TOTAL_STEPS || 10;
            this.answers = {};
            this.labels = {}; // Store field labels for summary

            this.dom = {
                modal: document.getElementById('quiz-modal'),
                closeBtn: document.getElementById('quiz-close-btn'),
                prevBtn: document.getElementById('quiz-prev-btn'),
                nextBtn: document.getElementById('quiz-next-btn'),
                submitBtn: document.getElementById('quiz-submit-btn'),
                progressLineFill: document.getElementById('quiz-steps-line-fill'),
                stepIndicatorsContainer: document.getElementById('quiz-steps-container'),
                stepIndicators: document.querySelectorAll('.quiz-step-indicator-item'),
                body: document.getElementById('quiz-body'),
                summaryContent: document.getElementById('quiz-summary-content')
            };

            this.init();

            // Expose globally
            window.rockstarsQuiz = this;
        }

        init() {
            if (!this.dom.modal) return;
            console.log('Quiz Widget: Initializing (Dynamic)...');
            this.bindEvents();
            this.setupTriggers();
            this.updateUI();

            // Initial icon check
            this.initIcons(3);
        }

        setupTriggers() {
            // 1. CSS Class & Data Attribute (Delegation)
            document.body.addEventListener('click', (e) => {
                const target = e.target.closest('.js-open-quiz, [data-quiz-trigger]');
                if (target) {
                    e.preventDefault();
                    let step = target.dataset.quizStep || target.dataset.step;
                    this.open(step ? parseInt(step) : null);
                }
            });

            // 2. URL Parameter (?open-quiz=true)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('open-quiz')) {
                this.open();
            }

            // 3. URL Hash (#open-quiz)
            if (window.location.hash === '#open-quiz') {
                this.open();
                // Optional: remove hash?
                // history.replaceState(null, null, ' ');
            }

            // 4. Custom Event
            window.addEventListener('open-quiz', (e) => {
                const step = e.detail && e.detail.step ? e.detail.step : null;
                this.open(step);
            });
        }

        bindEvents() {
            // Close modal
            if (this.dom.closeBtn) this.dom.closeBtn.addEventListener('click', () => this.close());

            // Close on backdrop click
            this.dom.modal.addEventListener('click', (e) => {
                if (e.target === this.dom.modal) {
                    this.close();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.dom.modal.classList.contains('active')) {
                    this.close();
                }
            });

            // Navigation buttons
            if (this.dom.prevBtn) this.dom.prevBtn.addEventListener('click', () => this.prevStep());
            if (this.dom.nextBtn) this.dom.nextBtn.addEventListener('click', () => this.nextStep());
            if (this.dom.submitBtn) this.dom.submitBtn.addEventListener('click', () => this.submit());

            // Input validation (Real-time)
            this.dom.modal.addEventListener('input', (e) => {
                if (e.target.matches('.quiz-input')) {
                    this.validateField(e.target);
                }
            });

            // Radio and checkbox selection styling
            this.setupOptionListeners();

            // Range slider listeners
            this.setupRangeListeners();

            // File upload listeners
            this.setupFileListeners();

            // Phone masking listeners
            this.setupPhoneMasks();

            // Switch listeners
            this.setupSwitchListeners();

            // Date picker listeners
            this.setupDatePickers();

            // Init backgrounds
            this.dom.modal.querySelectorAll('.quiz-range-input').forEach(s => this.updateSliderBackground(s));
        }

        updateSliderBackground(slider) {
            const min = parseFloat(slider.min) || 0;
            const max = parseFloat(slider.max) || 100;
            const val = parseFloat(slider.value);
            const percentage = ((val - min) / (max - min)) * 100;

            // Get CSS variables
            const primary = getComputedStyle(document.documentElement).getPropertyValue('--quiz-primary').trim() || '#4A6CF7';
            const border = getComputedStyle(document.documentElement).getPropertyValue('--quiz-border').trim() || '#2E3038';

            slider.style.background = `linear-gradient(to right, ${primary} 0%, ${primary} ${percentage}%, ${border} ${percentage}%, ${border} 100%)`;
        }

        setupRangeListeners() {
            this.dom.modal.addEventListener('input', (e) => {
                if (e.target.matches('.quiz-range-input')) {
                    const slider = e.target;
                    const displayId = `${slider.id}-display`;
                    const display = document.getElementById(displayId);

                    if (display) {
                        const prefix = slider.dataset.prefix || '';
                        const suffix = slider.dataset.suffix || '';
                        display.textContent = `${prefix}${slider.value}${suffix}`;
                    }

                    this.updateSliderBackground(slider);
                }
            });
        }

        setupOptionListeners() {
            // Delegate change event for better performance with dynamic elements
            this.dom.modal.addEventListener('change', (e) => {
                if (e.target.matches('input[type="radio"]') || e.target.matches('input[type="checkbox"]')) {
                    const option = e.target.closest('.quiz-option') || e.target.closest('.quiz-tile-option');
                    if (!option) return;

                    if (e.target.type === 'radio') {
                        // Remove selected class from all options in this group
                        const group = e.target.closest('.quiz-radio-group') || e.target.closest('.quiz-options-grid');
                        if (group) {
                            group.querySelectorAll('.quiz-option, .quiz-tile-option').forEach(opt => {
                                opt.classList.remove('selected');
                            });
                        }
                    }

                    // Toggle selected class
                    if (e.target.checked) {
                        option.classList.add('selected');
                    } else {
                        option.classList.remove('selected');
                    }
                }
            });
        }

        setupFileListeners() {
            this.dom.modal.addEventListener('change', (e) => {
                if (e.target.matches('.quiz-file-input')) {
                    const fileInput = e.target;
                    const wrapper = fileInput.closest('.quiz-file-container');
                    if (!wrapper) return;

                    const fileNameDisplay = wrapper.querySelector('.quiz-file-name');

                    if (fileInput.files.length > 0) {
                        const fileName = fileInput.files[0].name;
                        fileNameDisplay.textContent = fileName;
                        wrapper.classList.add('has-file');

                        // Check extension
                        const allowed = fileInput.dataset.allowed ? fileInput.dataset.allowed.split(',').map(ext => ext.trim().toLowerCase()) : [];
                        const ext = fileName.split('.').pop().toLowerCase();

                        if (allowed.length > 0 && !allowed.includes(ext)) {
                            alert('Invalid file type. Allowed: ' + allowed.join(', '));
                            fileInput.value = '';
                            fileNameDisplay.textContent = 'No file chosen';
                            wrapper.classList.remove('has-file');
                        }
                    } else {
                        fileNameDisplay.textContent = 'No file chosen';
                        wrapper.classList.remove('has-file');
                    }

                    this.validateField(fileInput);
                }
            });
        }

        /**
         * INITIALIZE FLATICKR DATEPICKERS
         */
        setupDatePickers() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.quiz-datepicker', {
                    locale: 'ru',
                    dateFormat: 'd.m.Y',
                    allowInput: true,
                    disableMobile: "true", // Force flatpickr UI on mobile
                    onOpen: function (selectedDates, dateStr, instance) {
                        // Ensure it stays on top of modal
                        instance.calendarContainer.style.zIndex = "2147483647";
                    }
                });
            }
        }

        setupPhoneMasks() {
            this.dom.modal.addEventListener('input', (e) => {
                if (e.target.matches('.quiz-phone-input')) {
                    this.applyPhoneMask(e.target);
                }
            });

            // Prevent cursor moving if mask is present
            this.dom.modal.addEventListener('click', (e) => {
                if (e.target.matches('.quiz-phone-input')) {
                    const input = e.target;
                    const val = input.value;
                    // If empty or only prefix, put cursor at end of static part
                    if (!val || val === input.dataset.mask.replace(/9/g, '_')) {
                        // Find first placeholder
                        const idx = input.dataset.mask.indexOf('9');
                        if (idx !== -1) input.setSelectionRange(idx, idx);
                    }
                }
            });
        }

        setupSwitchListeners() {
            this.dom.modal.addEventListener('change', (e) => {
                if (e.target.matches('.quiz-switch-input')) {
                    const input = e.target;
                    const statusEl = input.closest('.quiz-switch-wrapper').querySelector('.quiz-switch-status');
                    if (statusEl) {
                        statusEl.textContent = input.checked ? (input.dataset.on || 'Yes') : (input.dataset.off || 'No');
                    }
                }
            });
        }

        applyPhoneMask(input) {
            const mask = input.dataset.mask || '+7 (999) 999-99-99';
            let value = input.value;

            // Clean value from non-digits if mask is numeric
            const digits = value.replace(/\D/g, '');
            const maskDigits = mask.replace(/\D/g, '');

            // If user cleared everything, just show empty or prefix
            if (digits.length === 0) {
                input.value = '';
                return;
            }

            let result = '';
            let digitIdx = 0;

            // Iterate through mask
            for (let i = 0; i < mask.length; i++) {
                if (mask[i] === '9') {
                    if (digitIdx < digits.length) {
                        result += digits[digitIdx++];
                    } else {
                        result += '_';
                    }
                } else {
                    // Static character
                    result += mask[i];

                    // If this static char matches a digit user typed (e.g. leading 7), skip it from digits if needed
                    // But usually masks like +7 (999) expect user to start with first 9
                    // So we only skip if prefix matches digits
                    if (digitIdx < digits.length && digits[digitIdx] === mask[i]) {
                        // Optional: logic to skip leading digits if they match prefix
                        // digitIdx++; 
                    }
                }
            }

            input.value = result;

            // Set cursor to first placeholder
            const firstPlaceholder = result.indexOf('_');
            if (firstPlaceholder !== -1) {
                input.setSelectionRange(firstPlaceholder, firstPlaceholder);
            }
        }

        open(step = null) {
            this.dom.modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            if (step && step > 0 && step <= this.totalSteps) {
                this.currentStep = parseInt(step);
                this.updateUI();
            }

            this.initIcons(5);
        }

        initIcons(retries) {
            if (window.lucide) {
                window.lucide.createIcons();

                // Double check after a short delay for elements that might be rendering
                setTimeout(() => window.lucide.createIcons(), 300);
            } else if (retries > 0) {
                setTimeout(() => this.initIcons(retries - 1), 300);
            } else {
                console.error('Quiz Widget: Lucide library NOT found.');
            }
        }

        close() {
            this.dom.modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        nextStep() {
            // Validate current step
            if (!this.validateCurrentStep()) {
                return;
            }

            // Save current step data
            this.saveStepData();

            // Move to next step
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.updateUI();
            }
        }

        prevStep() {
            // Ensure we are not on success screen (if user went back somehow, though buttons should be hidden)

            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateUI();
            }
        }

        validateCurrentStep() {
            const activeStep = this.dom.modal.querySelector(`.quiz-step[data-step="${this.currentStep}"]`);
            if (!activeStep) return true;

            let isValid = true;
            let firstError = null;

            // 1. Validate inputs (text, email, textarea, select)
            const inputs = activeStep.querySelectorAll('.quiz-input');
            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                    if (!firstError) firstError = input;
                }
            });

            // 2. Validate Radio Groups
            const radioGroups = activeStep.querySelectorAll('.quiz-radio-group[data-required="true"]');
            radioGroups.forEach(group => {
                const checked = group.querySelector('input:checked');
                if (!checked) {
                    isValid = false;
                    this.showError(`Please select an option for "${group.dataset.name || 'this field'}"`);
                    // Highlight group?
                }
            });

            // 3. Validate Checkbox Groups
            const checkboxGroups = activeStep.querySelectorAll('.quiz-checkbox-group[data-required="true"]');
            checkboxGroups.forEach(group => {
                // Remove potential [] from name for display if needed
                const checked = group.querySelector('input:checked');
                if (!checked) {
                    isValid = false;
                    this.showError(`Please select at least one option for "${group.dataset.name || 'this field'}"`);
                }
            });

            if (!isValid && firstError) {
                firstError.focus();
                // If simple alert is preferred over Swal for multiple errors
                if (!document.getElementById('quiz-error-toast') || document.getElementById('quiz-error-toast').style.opacity === '0') {
                    // Only show if toast isn't already visible
                    this.showError('Please fill in all required fields correctly.');
                }
            }

            return isValid;
        }

        validateField(field) {
            let isValid = true;



            // Check Required first
            if (field.hasAttribute('required')) {
                // For select elements, check if value is empty string (placeholder)
                if (field.tagName === 'SELECT') {
                    if (!field.value || field.value === '') {
                        isValid = false;

                    }
                } else {
                    // For other inputs, check if trimmed value is empty
                    if (!field.value.trim()) {
                        isValid = false;
                    }
                }

                // For File inputs specifically
                if (field.type === 'file' && !field.files.length) {
                    isValid = false;
                }

                // For masked phone inputs
                if (field.classList.contains('quiz-phone-input')) {
                    if (field.value.includes('_')) {
                        isValid = false;
                    }
                }
            }

            // Check Email format - validate if:
            // 1. Field type is 'email' OR
            // 2. Field has data-purpose='email' (set in admin)
            const shouldValidateEmail = field.type === 'email' || field.dataset.purpose === 'email';

            if (shouldValidateEmail) {
                const emailValue = field.value.trim();
                if (emailValue) {
                    const emailIsValid = this.isValidEmail(emailValue);

                    if (!emailIsValid) {
                        isValid = false;
                    }
                }
            }

            // Apply styling
            if (!isValid) {
                field.classList.add('invalid');
                field.classList.remove('valid');
            } else {
                field.classList.remove('invalid');
                if (field.value && field.value.trim()) {
                    field.classList.add('valid');
                } else {
                    field.classList.remove('valid'); // Empty but optional
                }
            }

            return isValid;
        }

        saveStepData() {
            const activeStep = this.dom.modal.querySelector(`.quiz-step[data-step="${this.currentStep}"]`);
            if (!activeStep) return;

            // Save Inputs (Text, Email, Textarea, Select)
            const inputs = activeStep.querySelectorAll('.quiz-input');
            inputs.forEach(input => {
                this.answers[input.name] = input.value;
                this.labels[input.name] = input.dataset.label || input.name;

                // SPECIAL HANDLING: Check for Identity Purpose
                // If this field is marked as "name" or "email" in the builder, we save it explicitly as user_name/user_email
                // so the backend knows this is the User Identity regardless of the field's random ID/Name.
                const purpose = input.dataset.purpose;
                if (purpose === 'name') this.answers.user_name = input.value;
                if (purpose === 'email') this.answers.user_email = input.value;

                // For Select, save the label of the selected option if possible
                if (input.tagName === 'SELECT') {
                    const selectedOption = input.options[input.selectedIndex];
                }
            });

            // Save Radios
            const radioGroups = activeStep.querySelectorAll('.quiz-radio-group');
            radioGroups.forEach(group => {
                const checked = group.querySelector('input:checked');

                // Identity mapping from Group container
                const purpose = group.dataset.purpose;

                // find the name from the input inside
                const input = group.querySelector('input');
                if (input) {
                    const fieldName = input.name;
                    if (checked) {
                        this.answers[fieldName] = checked.value;
                        const labelEl = group.closest('.quiz-step').querySelector('h3');
                        this.labels[fieldName] = labelEl ? labelEl.textContent : 'Choice';

                        // Identity mapping
                        if (purpose === 'name') this.answers.user_name = checked.value;
                        if (purpose === 'email') this.answers.user_email = checked.value;
                    } else {
                        // Optional radio?
                        if (this.answers[fieldName] === undefined) this.answers[fieldName] = '';
                    }
                }
            });

            // Save Checkboxes
            const checkboxGroups = activeStep.querySelectorAll('.quiz-checkbox-group');
            checkboxGroups.forEach(group => {
                const checkedInputs = group.querySelectorAll('input:checked');
                const purpose = group.dataset.purpose;

                const input = group.querySelector('input');
                if (input) {
                    const fieldName = input.name.replace('[]', ''); // Remove []
                    const checkedValues = Array.from(checkedInputs).map(cb => cb.value);
                    this.answers[fieldName] = checkedValues; // Array

                    // Identity mapping (Using first value or joined string)
                    if (checkedValues.length > 0) {
                        if (purpose === 'name') this.answers.user_name = checkedValues.join(' ');
                        if (purpose === 'email') this.answers.user_email = checkedValues[0]; // Email usually one
                    }
                }
            });

            // Save Switches
            const switches = activeStep.querySelectorAll('.quiz-switch-input');
            switches.forEach(sw => {
                const label = sw.checked ? (sw.dataset.on || 'Yes') : (sw.dataset.off || 'No');
                this.answers[sw.name] = label;
                this.labels[sw.name] = sw.dataset.label || sw.name;
            });
        }

        updateUI() {
            // Update Progress Line
            // (currentStep - 1) / (totalSteps - 1) because lines are between steps
            let progress = 0;
            if (this.totalSteps > 1) {
                progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            }
            if (this.dom.progressLineFill) {
                this.dom.progressLineFill.style.width = progress + '%';
            }

            // Update Circles
            if (this.dom.stepIndicators) {
                this.dom.stepIndicators.forEach((el, index) => {
                    const stepNum = index + 1;

                    if (stepNum < this.currentStep) {
                        el.classList.add('completed');
                        el.classList.remove('active');
                    } else if (stepNum === this.currentStep) {
                        el.classList.add('active');
                        el.classList.remove('completed');

                        // Auto-scroll to center active step
                        if (this.dom.stepIndicatorsContainer) {
                            // Calculate center position
                            const container = this.dom.stepIndicatorsContainer;
                            const itemLeft = el.offsetLeft;
                            const itemWidth = el.offsetWidth;
                            const containerWidth = container.offsetWidth;

                            const scrollPos = itemLeft - (containerWidth / 2) + (itemWidth / 2);

                            container.scrollTo({
                                left: scrollPos,
                                behavior: 'smooth'
                            });
                        }
                    } else {
                        el.classList.remove('active', 'completed');
                    }
                });
            }

            // Show/hide steps
            const steps = document.querySelectorAll('.quiz-step');
            steps.forEach((step) => {
                if (parseInt(step.dataset.step) === this.currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Update buttons
            if (this.currentStep === 1) {
                this.dom.prevBtn.style.display = 'none';
            } else {
                this.dom.prevBtn.style.display = 'block';
            }

            if (this.currentStep === this.totalSteps) {
                // Summary Step
                this.dom.nextBtn.style.display = 'none';
                this.dom.submitBtn.style.display = 'block';
                this.generateSummary();
            } else {
                this.dom.nextBtn.style.display = 'block';
                this.dom.submitBtn.style.display = 'none';
            }
            // Ensure icons are processed for the current visible step
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Scroll to top
            if (this.dom.body) this.dom.body.scrollTop = 0;
        }

        generateSummary() {
            let html = '';

            // Loop through all previous steps (1 to totalSteps - 1)
            for (let i = 1; i < this.totalSteps; i++) {
                const stepDiv = this.dom.modal.querySelector(`.quiz-step[data-step="${i}"]`);
                if (!stepDiv) continue;

                const title = stepDiv.dataset.title || `Step ${i}`;
                let stepContent = '';

                // Find all fields in this step to show them
                // 1. Inputs (Text, Email, Textarea, Select, Range)
                stepDiv.querySelectorAll('.quiz-input').forEach(input => {
                    const label = input.dataset.label || input.name;
                    const val = this.answers[input.name];
                    if (val !== undefined && val !== '') {
                        // Convert selects to readable
                        let displayVal = val;
                        if (input.tagName === 'SELECT') {
                            const option = Array.from(input.options).find(o => o.value === val);
                            if (option) displayVal = option.textContent;
                        }

                        // Add prefix/suffix for range if available in summary
                        if (input.type === 'range') {
                            const prefix = input.dataset.prefix || '';
                            const suffix = input.dataset.suffix || '';
                            displayVal = `${prefix}${val}${suffix}`;
                        }

                        stepContent += `<p><strong>${this.escapeHtml(label)}:</strong> ${this.escapeHtml(displayVal)}</p>`;
                    }
                });

                // 2. Radios
                stepDiv.querySelectorAll('.quiz-radio-group, .quiz-options-grid').forEach(group => {
                    const input = group.querySelector('input[type="radio"]');
                    if (input) {
                        const name = input.name;
                        const val = this.answers[name];
                        if (val) {
                            // Find label for value
                            const selectedInput = group.querySelector(`input[value="${val}"]`);
                            const selectedLabel = selectedInput ? selectedInput.dataset.label : val;

                            let groupLabel = 'Choice';
                            const labelEl = group.closest('.quiz-step').querySelector('h3');
                            if (labelEl && labelEl.tagName === 'LABEL') groupLabel = labelEl.textContent.replace('*', '').trim();
                            // Fallback if label is before the group
                            if (!labelEl || labelEl.tagName !== 'LABEL') {
                                const prevLabel = group.previousElementSibling;
                                if (prevLabel && prevLabel.tagName === 'LABEL') groupLabel = prevLabel.textContent.replace('*', '').trim();
                                else if (title) groupLabel = title;
                            }

                            stepContent += `<p><strong>${this.escapeHtml(groupLabel)}:</strong> ${this.escapeHtml(selectedLabel)}</p>`;
                        }
                    }
                });

                // 3. Checkboxes
                stepDiv.querySelectorAll('.quiz-checkbox-group, .quiz-options-grid').forEach(group => {
                    const input = group.querySelector('input[type="checkbox"]');
                    if (input) {
                        const name = input.name.replace('[]', '');
                        const vals = this.answers[name]; // Array
                        if (vals && vals.length > 0) {
                            let groupLabel = 'Selection';
                            const labelEl = group.previousElementSibling;
                            if (labelEl && labelEl.tagName === 'LABEL') groupLabel = labelEl.textContent.replace('*', '').trim();
                            else if (title) groupLabel = title;

                            // Map vals to labels
                            const displayVals = vals.map(v => {
                                const cb = group.querySelector(`input[value="${v}"]`);
                                var label = v;
                                if (cb && cb.dataset.label) label = cb.dataset.label;
                                return label;
                            }).join(', ');

                            stepContent += `<p><strong>${this.escapeHtml(groupLabel)}:</strong> ${this.escapeHtml(displayVals)}</p>`;
                        }
                    }

                    // 4. File uploads
                    stepDiv.querySelectorAll('.quiz-file-input').forEach(fileInput => {
                        const label = fileInput.dataset.label || fileInput.name;
                        const file = fileInput.files[0];
                        if (file) {
                            stepContent += `<p><strong>${this.escapeHtml(label)}:</strong> ${this.escapeHtml(file.name)}</p>`;
                        }
                    });

                    // 5. Switches
                    stepDiv.querySelectorAll('.quiz-switch-input').forEach(sw => {
                        const label = sw.dataset.label || sw.name;
                        const status = sw.checked ? (sw.dataset.on || 'Yes') : (sw.dataset.off || 'No');
                        stepContent += `<p><strong>${this.escapeHtml(label)}:</strong> ${this.escapeHtml(status)}</p>`;
                    });
                });

                if (stepContent) {
                    html += `<div class="quiz-summary"><h4>${this.escapeHtml(title)}</h4>${stepContent}</div>`;
                }
            }

            this.dom.summaryContent.innerHTML = html || '<p>No information provided.</p>';
        }

        async submit() {
            console.log('Quiz: Submitting...', this.answers);

            this.dom.submitBtn.innerHTML = 'Sending<span class="loading-dot">.</span><span class="loading-dot">.</span><span class="loading-dot">.</span>';
            this.dom.submitBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('action', 'submit_quiz');
                formData.append('nonce', quiz_ajax.nonce);

                // Add common fields expected by backend (if explicit)
                formData.append('user_name', this.answers['user_name'] || this.answers['name'] || this.answers['full_name'] || 'Anonymous');
                formData.append('user_email', this.answers['user_email'] || this.answers['email'] || '');

                // Add ALL answers
                for (const [key, value] of Object.entries(this.answers)) {
                    if (Array.isArray(value)) {
                        value.forEach(v => formData.append(`${key}[]`, v));
                    } else {
                        formData.append(key, value);
                    }
                }

                // Add FILES
                this.dom.modal.querySelectorAll('.quiz-file-input').forEach(input => {
                    if (input.files[0]) {
                        formData.append(input.name, input.files[0]);
                    }
                });

                const response = await fetch(quiz_ajax.ajax_url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccess();
                } else {
                    this.showError(data.data || 'An error occurred while submitting');
                    this.showError(data.data || 'An error occurred while submitting');
                    this.dom.submitBtn.textContent = this.dom.submitBtn.dataset.originalText || 'Submit';
                    this.dom.submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Quiz Error:', error);
                this.showError('Network error. Please try again.');
                console.error('Quiz Error:', error);
                this.showError('Network error. Please try again.');
                this.dom.submitBtn.textContent = this.dom.submitBtn.dataset.originalText || 'Submit';
                this.dom.submitBtn.disabled = false;
            }
        }

        isValidEmail(email) {
            // More strict email validation
            // Requires: 
            // - At least 2 characters before @
            // - At least 2 characters for domain name
            // - At least 2 characters for TLD
            // - Valid characters only
            const emailRegex = /^[a-zA-Z0-9._-]{2,}@[a-zA-Z0-9.-]{2,}\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }

        showSuccess() {
            // Replace modal content with success message IN-PLACE
            this.dom.submitBtn.style.display = 'none';
            this.dom.prevBtn.style.display = 'none';
            this.dom.nextBtn.style.display = 'none';
            const stepsWrapper = this.dom.modal.querySelector('.quiz-steps-wrapper');
            if (stepsWrapper) stepsWrapper.style.display = 'none';

            const successHTML = `
                <div class="quiz-success-message" style="text-align: center; padding: 40px 20px; animation: fadeInUp 0.5s ease;">
                    <div style="width: 80px; height: 80px; background: var(--quiz-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: white; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 style="color: var(--quiz-text); margin-bottom: 16px; font-size: 28px; font-weight: 700;">Thank You!</h3>
                    <p style="color: var(--quiz-text); opacity: 0.8; font-size: 18px; margin-bottom: 32px; line-height: 1.6;">Your answers have been successfully submitted.<br>We will contact you shortly.</p>
                    <button id="quiz-success-close" class="quiz-btn quiz-btn-primary" style="min-width: 150px;">Close</button>
                </div>
            `;

            this.dom.body.innerHTML = successHTML;

            // Re-bind close button inside success message
            setTimeout(() => {
                const newCloseBtn = document.getElementById('quiz-success-close');
                if (newCloseBtn) {
                    newCloseBtn.addEventListener('click', () => {
                        this.close();
                        // Reload page to reset form state and prevent double submission
                        location.reload();
                    });
                }
            }, 100);
        }

        showError(message) {
            // Create toast if not exists
            let toast = document.getElementById('quiz-error-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'quiz-error-toast';
                toast.style.cssText = `
                   position: absolute;
                   top: 20px;
                   left: 50%;
                   transform: translateX(-50%);
                   background: var(--quiz-error);
                   color: white;
                   padding: 10px 20px;
                   border-radius: 8px;
                   box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                   z-index: 100000;
                   opacity: 0;
                   transition: opacity 0.3s ease;
                   font-weight: 500;
               `;
                this.dom.modal.appendChild(toast);
            }

            toast.textContent = message;
            toast.style.opacity = '1';

            // Hide after 3s
            setTimeout(() => {
                toast.style.opacity = '0';
            }, 3000);
        }

        escapeHtml(text) {
            if (!text) return text;
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    }

    // Init on DOM ready
    // Init on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        new QuizWidget();

        // Expose to global scope for button clicks (Backward Compatibility)
        window.openQuizModal = function (step = null) {
            if (window.rockstarsQuiz) {
                window.rockstarsQuiz.open(step);
            }
        };
    });

})();
