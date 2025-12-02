import React, { useState, useEffect, useRef } from 'react';

const PortfolioModal = ({ isOpen, onClose, url }) => {
    const [isLoading, setIsLoading] = useState(true);
    const [progress, setProgress] = useState(0);
    const iframeRef = useRef(null);

    // Reset state when modal opens
    useEffect(() => {
        if (isOpen) {
            setIsLoading(true);
            setProgress(0);

            // Start progress animation
            const interval = setInterval(() => {
                setProgress(prev => {
                    if (prev >= 90) {
                        clearInterval(interval);
                        return 90;
                    }
                    return prev + 5;
                });
            }, 200);

            return () => clearInterval(interval);
        }
    }, [isOpen, url]);

    const handleIframeLoad = () => {
        setProgress(100);
        setTimeout(() => {
            setIsLoading(false);
        }, 300);
    };

    if (!isOpen) return null;

    const encodedUrl = encodeURIComponent(url);
    const proxyUrl = `/api/proxy?url=${encodedUrl}`;

    return (
        <>
            <div className="portfolio-modal-overlay" onClick={onClose}>
                <div className="portfolio-modal-content" onClick={e => e.stopPropagation()}>
                    <iframe
                        ref={iframeRef}
                        src={proxyUrl}
                        className={`portfolio-iframe ${isLoading ? 'opacity-0' : 'opacity-100'}`}
                        onLoad={handleIframeLoad}
                        allowFullScreen
                    />
                </div>

                {isLoading && (
                    <div className="loading-overlay">
                        <div className="loading-text">Loading</div>
                        <div className="progress-container">
                            <div
                                className="progress-bar"
                                style={{ width: `${progress}%` }}
                            ></div>
                        </div>
                    </div>
                )}

                <button className="close-modal-btn" onClick={onClose}>
                    ✖
                </button>
            </div>

            <style jsx>{`
                .portfolio-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.95);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    opacity: 1;
                    transition: opacity 0.3s ease;
                }

                .portfolio-modal-content {
                    position: relative;
                    width: 90vw;
                    height: 92vh;
                    max-width: 1200px;
                    background: #000;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
                    transition: all 0.3s ease;
                }

                .portfolio-iframe {
                    width: 100%;
                    height: 100%;
                    border: none;
                    transition: opacity 0.5s ease;
                }

                .loading-overlay {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 350px;
                    text-align: center;
                    z-index: 10001;
                    user-select: none;
                    color: white;
                    font-family: Arial, sans-serif;
                    font-weight: 600;
                    font-size: 20px;
                }

                .loading-text {
                    margin-bottom: 12px;
                }

                .progress-container {
                    width: 100%;
                    height: 8px;
                    background: #333;
                    border-radius: 4px;
                    overflow: hidden;
                }

                .progress-bar {
                    height: 100%;
                    background: #4A6CF7;
                    border-radius: 4px;
                    transition: width 0.2s ease;
                }

                .close-modal-btn {
                    position: fixed;
                    top: 15px;
                    right: 15px;
                    background: #000;
                    color: #fff;
                    border: none;
                    padding: 8px 12px;
                    font-size: 20px;
                    cursor: pointer;
                    border-radius: 50%;
                    user-select: none;
                    box-shadow: 0 0 12px rgba(0, 0, 0, 0.9);
                    z-index: 10002;
                    transition: all 0.3s ease;
                }

                .close-modal-btn:hover {
                    background: #333;
                    transform: scale(1.1);
                }

                /* Responsive Styles */
                @media (max-width: 480px) and (orientation: portrait) {
                    .portfolio-modal-content {
                        width: 98vw;
                        height: 85vh;
                        border-radius: 4px;
                    }
                    .close-modal-btn {
                        top: 10px;
                        right: 10px;
                        padding: 8px 12px;
                        font-size: 20px;
                    }
                    .loading-overlay {
                        width: 280px;
                        font-size: 16px;
                    }
                }

                @media (max-width: 768px) and (orientation: landscape) {
                    .portfolio-modal-content {
                        width: 96vw;
                        height: 88vh;
                        border-radius: 6px;
                    }
                    .close-modal-btn {
                        top: 15px;
                        right: 15px;
                        padding: 10px 14px;
                        font-size: 22px;
                    }
                }

                @media (min-width: 481px) and (max-width: 1024px) {
                    .portfolio-modal-content {
                        width: 94vw;
                        height: 90vh;
                        border-radius: 8px;
                    }
                    .close-modal-btn {
                        top: 20px;
                        right: 20px;
                        padding: 12px 16px;
                        font-size: 24px;
                    }
                    .loading-overlay {
                        width: 320px;
                        font-size: 18px;
                    }
                }

                @media (min-width: 1025px) and (max-width: 1440px) {
                    .portfolio-modal-content {
                        width: 90vw;
                        height: 92vh;
                        max-width: 1200px;
                        border-radius: 10px;
                    }
                }

                @media (min-width: 1441px) and (max-width: 2560px) {
                    .portfolio-modal-content {
                        width: 85vw;
                        height: 90vh;
                        max-width: 1600px;
                        border-radius: 12px;
                    }
                    .close-modal-btn {
                        top: 20px;
                        right: 20px;
                        padding: 10px 14px;
                        font-size: 22px;
                    }
                    .loading-overlay {
                        width: 400px;
                        font-size: 22px;
                    }
                }

                @media (min-width: 2561px) {
                    .portfolio-modal-content {
                        width: 75vw;
                        height: 85vh;
                        max-width: 2000px;
                        border-radius: 16px;
                    }
                    .close-modal-btn {
                        top: 25px;
                        right: 25px;
                        padding: 12px 16px;
                        font-size: 24px;
                    }
                    .loading-overlay {
                        width: 450px;
                        font-size: 24px;
                    }
                }
            `}</style>
        </>
    );
};

export default PortfolioModal;
