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
        
        /* Remove validation borders for range inputs */
        input[type="range"].quiz-input {
            border: none !important;
            padding: 0;
            background: transparent;
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

        /* Range Slider Styles */
        .quiz-range-wrapper {
            margin-bottom: 24px;
            padding: 10px 0;
        }

        .quiz-range-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .quiz-range-value-display {
            font-size: 20px;
            font-weight: 700;
            color: var(--quiz-primary);
            background: rgba(74, 108, 247, 0.1);
            padding: 4px 12px;
            border-radius: 6px;
        }

        .quiz-range-input {
            width: 100%;
            height: 6px;
            -webkit-appearance: none;
            background: linear-gradient(to right, var(--quiz-primary) 0%, var(--quiz-primary) 50%, var(--quiz-border) 50%, var(--quiz-border) 100%);
            border-radius: 10px;
            outline: none;
            cursor: pointer;
            margin: 15px 0;
        }

        .quiz-range-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 28px;
            height: 28px;
            background: var(--quiz-primary);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(74, 108, 247, 0.4);
            border: 4px solid var(--quiz-bg);
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-top: -11px; /* Center thumb on track */
        }

        .quiz-range-input::-webkit-slider-runnable-track {
            width: 100%;
            height: 6px;
            cursor: pointer;
            border-radius: 10px;
        }

        .quiz-range-input:hover::-webkit-slider-thumb {
            transform: scale(1.15);
            box-shadow: 0 6px 15px rgba(74, 108, 247, 0.5);
        }

        .quiz-range-limits {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 14px;
            color: var(--quiz-text);
            opacity: 0.6;
        }

        .quiz-range-limits {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 14px;
            color: var(--quiz-text);
            opacity: 0.6;
        }

        /* Tile Selection Styles */
        .quiz-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .quiz-tile-option {
            position: relative;
            background: var(--quiz-card-bg);
            border: 2px solid var(--quiz-border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }

        .quiz-tile-option:hover {
            border-color: var(--quiz-primary);
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .quiz-tile-option.selected {
            border-color: var(--quiz-primary);
            background: rgba(74, 108, 247, 0.05);
            box-shadow: 0 0 0 1px var(--quiz-primary);
        }

        .quiz-tile-icon {
            margin-bottom: 12px;
            color: var(--quiz-primary);
            transition: transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quiz-tile-icon svg {
            width: 32px !important;
            height: 32px !important;
            stroke-width: 2px;
            color: inherit;
        }

        .quiz-tile-option:hover .quiz-tile-icon {
            transform: scale(1.1);
        }

        .quiz-tile-image {
            width: 100%;
            height: 80px;
            object-fit: contain;
            margin-bottom: 12px;
            border-radius: 6px;
        }

        .quiz-tile-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--quiz-text);
            line-height: 1.2;
        }

        .quiz-tile-option input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* File Upload Styles */
        .quiz-file-wrapper {
            margin-bottom: 24px;
        }
        .quiz-file-container {
            position: relative;
            width: 100%;
            height: 56px;
            background: var(--quiz-card-bg);
            border: 2px dashed var(--quiz-border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .quiz-file-container:hover {
            border-color: var(--quiz-primary);
            background: rgba(74, 108, 247, 0.02);
        }
        .quiz-file-container.has-file {
            border-style: solid;
            border-color: var(--quiz-primary);
            background: rgba(74, 108, 247, 0.05);
        }
        .quiz-file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .quiz-file-btn {
            background: var(--quiz-primary);
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            margin-right: 12px;
            pointer-events: none;
        }
        .quiz-file-name {
            color: var(--quiz-text);
            font-size: 14px;
            opacity: 0.7;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            pointer-events: none;
        }
        .quiz-file-container.has-file .quiz-file-name {
            opacity: 1;
            font-weight: 500;
        }
        .quiz-file-info {
            font-size: 11px;
            color: var(--quiz-text);
            opacity: 0.5;
            margin-top: 6px;
            display: block;
        }

        /* Toggle Switch Styles */
        .quiz-switch-wrapper {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: var(--quiz-card-bg);
            border-radius: 12px;
            border: 1px solid var(--quiz-border);
        }
        .quiz-switch-label {
            font-weight: 600;
            color: var(--quiz-text);
            font-size: 16px;
        }
        .quiz-switch-container {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }
        .quiz-switch-container input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .quiz-switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--quiz-border);
            transition: .4s;
            border-radius: 28px;
        }
        .quiz-switch-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        input:checked + .quiz-switch-slider {
            background-color: var(--quiz-primary);
        }
        input:focus + .quiz-switch-slider {
            box-shadow: 0 0 1px var(--quiz-primary);
        }
        input:checked + .quiz-switch-slider:before {
            transform: translateX(22px);
        }
        .quiz-switch-status {
            font-size: 13px;
            font-weight: 600;
            margin-left: 10px;
            color: var(--quiz-text);
            opacity: 0.7;
            min-width: 30px;
        }

        /* Date Picker Styles */
        input[type="date"].quiz-input {
            position: relative;
            appearance: none;
            -webkit-appearance: none;
            min-height: 50px;
        }
        input[type="date"].quiz-input::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
            z-index: 1;
        }
        .quiz-date-wrapper {
            position: relative;
            margin-bottom: 24px;
        }
        .quiz-date-wrapper:after {
            content: "";
            position: absolute;
            right: 15px;
            top: 42px;
            width: 20px;
            height: 20px;
            pointer-events: none;
            background-color: var(--quiz-primary);
            -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E") no-repeat center;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E") no-repeat center;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        .quiz-date-wrapper:focus-within:after {
            opacity: 1;
        }
        /* Custom Flatpickr Theme Adjustments */
        .flatpickr-calendar {
            background: var(--quiz-bg) !important;
            border: 1px solid var(--quiz-border) !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        }
        .flatpickr-day.selected {
            background: var(--quiz-primary) !important;
            border-color: var(--quiz-primary) !important;
        }
        .flatpickr-months .flatpickr-month, 
        .flatpickr-weekdays,
        .flatpickr-day {
            color: var(--quiz-text) !important;
        }
        .flatpickr-innerContainer, .flatpickr-rContainer {
            background: transparent !important;
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
                                if (empty($options_data)) return $options;

                                // Handle complex field format (array of arrays)
                                if (is_array($options_data)) {
                                    foreach ($options_data as $option) {
                                        if (is_array($option)) {
                                            $val = isset($option['option_value']) ? $option['option_value'] : '';
                                            $label = isset($option['option_label']) ? $option['option_label'] : $val;
                                            
                                            $options[$val] = [
                                                'label' => $label,
                                                'icon'  => isset($option['option_icon']) ? $option['option_icon'] : '',
                                                'image' => isset($option['option_image']) ? $option['option_image'] : '',
                                            ];
                                        }
                                    }
                                    if (!empty($options)) return $options;
                                }
                                
                                // Old textarea format fallback
                                if (is_string($options_data)) {
                                    $lines = explode("\n", $options_data);
                                    foreach ($lines as $line) {
                                        $line = trim($line);
                                        if (empty($line)) continue;
                                        $parts = explode(':', $line, 2);
                                        if (count($parts) === 2) {
                                            $options[trim($parts[0])] = ['label' => trim($parts[1]), 'icon' => '', 'image' => ''];
                                        } else {
                                            $options[trim($line)] = ['label' => trim($line), 'icon' => '', 'image' => ''];
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

                            <?php elseif ($field['_type'] === 'radio' || $field['_type'] === 'checkbox'): 
                                $is_checkbox = ($field['_type'] === 'checkbox');
                                $layout = isset($field['field_layout']) ? $field['field_layout'] : 'list';
                                $options = $parse_options($field['field_options']);
                            ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <div class="<?php echo $layout === 'tiles' ? 'quiz-options-grid' : ($is_checkbox ? 'quiz-checkbox-group' : 'quiz-radio-group'); ?>" 
                                     data-name="<?php echo esc_attr($field_name); ?>" 
                                     <?php echo $is_required ? 'data-required="true"' : ''; ?> 
                                     data-purpose="<?php echo esc_attr($purpose); ?>">
                                    <?php foreach ($options as $val => $opt_data): ?>
                                        <?php if ($layout === 'tiles'): ?>
                                            <label class="quiz-tile-option <?php echo $is_checkbox ? 'quiz-tile-checkbox' : 'quiz-tile-radio'; ?>">
                                                <input type="<?php echo $is_checkbox ? 'checkbox' : 'radio'; ?>" 
                                                       name="<?php echo esc_attr($is_checkbox ? $field_name . '[]' : $field_name); ?>" 
                                                       value="<?php echo esc_attr($val); ?>"
                                                       data-label="<?php echo esc_attr($opt_data['label']); ?>">
                                                
                                                <?php if (!empty($opt_data['image'])): ?>
                                                    <img src="<?php echo esc_url($opt_data['image']); ?>" class="quiz-tile-image" alt="">
                                                <?php elseif (!empty($opt_data['icon'])): ?>
                                                    <div class="quiz-tile-icon">
                                                        <i data-lucide="<?php echo esc_attr($opt_data['icon']); ?>" style="width: 32px; height: 32px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <span class="quiz-tile-label"><?php echo esc_html($opt_data['label']); ?></span>
                                            </label>
                                        <?php else: ?>
                                            <label class="quiz-option">
                                                <input type="<?php echo $is_checkbox ? 'checkbox' : 'radio'; ?>" 
                                                       name="<?php echo esc_attr($is_checkbox ? $field_name . '[]' : $field_name); ?>" 
                                                       value="<?php echo esc_attr($val); ?>"
                                                       data-label="<?php echo esc_attr($opt_data['label']); ?>">
                                                <span style="color: var(--quiz-text);"><?php echo esc_html($opt_data['label']); ?></span>
                                            </label>
                                        <?php endif; ?>
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
                                    <option value=""><?php echo esc_html($placeholder ? $placeholder : 'Select option...'); ?></option>
                                    <?php 
                                    $options = $parse_options($field['field_options']);
                                    foreach ($options as $val => $opt_data): 
                                    ?>
                                        <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($opt_data['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>

                            <?php elseif ($field['_type'] === 'range'): ?>
                                <div class="quiz-range-wrapper">
                                    <div class="quiz-range-header">
                                        <label style="display: block; color: var(--quiz-text); margin-bottom: 0; font-weight: 600;">
                                            <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                        </label>
                                        <div class="quiz-range-value-display" id="<?php echo esc_attr($field_id); ?>-display">
                                            <?php echo esc_html($field['field_prefix'] . $field['field_default'] . $field['field_suffix']); ?>
                                        </div>
                                    </div>
                                    <input type="range" 
                                           class="quiz-range-input quiz-input" 
                                           id="<?php echo esc_attr($field_id); ?>"
                                           name="<?php echo esc_attr($field_name); ?>"
                                           data-label="<?php echo esc_attr($field['field_label']); ?>"
                                           min="<?php echo esc_attr($field['field_min']); ?>"
                                           max="<?php echo esc_attr($field['field_max']); ?>"
                                           step="<?php echo esc_attr($field['field_step']); ?>"
                                           value="<?php echo esc_attr($field['field_default']); ?>"
                                           data-prefix="<?php echo esc_attr($field['field_prefix']); ?>"
                                           data-suffix="<?php echo esc_attr($field['field_suffix']); ?>"
                                           <?php echo $is_required; ?>>
                                    <div class="quiz-range-limits">
                                        <span><?php echo esc_html($field['field_prefix'] . $field['field_min'] . $field['field_suffix']); ?></span>
                                        <span><?php echo esc_html($field['field_prefix'] . $field['field_max'] . $field['field_suffix']); ?></span>
                                    </div>
                                </div>

                            <?php elseif ($field['_type'] === 'file'): ?>
                                <div class="quiz-file-wrapper">
                                    <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                        <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                    </label>
                                    <div class="quiz-file-container" id="file-container-<?php echo esc_attr($field_id); ?>">
                                        <div class="quiz-file-btn">Choose file</div>
                                        <div class="quiz-file-name"><?php echo esc_html($placeholder ? $placeholder : 'No file selected'); ?></div>
                                        <input type="file" 
                                               class="quiz-file-input quiz-input" 
                                               id="<?php echo esc_attr($field_id); ?>"
                                               name="<?php echo esc_attr($field_name); ?>"
                                               data-label="<?php echo esc_attr($field['field_label']); ?>"
                                               data-allowed="<?php echo esc_attr($field['field_file_types']); ?>"
                                               <?php echo $is_required; ?>>
                                    </div>
                                    <?php if (!empty($field['field_file_types'])): ?>
                                        <span class="quiz-file-info">Allowed: <?php echo esc_html($field['field_file_types']); ?></span>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ($field['_type'] === 'phone'): ?>
                                <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                    <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                </label>
                                <input type="text" 
                                       class="quiz-input quiz-phone-input" 
                                       id="<?php echo esc_attr($field_id); ?>"
                                       name="<?php echo esc_attr($field_name); ?>" 
                                       placeholder="<?php echo esc_attr($placeholder ?: '+7 (___) ___-__-__'); ?>"
                                       data-label="<?php echo esc_attr($field['field_label']); ?>"
                                       data-mask="<?php echo esc_attr($field['field_mask'] ?: '+7 (999) 999-99-99'); ?>"
                                       inputmode="tel"
                                       <?php echo $is_required; ?>>

                            <?php elseif ($field['_type'] === 'switch'): ?>
                                <div class="quiz-switch-wrapper">
                                    <span class="quiz-switch-label"><?php echo esc_html($field['field_label']); ?></span>
                                    <div style="display: flex; align-items: center;">
                                        <label class="quiz-switch-container">
                                            <input type="checkbox" 
                                                   class="quiz-input quiz-switch-input" 
                                                   id="<?php echo esc_attr($field_id); ?>"
                                                   name="<?php echo esc_attr($field_name); ?>"
                                                   data-label="<?php echo esc_attr($field['field_label']); ?>"
                                                   data-on="<?php echo esc_attr($field['field_on_label'] ?: 'Yes'); ?>"
                                                   data-off="<?php echo esc_attr($field['field_off_label'] ?: 'No'); ?>"
                                                   <?php echo $field['field_default_state'] ? 'checked' : ''; ?>>
                                            <span class="quiz-switch-slider"></span>
                                        </label>
                                        <span class="quiz-switch-status">
                                            <?php echo $field['field_default_state'] ? esc_html($field['field_on_label'] ?: 'Yes') : esc_html($field['field_off_label'] ?: 'No'); ?>
                                        </span>
                                    </div>
                                </div>

                            <?php elseif ($field['_type'] === 'date'): ?>
                                <div class="quiz-date-wrapper">
                                    <label style="display: block; color: var(--quiz-text); margin-bottom: 8px; font-weight: 600;">
                                        <?php echo esc_html($field['field_label'] . $required_mark); ?>
                                    </label>
                                    <input type="text" 
                                           class="quiz-input quiz-datepicker" 
                                           id="<?php echo esc_attr($field_id); ?>"
                                           name="<?php echo esc_attr($field_name); ?>" 
                                           placeholder="<?php echo esc_attr($placeholder ?: 'Select date'); ?>"
                                           data-label="<?php echo esc_attr($field['field_label']); ?>"
                                           autocomplete="off"
                                           <?php echo $is_required; ?>>
                                </div>

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
