import React, { createContext, useContext, useState, useCallback, useEffect } from 'react';

const QuizContext = createContext();

export const useQuiz = () => {
    const context = useContext(QuizContext);
    if (!context) {
        throw new Error('useQuiz must be used within a QuizProvider');
    }
    return context;
};

export const QuizProvider = ({ children, settings }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [currentStep, setCurrentStep] = useState(1);
    const [answers, setAnswers] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(false);

    // Total steps = settings.steps.length + 1 (Summary)
    const totalSteps = settings?.steps ? settings.steps.length + 1 : 1;

    const openQuiz = useCallback((step = 1) => {
        // If step is an event (object) or not a number, default to 1
        const targetStep = (typeof step === 'number') ? step : 1;
        setCurrentStep(targetStep);
        setIsOpen(true);
        document.body.style.overflow = 'hidden';
    }, []);

    const closeQuiz = useCallback(() => {
        setIsOpen(false);
        document.body.style.overflow = '';
    }, []);

    const updateAnswer = useCallback((name, value) => {
        setAnswers(prev => ({ ...prev, [name]: value }));
    }, []);

    const nextStep = useCallback(() => {
        if (currentStep < totalSteps) {
            setCurrentStep(prev => prev + 1);
        }
    }, [currentStep, totalSteps]);

    const prevStep = useCallback(() => {
        if (currentStep > 1) {
            setCurrentStep(prev => prev - 1);
        }
    }, [currentStep]);

    const resetQuiz = useCallback(() => {
        setCurrentStep(1);
        setAnswers({});
        setError(null);
        setSuccess(false);
    }, []);

    const value = {
        settings,
        isOpen,
        currentStep,
        totalSteps,
        answers,
        isSubmitting,
        error,
        success,
        openQuiz,
        closeQuiz,
        nextStep,
        prevStep,
        updateAnswer,
        setIsSubmitting,
        setError,
        setSuccess,
        resetQuiz
    };

    return (
        <QuizContext.Provider value={value}>
            {children}
        </QuizContext.Provider>
    );
};
