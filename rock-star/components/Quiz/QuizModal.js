import React, { useEffect, useRef } from 'react';
import { useQuiz } from './QuizContext';
import { useMutation } from '@apollo/client';
import { SUBMIT_QUIZ } from '../../lib/quiz';
import { isValidEmail } from '../../lib/quiz-utils';
import QuizProgress from './QuizProgress';
import QuizStep from './QuizStep';
import QuizSummary from './QuizSummary';

const QuizModal = () => {
    const {
        isOpen,
        closeQuiz,
        settings,
        currentStep,
        totalSteps,
        prevStep,
        nextStep,
        isSubmitting,
        setIsSubmitting,
        answers,
        setError,
        setSuccess,
        resetQuiz,
        success,
        error
    } = useQuiz();

    const [submitQuizMutation] = useMutation(SUBMIT_QUIZ);
    const modalRef = useRef(null);

    /**
     * Field Validation Logic
     */
    const validateCurrentStep = () => {
        const currentStepFields = settings?.steps[currentStep - 1]?.fields;
        if (!currentStepFields) return true;

        let isValid = true;
        for (const field of currentStepFields) {
            if (field.required) {
                const value = answers[field.name];
                if (value === undefined || value === '' || (Array.isArray(value) && value.length === 0)) {
                    isValid = false;
                    break;
                }

                if (field.type === 'email' || field.purpose === 'email') {
                    if (!isValidEmail(value)) {
                        isValid = false;
                        break;
                    }
                }

                if (field.type === 'phone' && value.includes('_')) {
                    isValid = false;
                    break;
                }
            }
        }

        if (!isValid) {
            alert('Please fill in all required fields correctly.');
        }
        return isValid;
    };

    const handleNext = () => {
        if (validateCurrentStep()) {
            nextStep();
        }
    };

    /**
     * Submission logic
     */
    const handleSubmit = async () => {
        setIsSubmitting(true);
        setError(null);

        try {
            // Prepare form data
            const formData = [];

            // Helper to convert File to Base64 if needed
            const toBase64 = (file) => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    const base64 = reader.result;
                    const formattedValue = base64.replace(/^data:[^;]+;/, `data:${file.type};name:${file.name};`);
                    resolve(formattedValue);
                };
                reader.onerror = error => reject(error);
            });

            const answerEntries = Object.entries(answers);
            for (const [name, value] of answerEntries) {
                let submitValue = '';
                console.log(`Quiz Processing field "${name}":`, typeof value, value);

                if (Array.isArray(value)) {
                    submitValue = value.join(', ');
                } else if (value && typeof value === 'object' && value._isQuizFile && value.file) {
                    console.log(`- Detected structured file object for "${name}"`);
                    try {
                        submitValue = await toBase64(value.file);
                        console.log(`- Successfully converted to base64 (${submitValue.length} chars)`);
                    } catch (e) {
                        console.error(`- File conversion error for "${name}":`, e);
                        submitValue = '[Error converting file: ' + e.message + ']';
                    }
                } else if (value && typeof value === 'object' && value.base64) {
                    console.log(`- Detected pre-formatted base64 for "${name}"`);
                    submitValue = value.base64;
                } else if (value && typeof value === 'object' && value.name && (value.size !== undefined || value.type)) {
                    console.log(`- Detected raw File object for "${name}"`);
                    try {
                        submitValue = await toBase64(value);
                        console.log(`- Successfully converted to base64 (${submitValue.length} chars)`);
                    } catch (e) {
                        console.error(`- File conversion error for "${name}":`, e);
                        submitValue = '[Error converting file: ' + e.message + ']';
                    }
                } else {
                    submitValue = String(value);
                }

                formData.push({
                    name: name,
                    value: submitValue
                });
            }

            console.log('DEBUG: Quiz Mutation Payload:', formData);

            const { data } = await submitQuizMutation({
                variables: {
                    input: {
                        clientMutationId: 'quiz-' + Date.now(),
                        nonce: settings.nonce,
                        answers: formData
                    }
                }
            });

            if (data?.submitQuiz?.success) {
                setSuccess(true);
            } else {
                setError(data?.submitQuiz?.message || 'Submission failed');
            }
        } catch (err) {
            console.error('Submission Error:', err);
            setError('Submission Error: ' + (err.message || JSON.stringify(err)));
        } finally {
            setIsSubmitting(false);
        }
    };

    // Close on Backdrop click
    const handleBackdropClick = (e) => {
        if (e.target === modalRef.current) {
            handleClose();
        }
    };

    const handleClose = () => {
        const wasSuccess = success;
        closeQuiz();
        if (wasSuccess) {
            // Short timeout to allow modal animation to complete
            setTimeout(resetQuiz, 300);
        }
    };

    // Close on Escape
    useEffect(() => {
        const handleEsc = (e) => {
            if (e.key === 'Escape') handleClose();
        };
        window.addEventListener('keydown', handleEsc);
        return () => window.removeEventListener('keydown', handleEsc);
    }, [closeQuiz]);

    if (!isOpen) return null;

    const isLastStep = currentStep === totalSteps;
    const currentStepData = settings?.steps[currentStep - 1];

    return (
        <div
            id="quiz-modal"
            className="active"
            ref={modalRef}
            onClick={handleBackdropClick}
            style={{
                '--quiz-primary': settings?.accentColor || '#4A6CF7'
            }}
        >
            <div className="quiz-container">
                {/* Header */}
                <div className="quiz-header">
                    <button className="quiz-close" onClick={handleClose} aria-label="Close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    {!success && <QuizProgress />}
                </div>

                {/* Body */}
                <div className="quiz-body">
                    {success ? (
                        <div className="quiz-success-message" style={{ textAlign: 'center', padding: '40px 0' }}>
                            <div
                                style={{
                                    width: '64px',
                                    height: '64px',
                                    background: 'var(--quiz-success)',
                                    borderRadius: '50%',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    margin: '0 auto 24px',
                                    color: 'white'
                                }}
                            >
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <h3 style={{ color: 'var(--quiz-text)', fontSize: '24px', marginBottom: '16px' }}>Thank You!</h3>
                            <p style={{ color: 'var(--quiz-text)', opacity: 0.8 }}>Your submission has been received. We will contact you soon.</p>
                            <button
                                className="quiz-btn quiz-btn-primary"
                                style={{ marginTop: '32px' }}
                                onClick={handleClose}
                            >
                                Close
                            </button>
                        </div>
                    ) : isLastStep ? (
                        <QuizSummary />
                    ) : (
                        currentStepData && (
                            <QuizStep
                                step={currentStepData}
                                index={currentStep}
                            />
                        )
                    )}
                    {error && (
                        <div style={{ color: 'var(--quiz-error)', marginTop: '16px', textAlign: 'center' }}>
                            {error}
                        </div>
                    )}
                </div>

                {/* Footer */}
                {!success && (
                    <div className="quiz-footer">
                        <button
                            className="quiz-btn quiz-btn-secondary"
                            onClick={prevStep}
                            style={{ display: currentStep === 1 ? 'none' : 'block' }}
                        >
                            {settings?.btnPrev || 'Back'}
                        </button>

                        {!isLastStep ? (
                            <button
                                className="quiz-btn quiz-btn-primary"
                                onClick={handleNext}
                            >
                                {settings?.btnNext || 'Next'}
                            </button>
                        ) : (
                            <button
                                className="quiz-btn quiz-btn-primary"
                                disabled={isSubmitting}
                                onClick={handleSubmit}
                            >
                                {isSubmitting ? (
                                    <span>
                                        Sending
                                        <span style={{ display: 'inline-flex', marginLeft: '2px' }}>
                                            <span className="loading-dot">.</span>
                                            <span className="loading-dot">.</span>
                                            <span className="loading-dot">.</span>
                                        </span>
                                    </span>
                                ) : (settings?.btnSubmit || 'Submit')}
                            </button>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
};

export default QuizModal;
