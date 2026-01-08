'use client';
import { useState } from 'react';
import { useQuery, gql } from '@apollo/client';
import Image from 'next/image';
import Swal from 'sweetalert2';

const GET_COMMENT_NONCE = gql`
  query GetCommentNonce {
    commentNonce
  }
`;

export default function PostComments({ postId, initialComments }) {
    const { data } = useQuery(GET_COMMENT_NONCE);
    const nonce = data?.commentNonce;

    // Ajax URL - fallback to standard if env var not set
    const wpAjaxUrl = process.env.NEXT_PUBLIC_WORDPRESS_API_URL
        ? process.env.NEXT_PUBLIC_WORDPRESS_API_URL.replace('/graphql', '/wp-admin/admin-ajax.php')
        : '/wp-admin/admin-ajax.php';

    const [comments, setComments] = useState(initialComments || []);
    const [formData, setFormData] = useState({
        author: '',
        email: '',
        comment: ''
    });
    const [touched, setTouched] = useState({
        author: false,
        email: false,
        comment: false
    });
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleBlur = (e) => {
        const { name } = e.target;
        setTouched(prev => ({
            ...prev,
            [name]: true
        }));
    };

    const validateField = (name) => {
        if (!formData[name]) return false;
        if (name === 'email') {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email);
        }
        return true;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        // Mark all as touched to show errors
        setTouched({
            author: true,
            email: true,
            comment: true
        });

        const isAuthorValid = validateField('author');
        const isEmailValid = validateField('email');
        const isCommentValid = validateField('comment');

        if (!isAuthorValid || !isEmailValid || !isCommentValid) {
            return;
        }

        if (!nonce) {
            Swal.fire({ title: 'Error', text: 'Security token missing', icon: 'error', customClass: { popup: 'swal2-custom-popup' } });
            return;
        }

        setIsSubmitting(true);

        const submitData = new URLSearchParams({
            action: 'rock_stars_submit_comment',
            comment_post_ID: postId,
            author: formData.author,
            email: formData.email,
            comment: formData.comment,
            _wpnonce: nonce,
        });

        try {
            const response = await fetch(wpAjaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: submitData
            });

            const result = await response.json().catch(() => null);

            if (result && result.success) {
                // Optimistically add the new comment
                // Ensure the structure matches the GraphQL SimpleComment type
                let newCommentData;

                // Normalizing data to ensure it matches what the component expects
                // Backend might return snake_case or different structure
                const backendData = result.data || {};

                newCommentData = {
                    id: backendData.id ? backendData.id.toString() : Date.now().toString(),
                    authorName: backendData.author_name || backendData.authorName || formData.author,
                    // Content might be an object with rendered property in WP REST API
                    content: backendData.content?.rendered || backendData.content || formData.comment,
                    date: backendData.date || 'Just now',
                    // Use a default gravatar if none provided
                    authorAvatar: backendData.author_avatar_urls?.['96'] || 'https://secure.gravatar.com/avatar/?s=96&d=mm&f=y',
                    parentId: '0'
                };

                setComments(prev => [...prev, newCommentData]);

                // Reset form
                setFormData({ author: '', email: '', comment: '' });
                setTouched({ author: false, email: false, comment: false });
            } else {
                throw new Error(result?.data || 'Submission failed');
            }

        } catch (error) {
            console.error(error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'Could not post comment. Please try again.',
                icon: 'error',
                customClass: { popup: 'swal2-custom-popup' }
            });
        } finally {
            setIsSubmitting(false);
        }
    };

    const getFieldClass = (name) => {
        // Removed focus:border-primary from baseClass to avoid override
        const baseClass = "w-full border rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none dark:bg-[#242B51]";

        if (!touched[name]) {
            // Default state
            return `${baseClass} !border-gray-300 dark:!border-gray-700 focus:!border-primary`;
        }

        return validateField(name)
            ? `${baseClass} !border-blue-500 focus:!border-blue-500`
            : `${baseClass} !border-red-500 focus:!border-red-500`;
    };

    // Helper for inline styles to bypass specificty wars
    const getFieldStyle = (name) => {
        if (!touched[name]) return {};
        return {
            borderColor: validateField(name) ? '#2563EB' : '#ef4444' // Blue / Red
        };
    };

    const formatDate = (dateString) => {
        if (!dateString || dateString === 'Just now') return 'Just now';

        // Handle Russian dates manually
        const months = {
            'января': 0, 'февраля': 1, 'марта': 2, 'апреля': 3, 'мая': 4, 'июня': 5,
            'июля': 6, 'августа': 7, 'сентября': 8, 'октября': 9, 'ноября': 10, 'декабря': 11,
            'gennaio': 0, 'febbraio': 1, 'marzo': 2, 'aprile': 3, 'maggio': 4, 'giugno': 5,
            'luglio': 6, 'agosto': 7, 'settembre': 8, 'ottobre': 9, 'novembre': 10, 'dicembre': 11
        };

        // Regex for "7 января, 2026 at..." or just "7 января, 2026"
        const ruDateMatch = dateString.match(/(\d+)\s+([а-яa-z]+),?\s+(\d{4})/i);

        let dateToFormat;

        if (ruDateMatch) {
            const day = parseInt(ruDateMatch[1]);
            const monthStr = ruDateMatch[2].toLowerCase();
            const year = parseInt(ruDateMatch[3]);

            if (months[monthStr] !== undefined) {
                dateToFormat = new Date(year, months[monthStr], day);
            }
        }

        if (!dateToFormat) {
            dateToFormat = new Date(dateString);
        }

        if (isNaN(dateToFormat.getTime())) {
            // If parsing fails, try to return as is but stripped of ' at' if present
            return dateString.replace(' at', '');
        }

        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }).format(dateToFormat);
    };

    const [isOpen, setIsOpen] = useState(false);

    const toggleComments = () => {
        setIsOpen(!isOpen);
    };

    return (
        <div className="pt-8 w-full">
            {/* View Comments Button - Only show when closed */}
            <div className={`transition-all duration-300 ease-in-out ${isOpen ? 'opacity-0 max-h-0 overflow-hidden mb-0' : 'opacity-100 max-h-20 mb-8'}`}>
                <div className="overflow-hidden min-h-0">
                    <button
                        onClick={toggleComments}
                        className="inline-flex items-center justify-center py-4 px-9 rounded-md bg-transparent border border-[#959CB1] text-[#959CB1] font-medium hover:bg-primary hover:border-primary hover:text-white hover:bg-opacity-90 hover:shadow-signUp transition duration-300 !text-[#959CB1] !border-[#959CB1]"
                        style={{ color: '#959CB1', borderColor: '#959CB1' }}
                    >
                        View Comments ({comments.length})
                    </button>
                </div>
            </div>

            {/* Collapsible Content */}
            <div
                className={`transition-all duration-500 ease-in-out overflow-hidden ${isOpen ? 'max-h-[5000px] opacity-100' : 'max-h-0 opacity-0'}`}
                style={{ maxHeight: isOpen ? '5000px' : '0px', opacity: isOpen ? 1 : 0 }}
            >
                <div>
                    <div className="flex items-center justify-between mb-8 pt-4">
                        <h3 className="font-bold text-black dark:text-white text-2xl">
                            {comments.length} Comments
                        </h3>
                        <button
                            onClick={toggleComments}
                            className="text-body-color hover:text-red-500 transition-colors text-xs font-medium flex items-center group"
                        >
                            <span className="mr-2 leading-none">Hide</span>
                            <svg width="10" height="10" viewBox="0 0 12 12" className="fill-current block">
                                <path d="M6 4.87868L10.9497 0L12 1.05025L7.05025 6L12 10.9497L10.9497 12L6 7.05025L1.05025 12L0 10.9497L4.94975 6L0 1.05025L1.05025 0L6 4.87868Z" />
                            </svg>
                        </button>
                    </div>

                    {/* Comments List */}
                    <div className="mb-12">
                        {comments.map((comment, index) => (
                            <div key={`${comment.id}-${index}`} className="mb-8" id={`comment-${comment.id}`}>
                                <div className="comment-block flex items-start bg-primary bg-opacity-[3%] dark:bg-dark p-6 rounded-[22px] rounded-tl-none border border-transparent dark:border-[#34374C]">
                                    <div className="flex-shrink-0 mr-4">
                                        <div className="w-[50px] h-[50px] rounded-full overflow-hidden relative">
                                            {comment.authorAvatar ? (
                                                <Image
                                                    src={comment.authorAvatar}
                                                    alt={comment.authorName}
                                                    width={50}
                                                    height={50}
                                                    className="object-cover"
                                                />
                                            ) : (
                                                <div className="w-full h-full bg-gray-300"></div>
                                            )}
                                        </div>
                                    </div>
                                    <div className="flex-grow">
                                        <div className="flex flex-wrap items-center mb-2">
                                            <h4 className="font-bold text-black dark:text-white text-sm mr-3">
                                                {comment.authorName}
                                            </h4>
                                            <span className="text-sm text-body-color flex items-center">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" className="mr-1">
                                                    <path d="M4.66667 1.16666V2.91666M9.33333 1.16666V2.91666M2.04167 5.30249H11.9583M2.33333 4.08332H11.6667C12.311 4.08332 12.8333 4.60566 12.8333 5.24999V11.0833C12.8333 11.7277 12.311 12.25 11.6667 12.25H2.33333C1.689 12.25 1.16667 11.7277 1.16667 11.0833V5.24999C1.16667 4.60566 1.689 4.08332 2.33333 4.08332Z" stroke="currentColor" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                                {formatDate(comment.date)}
                                            </span>
                                        </div>
                                        <div
                                            className="text-base text-body-color mb-3"
                                            dangerouslySetInnerHTML={{ __html: comment.content }}
                                        />
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Comment Form */}
                    <div className="bg-primary bg-opacity-[3%] dark:bg-opacity-10 rounded-md p-8 sm:p-11 lg:p-8 xl:p-11 border-[#E5E7EB] dark:border-[#34374C]">
                        <h3 className="font-bold text-black dark:text-white text-2xl mb-4">
                            Leave a Comment
                        </h3>
                        <p className="text-body-color text-base font-medium mb-10">
                            Your email address will not be published. Required fields are marked *
                        </p>

                        <form onSubmit={handleSubmit}>
                            <div className="flex flex-wrap mx-[-16px]">
                                <div className="w-full md:w-1/2 px-4">
                                    <div className="mb-8">
                                        <input
                                            type="text"
                                            name="author"
                                            value={formData.author}
                                            onChange={handleInputChange}
                                            onBlur={handleBlur}
                                            placeholder="Your Name *"
                                            className={getFieldClass('author')}
                                            style={getFieldStyle('author')}
                                        />
                                    </div>
                                </div>
                                <div className="w-full md:w-1/2 px-4">
                                    <div className="mb-8">
                                        <input
                                            type="email"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleInputChange}
                                            onBlur={handleBlur}
                                            placeholder="Your Email *"
                                            className={getFieldClass('email')}
                                            style={getFieldStyle('email')}
                                        />
                                    </div>
                                </div>
                                <div className="w-full px-4">
                                    <div className="mb-8">
                                        <textarea
                                            name="comment"
                                            value={formData.comment}
                                            onChange={handleInputChange}
                                            onBlur={handleBlur}
                                            rows="5"
                                            placeholder="Your Comment *"
                                            className={`${getFieldClass('comment')} resize-none`}
                                            style={getFieldStyle('comment')}
                                        ></textarea>
                                    </div>
                                </div>
                                <div className="w-full px-4">
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className="text-base font-medium text-white bg-primary py-4 px-9 hover:bg-opacity-80 hover:shadow-signUp rounded-md transition duration-300 ease-in-out disabled:opacity-50"
                                    >
                                        {isSubmitting ? 'Posting...' : 'Post Comment'}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}
