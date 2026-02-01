import React, { useEffect, useRef } from 'react';
import { useQuiz } from './QuizContext';

const QuizProgress = () => {
    const { currentStep, totalSteps } = useQuiz();
    const containerRef = useRef(null);
    const trackRef = useRef(null);

    // Auto-scroll to center active step
    useEffect(() => {
        if (containerRef.current) {
            const container = containerRef.current;
            const activeItem = container.querySelector(`.quiz-step-indicator-item[data-index="${currentStep}"]`);

            if (activeItem) {
                const itemLeft = activeItem.offsetLeft;
                const itemWidth = activeItem.offsetWidth;
                const containerWidth = container.offsetWidth;
                const scrollPos = itemLeft - (containerWidth / 2) + (itemWidth / 2);

                container.scrollTo({
                    left: scrollPos,
                    behavior: 'smooth'
                });
            }
        }
    }, [currentStep]);

    // Calculate progress width
    // (currentStep - 1) / (totalSteps - 1)
    const progressPercent = totalSteps > 1 ? ((currentStep - 1) / (totalSteps - 1)) * 100 : 0;

    return (
        <div className="quiz-steps-wrapper" style={{ '--mobile-progress': `${progressPercent}%` }}>
            <div className="quiz-steps-container" ref={containerRef} id="quiz-steps-container">
                <div className="quiz-steps-track" ref={trackRef}>
                    <div className="quiz-steps-line-bg"></div>
                    <div
                        className="quiz-steps-line-fill"
                        style={{ width: `${progressPercent}%` }}
                    ></div>

                    {[...Array(totalSteps)].map((_, i) => {
                        const stepNum = i + 1;
                        const isCompleted = stepNum < currentStep;
                        const isActive = stepNum === currentStep;

                        return (
                            <div
                                key={stepNum}
                                className={`quiz-step-indicator-item ${isActive ? 'active' : ''} ${isCompleted ? 'completed' : ''}`}
                                data-index={stepNum}
                            >
                                {stepNum}
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );
};

export default QuizProgress;
