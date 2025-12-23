import React, { useState } from 'react';

const SectionEight = ({ data }) => {
    const title = data?.sec8Title;
    const placeholder = data?.sec8Placeholder || 'Enter your email';
    const btnText = data?.sec8BtnText || 'Subscribe';

    const [email, setEmail] = useState('');
    const [status, setStatus] = useState('idle'); // idle, sending, success, error
    const [message, setMessage] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [dots, setDots] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!email) return;

        setStatus('sending');
        setMessage('');

        // Dots animation logic: 0 -> 1 -> 2 -> 3 -> 0
        let dotCount = 0;
        const intervalId = setInterval(() => {
            dotCount = (dotCount + 1) % 4;
            setDots('.'.repeat(dotCount));
        }, 500);

        try {
            const formData = new FormData();
            formData.append('action', 'wp_custom_subscribe');
            formData.append('email', email);
            if (data?.subscribeNonce) {
                formData.append('nonce', data.subscribeNonce);
            }

            // Using absolute URL for localhost environment
            const ajaxUrl = 'http://localhost:8081/wp-admin/admin-ajax.php';

            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                setStatus('success');
                setShowModal(true);
                setEmail('');
            } else {
                setStatus('error');
                setMessage(result.data || 'Sending error.');
            }

        } catch (error) {
            console.error('Error:', error);
            setStatus('error');
            setMessage('An error occurred. Please try again.');
        } finally {
            clearInterval(intervalId);
            setDots('');
        }
    };

    const closeModal = () => {
        setShowModal(false);
        setStatus('idle');
        setMessage('');
    };

    return (
        <div className="max-w-6xl py-10 px-4 sm:px-6 lg:px-8 lg:py-16 mx-auto wow fadeInUp" data-wow-delay=".2s">
            <div className="max-w-xl text-center mx-auto">
                <div className="mb-5">
                    <h2 className="text-2xl font-bold md:text-3xl md:leading-tight dark:text-white">
                        {title}
                    </h2>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="mt-5 lg:mt-8 flex flex-col items-center gap-2 sm:flex-row sm:gap-3">
                        <div className="w-full">
                            <label htmlFor="subscribe-email" className="sr-only">Email</label>
                            <input
                                type="email"
                                id="subscribe-email"
                                name="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                className="w-full border border-gray-200 dark:border-neutral-700 dark:bg-neutral-900 rounded-md shadow-xs dark:shadow-neutral-700/70 py-3 px-6 text-gray-800 dark:text-neutral-200 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                                placeholder={placeholder}
                                required
                                disabled={status === 'sending'}
                            />
                        </div>
                        <button
                            type="submit"
                            className="w-full sm:w-auto whitespace-nowrap py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                            disabled={status === 'sending'}
                            style={{ minWidth: '120px' }}
                        >
                            {status === 'sending' ? (
                                <span>Sending<span style={{ display: 'inline-block', width: '1.5em', textAlign: 'left' }}>{dots}</span></span>
                            ) : btnText}
                        </button>
                    </div>
                    {status === 'error' && (
                        <div className="mt-3 text-sm text-red-500">{message || 'An error occurred.'}</div>
                    )}
                </form>
            </div>

            {/* Success Modal */}
            {showModal && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity duration-300" onClick={closeModal}>
                    <div
                        className="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl p-10 max-w-md w-full mx-4 text-center shadow-2xl transform transition-all duration-300 scale-100"
                        onClick={e => e.stopPropagation()}
                    >
                        <div className="mb-5">
                            <svg className="mx-auto h-20 w-20 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" strokeWidth="2" stroke="currentColor" fill="none"></circle>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 className="text-2xl font-bold text-gray-800 dark:text-white mb-3">Thank You!</h3>
                        <p className="text-gray-600 dark:text-neutral-400 mb-6">
                            We have received your subscription.
                        </p>
                        <button
                            onClick={closeModal}
                            className="bg-blue-600 text-white py-3 px-8 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg"
                        >
                            Close
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default SectionEight;
