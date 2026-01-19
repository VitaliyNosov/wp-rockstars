<?php
/**
 * Quiz Modal - HTML markup and inline script
 */

/**
 * Get Quiz Structure (from Carbon Fields or Default Fallback)
 */
function get_quiz_structure() {
    // Only try to get option if Carbon Fields is active
    if (function_exists('carbon_get_theme_option')) {
        $structure = carbon_get_theme_option('quiz_structure');
        if (!empty($structure)) {
            return $structure;
        }
    }

    // Default Fallback
    return array(
        array(
            '_type' => 'step',
            'step_title' => 'Personal Information',
            'step_description' => 'Tell us about yourself',
            'step_fields' => array(
                array(
                    '_type' => 'text',
                    'field_label' => 'Your Name *',
                    'field_name' => 'user_name',
                    'field_placeholder' => 'Enter your name',
                    'field_required' => true,
                    'field_purpose' => 'name',
                ),
                array(
                    '_type' => 'email',
                    'field_label' => 'Email *',
                    'field_name' => 'user_email',
                    'field_placeholder' => 'your@email.com',
                    'field_required' => true,
                    'field_purpose' => 'email',
                )
            )
        )
    );
}

function render_quiz_modal_html() {
    $steps = get_quiz_structure();
    $total_steps = count($steps) + 1; // +1 for Summary step
    ?>
    <style>
        /* Quiz Modal Styles - Matching Site Theme */
        :root {
            --quiz-primary: #4A6CF7; /* Site primary color */
            --quiz-success: #10b981;
            --quiz-error: #ef4444;
            --quiz-warning: #f59e0b;
        }
        
        /* Light theme (default) */
        :root {
            --quiz-bg: #ffffff;
            --quiz-card-bg: #ffffff;
            --quiz-text: #1f2937;
            --quiz-border: #e5e7eb;
            --quiz-input-bg: #f9fafb;
        }
        
        /* Dark theme */
        .dark {
            --quiz-bg: #060607; /* User Specified */
            --quiz-card-bg: #060607;
            --quiz-text: #ffffff;
            --quiz-border: #2E3038; /* User Specified */
            --quiz-input-bg: #0c0c0e;
        }
        
        .dark .quiz-container {
            border: 1px solid var(--quiz-border);
        }
        
        #quiz-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 99999;
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        #quiz-modal.active {
            display: flex;
            opacity: 1;
        }
        
        .quiz-container {
            background: var(--quiz-bg);
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            /* Fixed max-height with Flexbox for internal scrolling */
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        #quiz-modal.active .quiz-container {
            transform: scale(1);
        }
        
        .quiz-header {
            background: transparent;
            color: var(--quiz-text);
            padding: 24px;
            position: relative;
            flex-shrink: 0;
            text-align: center;
        }
        
        .quiz-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: transparent;
            border: none;
            color: var(--quiz-text);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        
        .quiz-close:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .dark .quiz-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .quiz-progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 16px;
        }
        
        .quiz-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 4px;
        }
        
        .quiz-body {
            padding: 32px;
            flex-grow: 1; /* Take remaining height */
            overflow-y: auto; /* Internal scroll */
            min-height: 0;
            background: var(--quiz-bg);
        }
        
        .quiz-step {
            display: none;
            animation: fadeInUp 0.4s ease;
        }
        
        .quiz-step.active {
            display: block;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .quiz-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--quiz-border);
            border-radius: 8px;
            background: var(--quiz-input-bg);
            color: var(--quiz-text);
            font-size: 16px;
            transition: border-color 0.3s;
            margin-bottom: 16px;
            box-sizing: border-box;
        }
        
        .quiz-input:focus {
            outline: none;
            border-color: var(--quiz-primary);
        }
        
        .quiz-input.valid {
            border-color: var(--quiz-success);
        }
        
        .quiz-input.invalid {
            border-color: var(--quiz-error);
        }
        
        .quiz-radio-group, .quiz-checkbox-group {
            margin-bottom: 16px;
        }
        
        .quiz-option {
            display: flex;
            align-items: center;
            padding: 16px;
            border: 2px solid var(--quiz-border);
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--quiz-card-bg);
        }
        
        .quiz-option:hover {
            border-color: var(--quiz-primary);
            background: rgba(74, 108, 247, 0.05);
        }
        
        .dark .quiz-option:hover {
            background: rgba(74, 108, 247, 0.1);
        }
        
        .quiz-option input[type="radio"],
        .quiz-option input[type="checkbox"] {
            margin-right: 12px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--quiz-primary);
        }
        
        .quiz-option.selected {
            border-color: var(--quiz-primary);
            background: rgba(74, 108, 247, 0.1);
        }
        
        .quiz-footer {
            padding: 24px;
            border-top: 1px solid var(--quiz-border);
            display: flex;
            justify-content: space-between;
            background: var(--quiz-bg);
            flex-shrink: 0;
        }
        
        .quiz-btn {
            padding: 12px 32px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
        }
        
        .quiz-btn-primary {
            background: var(--quiz-primary);
            color: white;
        }
        
        .quiz-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
            opacity: 0.9;
        }
        
        .quiz-btn-secondary {
            background: transparent;
            color: var(--quiz-text);
            border: 2px solid var(--quiz-border);
        }
        
        .quiz-btn-secondary:hover {
            border-color: var(--quiz-primary);
            color: var(--quiz-primary);
        }
        
        .quiz-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .quiz-summary {
            background: var(--quiz-card-bg);
            border: 2px solid var(--quiz-border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }
        
        .quiz-summary h4 {
            color: var(--quiz-primary);
            margin-top: 0;
            margin-bottom: 12px;
        }
        
        .quiz-summary p {
            color: var(--quiz-text);
            margin: 8px 0;
            line-height: 1.6;
            word-break: break-word;
        }
        
        /* Loading animation */
        @keyframes dot-pulse {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }
        
        .loading-dot {
            animation: dot-pulse 1.4s infinite;
            margin: 0 2px;
        }
        
        .loading-dot:nth-child(2) { animation-delay: 0.2s; }
        .loading-dot:nth-child(3) { animation-delay: 0.4s; }
        
        /* Scrollbar */
        .quiz-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .quiz-body::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .quiz-body::-webkit-scrollbar-thumb {
            background-color: var(--quiz-border);
            border-radius: 3px;
        }

        /* CUSTOM SELECT STYLES */
        .quiz-custom-select-wrapper {
            position: relative;
            user-select: none;
            width: 100%;
            margin-bottom: 16px;
        }

        .quiz-custom-select {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .quiz-custom-select__trigger {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            font-size: 16px;
            color: var(--quiz-text);
            background: var(--quiz-input-bg);
            border: 2px solid var(--quiz-border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .quiz-custom-select__trigger:hover {
            border-color: var(--quiz-primary);
        }

        .quiz-custom-select.open .quiz-custom-select__trigger {
            border-color: var(--quiz-primary);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .quiz-custom-select__options {
            position: absolute;
            display: block;
            top: 100%;
            left: 0;
            right: 0;
            border: 2px solid var(--quiz-primary);
            border-top: 0;
            background: var(--quiz-card-bg);
            transition: all 0.3s;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 10;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .quiz-custom-select.open .quiz-custom-select__options {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        .quiz-custom-option {
            position: relative;
            display: block;
            padding: 12px 16px;
            font-size: 16px;
            color: var(--quiz-text);
            cursor: pointer;
            transition: all 0.2s;
        }

        .quiz-custom-option:hover {
            background: rgba(74, 108, 247, 0.1);
            color: var(--quiz-primary);
        }

        .quiz-custom-option.selected {
            background: var(--quiz-primary);
            color: #ffffff;
        }
        
        .quiz-arrow {
            position: relative;
            height: 10px;
            width: 10px;
        }
        
        .quiz-arrow::after {
            content: ''; 
            position: absolute;
            border: solid var(--quiz-text);
            border-width: 0 2px 2px 0;
            display: inline-block;
            padding: 3px;
            top: -2px;
            right: 0;
            transform: rotate(45deg);
            transition: 0.3s;
        }
        
        .quiz-custom-select.open .quiz-arrow::after {
             transform: rotate(225deg);
             top: 2px;
        }
            
        /* Hide native select but keep it functional for form data */
        select.quiz-input {
            display: none; 
        }

        /* Custom scrollbar for options */
        .quiz-custom-select__options::-webkit-scrollbar {
            width: 6px;
        }
        .quiz-custom-select__options::-webkit-scrollbar-thumb {
            background-color: var(--quiz-border);
            border-radius: 3px;
        }
        /* Step Indicator Styles */
        .quiz-steps-wrapper {
            margin: 20px 0 24px;
            padding: 0 10px;
        }

        .quiz-steps-container {
            display: flex;
            justify-content: space-between;
            position: relative;
            max-width: 400px; /* Adjust as needed */
            margin: 0 auto;
        }

        /* Connecting Line Background */
        .quiz-steps-line-bg {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #E5E7EB; /* Grey line */
            transform: translateY(-50%);
            z-index: 0;
            border-radius: 2px;
        }

        .dark .quiz-steps-line-bg {
            background: #2E3038;
        }

        /* Active Line Fill (Dynamic width) */
        .quiz-steps-line-fill {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background: var(--quiz-primary);
            transform: translateY(-50%);
            z-index: 1;
            border-radius: 2px;
            transition: width 0.4s ease;
        }

        .quiz-step-indicator-item {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #D1D5DB; /* Default Grey */
            color: white;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 0 0 4px var(--quiz-bg); /* Gap around circle for line */
        }

        .dark .quiz-step-indicator-item {
            background: #4B5563;
        }

        /* Active / Completed State */
        .quiz-step-indicator-item.active,
        .quiz-step-indicator-item.completed {
            background: var(--quiz-primary);
            box-shadow: 0 0 0 4px var(--quiz-bg), 0 0 15px rgba(74, 108, 247, 0.5); /* Glowing effect */
            transform: scale(1.1);
        }

        .quiz-step-indicator-item.completed {
            transform: scale(1);
             box-shadow: 0 0 0 4px var(--quiz-bg);
        }

    </style>
    
    <script>
    // Inline JS to Initialize Custom Selects
    document.addEventListener('DOMContentLoaded', function() {
        const initCustomSelects = () => {
            const selects = document.querySelectorAll('select.quiz-input');
            selects.forEach(select => {
                if (select.closest('.quiz-custom-select-wrapper')) return; // Already init

                const wrapper = document.createElement('div');
                wrapper.classList.add('quiz-custom-select-wrapper');
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);

                const customSelect = document.createElement('div');
                customSelect.classList.add('quiz-custom-select');
                
                // Trigger (Selected Value)
                const trigger = document.createElement('div');
                trigger.classList.add('quiz-custom-select__trigger');
                const firstOption = select.options[select.selectedIndex];
                trigger.innerHTML = `<span class="quiz-selection">${firstOption ? firstOption.text : 'Select'}</span><div class="quiz-arrow"></div>`;
                customSelect.appendChild(trigger);
                
                // Options List
                const optionsList = document.createElement('div');
                optionsList.classList.add('quiz-custom-select__options');
                
                Array.from(select.options).forEach(option => {
                    if (option.value === '') return; // Skip placeholder if empty value
                    const customOption = document.createElement('div');
                    customOption.classList.add('quiz-custom-option');
                    customOption.textContent = option.text;
                    customOption.dataset.value = option.value;
                    
                    if (option.selected) {
                        customOption.classList.add('selected');
                    }
                    
                    customOption.addEventListener('click', function() {
                        select.value = this.dataset.value;
                        
                        // Visual update
                        trigger.querySelector('.quiz-selection').textContent = this.textContent;
                        customSelect.classList.remove('open');
                        
                        customSelect.querySelectorAll('.quiz-custom-option').forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');
                        
                        // Trigger change event on native select for validation
                        select.dispatchEvent(new Event('change'));
                        select.dispatchEvent(new Event('input')); // For real-time validation in widget
                    });
                    
                    optionsList.appendChild(customOption);
                });
                
                customSelect.appendChild(optionsList);
                wrapper.appendChild(customSelect);
                
                // Toggle Open
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Close others
                    document.querySelectorAll('.quiz-custom-select').forEach(el => {
                        if (el !== customSelect) el.classList.remove('open');
                    });
                    customSelect.classList.toggle('open');
                });
            });
            
            // Close on click outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.quiz-custom-select')) {
                     document.querySelectorAll('.quiz-custom-select').forEach(el => el.classList.remove('open'));
                }
            });
        };
        
        // Init initially
        initCustomSelects();
        
        // Re-init when modal opens (if needed for dynamic content)
        // Or wait for widget init
    });
    </script>

    <!-- Quiz Modal -->
    <div id="quiz-modal">
        <div class="quiz-container">
            <!-- Header -->
            <div class="quiz-header">
                <button class="quiz-close" id="quiz-close-btn" aria-label="Close">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="quiz-steps-wrapper">
                    <div class="quiz-steps-container">
                        <div class="quiz-steps-line-bg"></div>
                        <div class="quiz-steps-line-fill" id="quiz-steps-line-fill" style="width: 0%;"></div>
                        <?php for ($i = 1; $i <= $total_steps; $i++): ?>
                            <div class="quiz-step-indicator-item <?php echo $i === 1 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="quiz-body" id="quiz-body">
                
                <?php
                $step_count = 0;
                foreach ($steps as $step):
                    $step_count++;
                    ?>
                    <div class="quiz-step <?php echo $step_count === 1 ? 'active' : ''; ?>" data-step="<?php echo $step_count; ?>" data-title="<?php echo esc_attr($step['step_title']); ?>">
                        <h3 style="color: var(--quiz-text); margin-top: 0;"><?php echo esc_html($step['step_title']); ?></h3>
                        <?php if (!empty($step['step_description'])): ?>
                            <p style="color: var(--quiz-text); opacity: 0.7; margin-bottom: 24px;"><?php echo esc_html($step['step_description']); ?></p>
                        <?php endif; ?>
                        
                        <?php 
                        foreach ($step['step_fields'] as $field): 
                            // Helper to parse options - supports both textarea and complex field formats
                            $parse_options = function($options_data) {
                                $options = [];
                                
                                // Check if it's the new complex field format (array of arrays)
                                if (is_array($options_data) && !empty($options_data)) {
                                    // Check if first element has option_value and option_label keys
                                    $first = reset($options_data);
                                    if (is_array($first) && isset($first['option_value']) && isset($first['option_label'])) {
                                        // New complex field format
                                        foreach ($options_data as $option) {
                                            $options[$option['option_value']] = $option['option_label'];
                                        }
                                        return $options;
                                    }
                                }
                                
                                // Old textarea format (string with newlines)
                                if (is_string($options_data)) {
                                    $lines = explode("\n", $options_data);
                                    foreach ($lines as $line) {
                                        $line = trim($line);
                                        if (empty($line)) continue;
                                        
                                        $parts = explode(':', $line, 2);
                                        if (count($parts) === 2) {
                                            $options[trim($parts[0])] = trim($parts[1]);
                                        } else {
                                            // Fallback if no colon
                                            $val = trim($line);
                                            $options[$val] = $val;
                                        }
                                    }
                                }
                                
                                return $options;
                            };
                            
                            $is_required = !empty($field['field_required']) ? 'required' : '';
                            $required_mark = !empty($field['field_required']) ? ' *' : '';
                            $placeholder = !empty($field['field_placeholder']) ? $field['field_placeholder'] : '';
                            $field_name = $field['field_name'];
                            $purpose = !empty($field['field_purpose']) ? $field['field_purpose'] : 'none';
                            
                            // Prevent ID collisions
                            $field_id = 'quiz-field-' . $field_name;
                            
                            ?>
                            
                            <?php if ($field['_type'] === 'text' || $field['_type'] === 'email'): ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <input type="<?php echo $field['_type']; ?>" 
                                       class="quiz-input" 
                                       id="<?php echo esc_attr($field_id); ?>"
                                       name="<?php echo esc_attr($field_name); ?>"
                                       data-label="<?php echo esc_attr($field['field_label']); ?>"
                                       data-purpose="<?php echo esc_attr($purpose); ?>"
                                       placeholder="<?php echo esc_attr($placeholder); ?>" 
                                       <?php echo $is_required; ?>>
                                       
                            <?php elseif ($field['_type'] === 'textarea'): ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <textarea class="quiz-input" 
                                          id="<?php echo esc_attr($field_id); ?>"
                                          name="<?php echo esc_attr($field_name); ?>"
                                          data-label="<?php echo esc_attr($field['field_label']); ?>"
                                          data-purpose="<?php echo esc_attr($purpose); ?>"
                                          rows="<?php echo esc_attr(!empty($field['field_rows']) ? $field['field_rows'] : 4); ?>" 
                                          placeholder="<?php echo esc_attr($placeholder); ?>" 
                                          <?php echo $is_required; ?>></textarea>

                            <?php elseif ($field['_type'] === 'radio'): ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <div class="quiz-radio-group" data-name="<?php echo esc_attr($field_name); ?>" <?php echo $is_required ? 'data-required="true"' : ''; ?> data-purpose="<?php echo esc_attr($purpose); ?>">
                                    <?php 
                                    $options = $parse_options($field['field_options']);
                                    foreach ($options as $val => $label): 
                                    ?>
                                        <label class="quiz-option">
                                            <input type="radio" 
                                                   name="<?php echo esc_attr($field_name); ?>" 
                                                   value="<?php echo esc_attr($val); ?>"
                                                   data-label="<?php echo esc_attr($label); ?>">
                                            <span style="color: var(--quiz-text);"><?php echo esc_html($label); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif ($field['_type'] === 'checkbox'): ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <div class="quiz-checkbox-group" data-name="<?php echo esc_attr($field_name); ?>" <?php echo $is_required ? 'data-required="true"' : ''; ?> data-purpose="<?php echo esc_attr($purpose); ?>">
                                    <?php 
                                    $options = $parse_options($field['field_options']);
                                    foreach ($options as $val => $label): 
                                    ?>
                                        <label class="quiz-option">
                                            <input type="checkbox" 
                                                   name="<?php echo esc_attr($field_name); ?>[]" 
                                                   value="<?php echo esc_attr($val); ?>"
                                                   data-label="<?php echo esc_attr($label); ?>">
                                            <span style="color: var(--quiz-text);"><?php echo esc_html($label); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif ($field['_type'] === 'select'): ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <select class="quiz-input" 
                                        id="<?php echo esc_attr($field_id); ?>"
                                        name="<?php echo esc_attr($field_name); ?>" 
                                        data-label="<?php echo esc_attr($field['field_label']); ?>"
                                        data-purpose="<?php echo esc_attr($purpose); ?>"
                                        <?php echo $is_required; ?>>
                                    <option value="">-- <?php echo esc_html($placeholder ?: 'Select'); ?> --</option>
                                    <?php 
                                    $options = $parse_options($field['field_options']);
                                    foreach ($options as $val => $label): 
                                    ?>
                                        <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                            <?php elseif ($field['_type'] === 'info'): ?>
                                <div style="margin-bottom: 16px; color: var(--quiz-text);">
                                    <?php echo wpautop($field['field_content']); ?>
                                </div>
                            <?php endif; ?>
                            
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Summary Step (Last) -->
                <div class="quiz-step" data-step="<?php echo $total_steps; ?>" data-title="Summary">
                    <h3 style="color: var(--quiz-text); margin-top: 0;">Summary</h3>
                    <p style="color: var(--quiz-text); opacity: 0.7; margin-bottom: 24px;">Review your answers before submitting</p>
                    
                    <div id="quiz-summary-content">
                        <!-- Summary will be generated here -->
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="quiz-footer">
                <button class="quiz-btn quiz-btn-secondary" id="quiz-prev-btn" style="display: none;">Back</button>
                <button class="quiz-btn quiz-btn-primary" id="quiz-next-btn">Next</button>
                <button class="quiz-btn quiz-btn-primary" id="quiz-submit-btn" style="display: none;">Submit</button>
            </div>
        </div>
    </div>
    <script>
        // Pass total steps to JS
        window.QUIZ_TOTAL_STEPS = <?php echo $total_steps; ?>;
    </script>
    <?php
}
add_action('wp_footer', 'render_quiz_modal_html');
