import React, { useState, useEffect, useRef } from 'react';
import { useQuiz } from './QuizContext';
import { sanitizeId, applyPhoneMask } from '../../lib/quiz-utils';
import { QuizIcons } from './QuizIcons';

/**
 * Quiz Step Container
 */
const QuizStep = ({ step, index }) => {
    return (
        <div className="quiz-step active" data-step={index}>
            <h3 style={{ color: 'var(--quiz-text)', marginTop: 0 }}>{step.title}</h3>
            {step.description && (
                <p style={{ color: 'var(--quiz-text)', opacity: 0.7, marginBottom: '24px' }}>
                    {step.description}
                </p>
            )}



            {step.fields?.map((field, idx) => (
                <QuizField key={`${field.name}-${idx}`} field={field} />
            ))}
        </div>
    );
};

/**
 * Field Dispatcher
 */
const QuizField = ({ field }) => {
    const { type } = field;

    switch (type) {
        case 'text':
        case 'email':
            return <TextField field={field} />;
        case 'textarea':
            return <TextareaField field={field} />;
        case 'radio':
        case 'checkbox':
            return <OptionField field={field} />;
        case 'select':
            return <SelectField field={field} />;
        case 'range':
            return <RangeField field={field} />;
        case 'file':
            return <FileField field={field} />;
        case 'phone':
            return <PhoneField field={field} />;
        case 'switch':
            return <SwitchField field={field} />;
        case 'date':
            return <DateField field={field} />;
        case 'info':
            return <InfoField field={field} />;
        default:
            return null;
    }
}

/**
 * Text & Email Field
 */
const TextField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const value = answers[field.name] || '';
    const id = sanitizeId(field.name);

    return (
        <div className="quiz-field-wrapper">
            <label className="quiz-label" htmlFor={id} style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <input
                type={field.type}
                id={id}
                name={field.name}
                className="quiz-input"
                placeholder={field.placeholder}
                value={value}
                onChange={(e) => updateAnswer(field.name, e.target.value)}
                required={field.required}
            />
        </div>
    );
};

/**
 * Textarea Field
 */
const TextareaField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const value = answers[field.name] || '';
    const id = sanitizeId(field.name);

    return (
        <div className="quiz-field-wrapper">
            <label className="quiz-label" htmlFor={id} style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <textarea
                id={id}
                name={field.name}
                className="quiz-input"
                placeholder={field.placeholder}
                rows={field.rows || 4}
                value={value}
                onChange={(e) => updateAnswer(field.name, e.target.value)}
                required={field.required}
            />
        </div>
    );
};

/**
 * Radio & Checkbox Field
 */
const OptionField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const isCheckbox = field.type === 'checkbox';
    const currentValue = answers[field.name] || (isCheckbox ? [] : '');

    const handleChange = (e) => {
        const val = e.target.value;
        if (isCheckbox) {
            const newVals = e.target.checked
                ? [...currentValue, val]
                : currentValue.filter(v => v !== val);
            updateAnswer(field.name, newVals);
        } else {
            updateAnswer(field.name, val);
        }
    };

    const isSelected = (val) => {
        return isCheckbox ? currentValue.includes(val) : currentValue === val;
    };

    return (
        <div className="quiz-field-wrapper">
            <label className="quiz-label" style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <div className={field.layout === 'tiles' ? 'quiz-options-grid' : (isCheckbox ? 'quiz-checkbox-group' : 'quiz-radio-group')}>
                {field.options?.map((opt, idx) => (
                    <label
                        key={idx}
                        className={`${field.layout === 'tiles' ? 'quiz-tile-option' : 'quiz-option'} ${isSelected(opt.value) ? 'selected' : ''}`}
                    >
                        <input
                            type={field.type}
                            name={field.name}
                            value={opt.value}
                            checked={isSelected(opt.value)}
                            onChange={handleChange}
                            style={field.layout === 'tiles' ? { position: 'absolute', opacity: 0, width: 0, height: 0 } : { marginRight: '12px' }}
                        />
                        {field.layout === 'tiles' && (
                            <>
                                {opt.image ? (
                                    <img src={opt.image} className="quiz-tile-image" alt="" />
                                ) : (opt.icon && QuizIcons[opt.icon]) ? (
                                    <div className="quiz-tile-icon">
                                        {QuizIcons[opt.icon]}
                                    </div>
                                ) : null}
                                <span className="quiz-tile-label">{opt.label}</span>
                            </>
                        )}
                        {field.layout !== 'tiles' && <span style={{ color: 'var(--quiz-text)' }}>{opt.label}</span>}
                    </label>
                ))}
            </div>
        </div>
    );
};

/**
 * Custom Select Field
 */
