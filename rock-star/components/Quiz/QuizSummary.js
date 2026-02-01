import React from 'react';
import { useQuiz } from './QuizContext';
import { escapeHtml } from '../../lib/quiz-utils';

const QuizSummary = () => {
    const { settings, answers } = useQuiz();

    if (!settings?.steps) return null;

    return (
        <div className="quiz-summary-content">
            <h3 style={{ color: 'var(--quiz-text)', marginTop: 0 }}>Summary</h3>
            <p style={{ color: 'var(--quiz-text)', opacity: 0.7, marginBottom: '24px' }}>
                Review your answers before submitting
            </p>

            {settings.steps.map((step, sIdx) => {
                // Only show step if it has fields with answers
                const stepHasAnswers = step.fields?.some(field => {
                    const val = answers[field.name];
                    return val !== undefined && val !== '' && (Array.isArray(val) ? val.length > 0 : true);
                });

                if (!stepHasAnswers) return null;

                return (
                    <div
                        key={sIdx}
                        className="quiz-summary-group"
                        style={{
                            marginBottom: '16px',
                            border: '1px solid var(--quiz-border)',
                            borderRadius: '12px',
                            padding: '16px 20px',
                            background: 'var(--quiz-card-bg)',
                            transition: 'border-color 0.3s'
                        }}
                    >
                        <h4 style={{
                            color: 'var(--quiz-primary)',
                            margin: '0 0 12px 0',
                            fontSize: '18px',
                            fontWeight: '600'
                        }}>
                            {step.title}
                        </h4>
                        {step.fields?.map((field, fIdx) => {
                            const val = answers[field.name];
                            if (val === undefined || val === '' || (Array.isArray(val) && val.length === 0)) return null;

                            let displayValue = val;

                            // Format based on type
                            if (field.type === 'select') {
                                const opt = field.options?.find(o => o.value === val);
                                if (opt) displayValue = opt.label;
                            } else if (field.type === 'radio') {
                                const opt = field.options?.find(o => o.value === val);
                                if (opt) displayValue = opt.label;
                            } else if (field.type === 'checkbox') {
                                displayValue = val.map(v => {
                                    const opt = field.options?.find(o => o.value === v);
                                    return opt ? opt.label : v;
                                }).join(', ');
                            } else if (field.type === 'range') {
                                displayValue = `${field.prefix || ''}${val}${field.suffix || ''}`;
                            } else if (field.type === 'file') {
                                displayValue = (val && val._isQuizFile) ? val.name : (val?.name || 'File selected');
                            } else if (field.type === 'switch') {
                                displayValue = val ? (field.onLabel || 'Yes') : (field.offLabel || 'No');
                            }

                            return (
                                <p key={fIdx} style={{ margin: '8px 0', color: 'var(--quiz-text)', lineHeight: '1.4' }}>
                                    <strong style={{ opacity: 0.9 }}>{field.label}:</strong> <span style={{ opacity: 0.8 }}>{displayValue}</span>
                                </p>
                            );
                        })}
                    </div>
                );
            })}
        </div>
    );
};

export default QuizSummary;
