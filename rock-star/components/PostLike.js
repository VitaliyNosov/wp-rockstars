'use client';
import { useState, useEffect, useRef } from 'react';
import { useQuery, gql } from '@apollo/client';

import { gsap } from 'gsap';

const GET_LIKE_NONCE = gql`
  query GetLikeNonce {
    likeNonce
  }
`;

export default function PostLike({ postId, initialLikes }) {
    const { data } = useQuery(GET_LIKE_NONCE);
    const nonce = data?.likeNonce;

    // Ajax URL - fallback to standard if env var not set
    const wpAjaxUrl = process.env.NEXT_PUBLIC_WORDPRESS_API_URL
        ? process.env.NEXT_PUBLIC_WORDPRESS_API_URL.replace('/graphql', '/wp-admin/admin-ajax.php')
        : '/wp-admin/admin-ajax.php';

    const [likes, setLikes] = useState(parseInt(initialLikes) || 0);
    const [isLiked, setIsLiked] = useState(false);
    const iconRef = useRef(null);
    const btnRef = useRef(null);

    // Animation refs
    const burstRef = useRef(null);

    // Initial load check
    useEffect(() => {
        if (typeof window !== 'undefined') {
            const storageKey = `rock_stars_liked_${postId}`;
            const liked = localStorage.getItem(storageKey) === 'true';
            setIsLiked(liked);
        }
    }, [postId]);

    // Initialize mo.js burst
    useEffect(() => {
        if (typeof window !== 'undefined' && iconRef.current) {
            import('mo-js').then((mojsModule) => {
                const mojs = mojsModule.default || mojsModule;
                const burst = new mojs.Burst({
                    parent: iconRef.current,
                    radius: { 0: 30 },
                    count: 10,
                    // Center the burst
                    left: '50%',
                    top: '50%',
                    angle: { 0: 90 },
                    children: {
                        shape: 'circle',
                        radius: { 4: 0 },
                        fill: ['#ef4444', '#f87171', '#dc2626'], // Tailwind Red
                        strokeWidth: 0,
                        duration: 1000,
                        easing: 'sin.out'
                    }
                });
                burstRef.current = burst;
            });
        }
    }, []);

    const handleLike = async (e) => {
        e.preventDefault();

        // Optimistic Update
        const newLikedState = !isLiked;
        setIsLiked(newLikedState);
        setLikes(prev => newLikedState ? prev + 1 : Math.max(0, prev - 1));

        // Animations
        if (newLikedState) {
            // 1. Burst
            if (burstRef.current) {
                burstRef.current.replay();
            }
            // 2. Pulse (GSAP)
            if (iconRef.current) {
                const svg = iconRef.current.querySelector('svg');
                if (svg) {
                    gsap.fromTo(svg,
                        { scale: 1 },
                        { duration: 0.4, scale: 1.4, yoyo: true, repeat: 1, ease: "power2.out" }
                    );
                }
            }
        }

        // Local Storage
        const storageKey = `rock_stars_liked_${postId}`;
        if (newLikedState) {
            localStorage.setItem(storageKey, 'true');
        } else {
            localStorage.removeItem(storageKey);
        }

        // Server Request
        if (!nonce) return; // Silent fail if no nonce yet

        const action = newLikedState ? 'add' : 'remove';
        const formData = new URLSearchParams({
            action: 'rock_stars_like_post',
            nonce: nonce,
            post_id: postId,
            like_action: action
        });

        try {
            const response = await fetch(wpAjaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });
            const result = await response.json();

            if (!result.success) {
                // Revert on failure
                console.error('Like failed', result);
                setIsLiked(!newLikedState);
                setLikes(prev => !newLikedState ? prev + 1 : Math.max(0, prev - 1));
            }
        } catch (error) {
            console.error('Network error', error);
            // Revert on error
            setIsLiked(!newLikedState);
            setLikes(prev => !newLikedState ? prev + 1 : Math.max(0, prev - 1));
        }
    };

    return (
        <a
            href="#"
            onClick={handleLike}
            className={`flex items-center text-sm font-medium mr-6 transition-colors ${isLiked ? 'text-red-500' : 'text-body-color hover:text-red-500'
                }`}
        >
            <span ref={iconRef} className="mr-4 relative inline-flex justify-center items-center" style={{ width: '20px', height: '20px' }}>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    strokeWidth="2"
                    stroke="currentColor"
                    fill={isLiked ? "currentColor" : "none"}
                    className="w-4 h-4 transition-colors"
                >
                    <path strokeLinecap="round" strokeLinejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </span>
            <span className="like-count">
                {likes}
            </span>
        </a>
    );
}