const SelectField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const [isOpen, setIsOpen] = useState(false);
    const value = answers[field.name] || '';
    const selectedOption = field.options?.find(opt => opt.value === value);

    return (
        <div className="quiz-field-wrapper">
            <label className="quiz-label" style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <div className="quiz-custom-select-wrapper" onClick={() => setIsOpen(!isOpen)}>
                <div className={`quiz-custom-select ${isOpen ? 'open' : ''}`}>
                    <div className="quiz-custom-select__trigger">
                        <span className="quiz-selection">
                            {selectedOption ? selectedOption.label : (field.placeholder || 'Select option...')}
                        </span>
                        <div className="quiz-arrow"></div>
                    </div>
                    <div className="quiz-custom-select__options">
                        {field.options?.map((opt, idx) => (
                            <div
                                key={idx}
                                className={`quiz-custom-option ${value === opt.value ? 'selected' : ''}`}
                                onClick={(e) => {
                                    e.stopPropagation();
                                    updateAnswer(field.name, opt.value);
                                    setIsOpen(false);
                                }}
                            >
                                {opt.label}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

/**
 * Range Slider Field
 */
const RangeField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const value = answers[field.name] || field.defaultValue || 0;

    const min = parseFloat(field.min) || 0;
    const max = parseFloat(field.max) || 100;
    const percentage = ((value - min) / (max - min)) * 100;

    return (
        <div className="quiz-range-wrapper">
            <div className="quiz-range-header">
                <label className="quiz-label" style={{ marginBottom: 0, color: 'var(--quiz-text)', fontWeight: 600 }}>
                    {field.label}{field.required ? ' *' : ''}
                </label>
                <div className="quiz-range-value-display">
                    {field.prefix}{value}{field.suffix}
                </div>
            </div>
            <input
                type="range"
                className="quiz-range-input"
                min={field.min}
                max={field.max}
                step={field.step}
                value={value}
                onChange={(e) => updateAnswer(field.name, e.target.value)}
                style={{
                    background: `linear-gradient(to right, var(--quiz-primary) 0%, var(--quiz-primary) ${percentage}%, var(--quiz-border) ${percentage}%, var(--quiz-border) 100%)`
                }}
            />
            <div className="quiz-range-limits">
                <span>{field.prefix}{field.min}{field.suffix}</span>
                <span>{field.prefix}{field.max}{field.suffix}</span>
            </div>
        </div>
    );
};

/**
 * File Upload Field
 */
const FileField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const file = answers[field.name];

    return (
        <div className="quiz-file-wrapper">
            <label className="quiz-label" style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <div className={`quiz-file-container ${file ? 'has-file' : ''}`}>
                <div className="quiz-file-btn">Choose file</div>
                <div className="quiz-file-name">
                    {file ? file.name : (field.placeholder || 'No file selected')}
                </div>
                <input
                    type="file"
                    className="quiz-file-input"
                    onChange={(e) => {
                        const file = e.target.files[0];
                        if (file) {
                            updateAnswer(field.name, {
                                _isQuizFile: true,
                                name: file.name,
                                file: file
                            });
                        }
                    }}
                />
            </div>
            {field.fileTypes && (
                <span className="quiz-file-info">Allowed: {field.fileTypes}</span>
            )}
        </div>
    );
};

/**
 * Phone Field (Masked)
 */
const PhoneField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const value = answers[field.name] || '';

    const handleInput = (e) => {
        const masked = applyPhoneMask(e.target.value, field.mask);
        updateAnswer(field.name, masked);
    };

    return (
        <div className="quiz-field-wrapper">
            <label className="quiz-label" style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <input
                type="tel"
                className="quiz-input"
                placeholder={field.placeholder || '+7 (___) ___-__-__'}
                value={value}
                onInput={handleInput}
            />
        </div>
    );
};

/**
 * Switch Field
 */
const SwitchField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const checked = answers[field.name] === undefined ? !!field.defaultState : !!answers[field.name];

    return (
        <div className="quiz-switch-wrapper">
            <span className="quiz-switch-label">{field.label}</span>
            <div style={{ display: 'flex', alignItems: 'center' }}>
                <label className="quiz-switch-container">
                    <input
                        type="checkbox"
                        checked={checked}
                        onChange={(e) => updateAnswer(field.name, e.target.checked)}
                    />
                    <span className="quiz-switch-slider"></span>
                </label>
                <span className="quiz-switch-status">
                    {checked ? (field.onLabel || 'Yes') : (field.offLabel || 'No')}
                </span>
            </div>
        </div>
    );
};

/**
 * Date Field
 */
const DateField = ({ field }) => {
    const { answers, updateAnswer } = useQuiz();
    const value = answers[field.name] || '';
    const inputRef = useRef(null);

    useEffect(() => {
        if (typeof window !== 'undefined' && window.flatpickr && inputRef.current) {
            const instance = window.flatpickr(inputRef.current, {
                locale: 'ru',
                dateFormat: 'd.m.Y',
                allowInput: true,
                disableMobile: "true",
                defaultDate: value || null,
                onChange: (selectedDates, dateStr) => {
                    updateAnswer(field.name, dateStr);
                },
                onOpen: (selectedDates, dateStr, instance) => {
                    instance.calendarContainer.style.zIndex = "2147483647";
                }
            });

            return () => {
                instance.destroy();
            };
        }
    }, [field.name, updateAnswer]);

    return (
        <div className="quiz-date-wrapper" style={{ position: 'relative' }}>
            <label className="quiz-label" style={{ display: 'block', color: 'var(--quiz-text)', marginBottom: '8px', fontWeight: 600 }}>
                {field.label}{field.required ? ' *' : ''}
            </label>
            <input
                ref={inputRef}
                type="text"
                className="quiz-input"
                placeholder={field.placeholder || 'Select date'}
                defaultValue={value}
                autoComplete="off"
            />
            <div style={{
                position: 'absolute',
                right: '15px',
                top: '47px',
                width: '20px',
                height: '20px',
                pointerEvents: 'none',
                opacity: 0.8,
                color: 'var(--quiz-primary)'
            }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
        </div>
    );
};

/**
 * Info Field
 */
const InfoField = ({ field }) => {
    return (
        <div
            style={{ marginBottom: '16px', color: 'var(--quiz-text)' }}
            dangerouslySetInnerHTML={{ __html: field.content }}
        />
    );
};

export default QuizStep;
