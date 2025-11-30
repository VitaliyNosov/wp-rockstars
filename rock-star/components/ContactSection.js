'use client';
import { useState } from 'react';
import { gql, useQuery } from '@apollo/client';

const GET_TICKET_NONCE = gql`
  query GetTicketNonce {
    ticketNonce
  }
`;

export default function ContactSection({ wpAjaxUrl: propAjaxUrl, wpNonce: propNonce }) {
    const { data, error } = useQuery(GET_TICKET_NONCE);
    const fetchedNonce = data?.ticketNonce;

    console.log('Debug ContactSection:', {
        data,
        error,
        envUrl: process.env.NEXT_PUBLIC_WORDPRESS_API_URL,
        computedAjaxUrl: process.env.NEXT_PUBLIC_WORDPRESS_API_URL ? process.env.NEXT_PUBLIC_WORDPRESS_API_URL.replace('/graphql', '/wp-admin/admin-ajax.php') : '/wp-admin/admin-ajax.php'
    });

    const wpNonce = propNonce || fetchedNonce;
    const wpAjaxUrl = propAjaxUrl || (process.env.NEXT_PUBLIC_WORDPRESS_API_URL ? process.env.NEXT_PUBLIC_WORDPRESS_API_URL.replace('/graphql', '/wp-admin/admin-ajax.php') : '/wp-admin/admin-ajax.php');

    const [formData, setFormData] = useState({
        name: '',
        email: '',
        message: ''
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [showSuccessModal, setShowSuccessModal] = useState(false);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        // Validation
        if (!formData.name || !formData.email || !formData.message) {
            alert('Please fill in all fields');
            return;
        }

        if (!formData.email.includes('@')) {
            alert('Please enter a valid email address');
            return;
        }

        setIsSubmitting(true);

        try {
            const response = await fetch(wpAjaxUrl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'wp_custom_submit_ticket',
                    nonce: wpNonce,
                    name: formData.name,
                    email: formData.email,
                    message: formData.message
                })
            });

            const data = await response.json();

            if (data.success) {
                setShowSuccessModal(true);
                setFormData({ name: '', email: '', message: '' });

                // Auto close modal after 5 seconds
                setTimeout(() => {
                    setShowSuccessModal(false);
                }, 5000);
            } else {
                alert('Error: ' + (data.data || 'Something went wrong'));
            }
        } catch (error) {
            console.error('Form submission error:', error);
            alert('Error: Unable to submit ticket. Please try again.');
        } finally {
            setIsSubmitting(false);
        }
    };

    const closeModal = () => {
        setShowSuccessModal(false);
    };

    return (
        <>
            <section id="contact" className="bg-white dark:bg-dark pt-[120px] pb-20 overflow-hidden">
                <div className="container">
                    <div className="flex flex-wrap mx-[-16px]">
                        {/* Ticket Form - Left Side */}
                        <div className="w-full lg:w-8/12 px-4">
                            <div className="bg-primary bg-opacity-[3%] dark:bg-opacity-10 rounded-md p-11 mb-12 lg:mb-5 sm:p-[55px] lg:p-11 xl:p-[55px] wow fadeInUp" data-wow-delay=".15s">
                                <h2 className="font-bold text-black dark:text-white text-2xl sm:text-3xl lg:text-2xl xl:text-3xl mb-3">
                                    Need Help? Open a Ticket
                                </h2>
                                <p className="text-body-color text-base font-medium mb-12">
                                    Our support team will get back to you ASAP via email.
                                </p>

                                <form onSubmit={handleSubmit}>
                                    <div className="flex flex-wrap mx-[-16px]">
                                        <div className="w-full md:w-1/2 px-4">
                                            <div className="mb-8">
                                                <label htmlFor="wp-custom-name" className="block text-sm font-medium text-dark dark:text-white mb-3">
                                                    Your Name
                                                </label>
                                                <input
                                                    type="text"
                                                    id="wp-custom-name"
                                                    name="name"
                                                    value={formData.name}
                                                    onChange={handleInputChange}
                                                    placeholder="Enter your name"
                                                    className="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary"
                                                />
                                            </div>
                                        </div>
                                        <div className="w-full md:w-1/2 px-4">
                                            <div className="mb-8">
                                                <label htmlFor="wp-custom-email" className="block text-sm font-medium text-dark dark:text-white mb-3">
                                                    Your Email
                                                </label>
                                                <input
                                                    type="email"
                                                    id="wp-custom-email"
                                                    name="email"
                                                    value={formData.email}
                                                    onChange={handleInputChange}
                                                    placeholder="Enter your email"
                                                    className="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary"
                                                />
                                            </div>
                                        </div>
                                        <div className="w-full px-4">
                                            <div className="mb-8">
                                                <label htmlFor="wp-custom-message" className="block text-sm font-medium text-dark dark:text-white mb-3">
                                                    Your Message
                                                </label>
                                                <textarea
                                                    id="wp-custom-message"
                                                    name="message"
                                                    value={formData.message}
                                                    onChange={handleInputChange}
                                                    rows={5}
                                                    placeholder="Enter your Message"
                                                    className="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary resize-none"
                                                />
                                            </div>
                                        </div>
                                        <div className="w-full px-4">
                                            <button
                                                type="submit"
                                                disabled={isSubmitting}
                                                className="text-base font-medium text-white bg-primary py-4 px-9 hover:bg-opacity-80 hover:shadow-signUp rounded-md transition duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                {isSubmitting ? 'Sending...' : 'Submit Ticket'}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {/* Newsletter Subscription - Right Side */}
                        <div className="w-full lg:w-4/12 px-4">
                            <div className="relative z-10 rounded-md bg-primary bg-opacity-[3%] dark:bg-opacity-10 p-8 sm:p-11 lg:p-8 xl:p-11 mb-5 wow fadeInUp" data-wow-delay=".2s">
                                <h3 className="text-black dark:text-white font-bold text-2xl leading-tight mb-4">
                                    Subscribe to receive future updates
                                </h3>
                                <p className="font-medium text-base text-body-color leading-relaxed pb-11 mb-11 border-b border-body-color border-opacity-25 dark:border-white dark:border-opacity-25">
                                    Lorem ipsum dolor sited Sed ullam corper consectur adipiscing Mae ornare massa quis lectus.
                                </p>
                                <form>
                                    <input
                                        type="text"
                                        name="name"
                                        placeholder="Enter your name"
                                        className="w-full border border-body-color border-opacity-10 dark:border-white dark:border-opacity-10 dark:bg-[#242B51] rounded-md py-3 px-6 font-medium text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary focus:border-opacity-100 mb-4"
                                    />
                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        className="w-full border border-body-color border-opacity-10 dark:border-white dark:border-opacity-10 dark:bg-[#242B51] rounded-md py-3 px-6 font-medium text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary focus:border-opacity-100 mb-4"
                                    />
                                    <input
                                        type="submit"
                                        value="Subscribe"
                                        className="w-full border border-primary bg-primary rounded-md py-3 px-6 font-medium text-white text-base text-center outline-none cursor-pointer focus-visible:shadow-none hover:shadow-signUp hover:bg-opacity-80 transition duration-80 ease-in-out mb-4"
                                    />
                                    <p className="text-base text-body-color text-center font-medium leading-relaxed">
                                        No spam guaranteed, So please don't send any spam mail.
                                    </p>
                                </form>

                                {/* Decorative SVG Background */}
                                <div className="absolute top-0 left-0 z-[-1]">
                                    <svg width="370" height="596" viewBox="0 0 370 596" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <mask id="mask0_88:141" style={{ maskType: 'alpha' }} maskUnits="userSpaceOnUse" x="0" y="0" width="370" height="596">
                                            <rect width="370" height="596" rx="2" fill="#1D2144" />
                                        </mask>
                                        <g mask="url(#mask0_88:141)">
                                            <path opacity="0.15" d="M15.4076 50.9571L54.1541 99.0711L71.4489 35.1605L15.4076 50.9571Z" fill="url(#paint0_linear_88:141)" />
                                            <path opacity="0.15" d="M20.7137 501.422L44.6431 474.241L6 470.624L20.7137 501.422Z" fill="url(#paint1_linear_88:141)" />
                                            <path opacity="0.1" d="M331.676 198.309C344.398 204.636 359.168 194.704 358.107 180.536C357.12 167.363 342.941 159.531 331.265 165.71C318.077 172.69 318.317 191.664 331.676 198.309Z" fill="url(#paint2_linear_88:141)" />
                                            <g opacity="0.3">
                                                <path d="M209 89.9999C216 77.3332 235.7 50.7999 258.5 45.9999C287 39.9999 303 41.9999 314 30.4999C325 18.9999 334 -3.50014 357 -3.50014C380 -3.50014 395 4.99986 408.5 -8.50014C422 -22.0001 418.5 -46.0001 452 -37.5001C478.8 -30.7001 515.167 -45 530 -53" stroke="url(#paint3_linear_88:141)" />
                                                <path d="M251 64.9999C258 52.3332 277.7 25.7999 300.5 20.9999C329 14.9999 345 16.9999 356 5.49986C367 -6.00014 376 -28.5001 399 -28.5001C422 -28.5001 437 -20.0001 450.5 -33.5001C464 -47.0001 460.5 -71.0001 494 -62.5001C520.8 -55.7001 557.167 -70 572 -78" stroke="url(#paint4_linear_88:141)" />
                                                <path d="M212 73.9999C219 61.3332 238.7 34.7999 261.5 29.9999C290 23.9999 306 25.9999 317 14.4999C328 2.99986 337 -19.5001 360 -19.5001C383 -19.5001 398 -11.0001 411.5 -24.5001C425 -38.0001 421.5 -62.0001 455 -53.5001C481.8 -46.7001 518.167 -61 533 -69" stroke="url(#paint5_linear_88:141)" />
                                                <path d="M249 40.9999C256 28.3332 275.7 1.79986 298.5 -3.00014C327 -9.00014 343 -7.00014 354 -18.5001C365 -30.0001 374 -52.5001 397 -52.5001C420 -52.5001 435 -44.0001 448.5 -57.5001C462 -71.0001 458.5 -95.0001 492 -86.5001C518.8 -79.7001 555.167 -94 570 -102" stroke="url(#paint6_linear_88:141)" />
                                            </g>
                                        </g>
                                        <defs>
                                            <linearGradient id="paint0_linear_88:141" x1="13.4497" y1="63.5059" x2="81.144" y2="41.5072" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                            <linearGradient id="paint1_linear_88:141" x1="28.1579" y1="501.301" x2="8.69936" y2="464.391" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                            <linearGradient id="paint2_linear_88:141" x1="338" y1="167" x2="349.488" y2="200.004" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                            <linearGradient id="paint3_linear_88:141" x1="369.5" y1="-53" x2="369.5" y2="89.9999" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                            <linearGradient id="paint4_linear_88:141" x1="411.5" y1="-78" x2="411.5" y2="64.9999" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                            <linearGradient id="paint5_linear_88:141" x1="372.5" y1="-69" x2="372.5" y2="73.9999" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                            <linearGradient id="paint6_linear_88:141" x1="409.5" y1="-102" x2="409.5" y2="40.9999" gradientUnits="userSpaceOnUse">
                                                <stop stopColor="white" />
                                                <stop offset="1" stopColor="white" stopOpacity="0" />
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Success Modal */}
            {showSuccessModal && (
                <div
                    className="fixed top-0 left-0 w-full h-full bg-black bg-opacity-80 flex items-center justify-center z-[9999]"
                    style={{ backdropFilter: 'blur(4px)' }}
                    onClick={closeModal}
                >
                    <div
                        className="bg-[#060607] border border-[#2E3038] rounded-2xl p-10 max-w-[420px] mx-5 text-center shadow-2xl animate-[modalSlideIn_0.3s_ease-out]"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="mb-5">
                            <svg className="mx-auto h-20 w-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2" fill="none" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 className="text-2xl font-bold text-white mb-3">Thank You!</h3>
                        <p className="text-gray-300 mb-6 text-base leading-relaxed">
                            We have received your message and will contact you soon.
                        </p>
                        <button
                            onClick={closeModal}
                            className="bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white px-8 py-3 rounded-lg font-semibold text-base transition-all duration-300 hover:shadow-lg"
                            style={{ boxShadow: '0 4px 15px 0 rgba(116, 79, 168, 0.75)' }}
                        >
                            Close
                        </button>
                    </div>
                </div>
            )}

            <style jsx>{`
@keyframes modalSlideIn {
          from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
          to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
`}</style>
        </>
    );
}
