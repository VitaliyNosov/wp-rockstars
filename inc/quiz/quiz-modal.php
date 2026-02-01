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
    
    // Get custom accent color
    $accent_color = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_accent_color') : '';
    if (empty($accent_color)) $accent_color = '#4A6CF7';

    // Get Typography & Text
    $font_family = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_font_family') : '';
    
    // Handle Custom Font
    if ($font_family === 'custom') {
        $custom_font_name = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_custom_font_name') : '';
        $custom_font_url = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_custom_font_url') : '';
        
        if (!empty($custom_font_url)) {
            echo '<link rel="stylesheet" href="' . esc_url($custom_font_url) . '">';
        }
        
        if (!empty($custom_font_name)) {
            // Ensure font name is quoted if not already
            $font_family = (strpos($custom_font_name, "'") === false && strpos($custom_font_name, '"') === false) 
                ? "'" . $custom_font_name . "', sans-serif" 
                : $custom_font_name;
        } else {
            $font_family = ''; // Fallback
        }
    }
    
    $btn_prev = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_btn_prev') : 'Back';
    if (empty($btn_prev)) $btn_prev = 'Back';

    $btn_next = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_btn_next') : 'Next';
    if (empty($btn_next)) $btn_next = 'Next';

    $btn_submit = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_btn_submit') : 'Submit';
    if (empty($btn_submit)) $btn_submit = 'Submit';
    ?>
    
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
                    <div class="quiz-steps-container" id="quiz-steps-container">
                        <div class="quiz-steps-track">
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
                                               data-max-size="<?php echo esc_attr(!empty($field['field_max_size']) ? $field['field_max_size'] : '2'); ?>"
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
                <button class="quiz-btn quiz-btn-secondary" id="quiz-prev-btn" style="display: none;"><?php echo esc_html($btn_prev); ?></button>
                <button class="quiz-btn quiz-btn-primary" id="quiz-next-btn"><?php echo esc_html($btn_next); ?></button>
                <button class="quiz-btn quiz-btn-primary" id="quiz-submit-btn" style="display: none;" data-original-text="<?php echo esc_attr($btn_submit); ?>"><?php echo esc_html($btn_submit); ?></button>
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
