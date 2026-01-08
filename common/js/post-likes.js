document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.getElementById('post-like-btn');

    if (likeBtn) {
        const postId = likeBtn.dataset.postId;
        const countSpan = likeBtn.querySelector('.like-count');
        // Target the icon container for the burst origin
        const iconContainer = likeBtn.querySelector('span');
        const iconSvg = likeBtn.querySelector('svg');

        // Ключ для LocalStorage
        const storageKey = 'rock_stars_liked_' + postId;

        // Initialize animations variables
        let burst;

        // Ensure container is relative for absolute positioning of burst
        if (iconContainer) {
            iconContainer.style.position = 'relative';
            iconContainer.style.display = 'inline-flex'; // Ensure it behaves like a block for positioning
            iconContainer.style.justifyContent = 'center';
            iconContainer.style.alignItems = 'center';
        }

        // Check if mo.js is loaded
        if (typeof mojs !== 'undefined') {
            // Configure the burst animation
            burst = new mojs.Burst({
                parent: iconContainer || likeBtn, // Attach to icon if found, else button
                radius: { 0: 30 },
                count: 10,
                // Center the burst in the container
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
        }

        // Функция для обновления UI
        const updateUI = (isLiked) => {
            if (isLiked) {
                likeBtn.classList.add('liked-post');
            } else {
                likeBtn.classList.remove('liked-post');
            }
        };

        // Инициализация при загрузке
        const isLikedInitial = localStorage.getItem(storageKey) === 'true';
        updateUI(isLikedInitial);

        likeBtn.addEventListener('click', function (e) {
            e.preventDefault();

            // Текущее состояние
            const currentlyLiked = likeBtn.classList.contains('liked-post');

            // Новое состояние (инвертируем)
            const newLikedState = !currentlyLiked;
            const action = newLikedState ? 'add' : 'remove';

            // Текущее число лайков
            let currentCount = parseInt(countSpan.textContent) || 0;

            // 1. ОПТИМИСТИЧНЫЙ UI
            updateUI(newLikedState);
            countSpan.textContent = newLikedState ? currentCount + 1 : Math.max(0, currentCount - 1);

            // ANIMATION EFFECTS
            if (newLikedState) {
                // 1. Burst Effect (mo.js)
                if (burst) {
                    burst.replay();
                }

                // 2. Pulse Effect (GSAP)
                if (typeof TweenMax !== 'undefined' && iconSvg) {
                    TweenMax.fromTo(iconSvg, 0.4,
                        { scale: 1 },
                        { scale: 1.4, yoyo: true, repeat: 1, ease: Power2.easeOut }
                    );
                }
            }

            // Обновляем LocalStorage сразу
            if (newLikedState) {
                localStorage.setItem(storageKey, 'true');
            } else {
                localStorage.removeItem(storageKey);
            }

            // 2. Отправляем запрос на сервер
            const data = new FormData();
            data.append('action', 'rock_stars_like_post');
            data.append('nonce', rock_stars_likes.nonce);
            data.append('post_id', postId);
            data.append('like_action', action);

            fetch(rock_stars_likes.ajax_url, {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(response => {
                    if (!response.success) {
                        // Откат при ошибке сервера
                        console.error('Like failed, reverting UI');
                        updateUI(currentlyLiked);
                        countSpan.textContent = currentCount;
                        if (currentlyLiked) {
                            localStorage.setItem(storageKey, 'true');
                        } else {
                            localStorage.removeItem(storageKey);
                        }
                    }
                })
                .catch(error => {
                    console.error('Network Error:', error);
                    // Откат при ошибке сети
                    updateUI(currentlyLiked);
                    countSpan.textContent = currentCount;
                    if (currentlyLiked) {
                        localStorage.setItem(storageKey, 'true');
                    } else {
                        localStorage.removeItem(storageKey);
                    }
                });
        });
    }
});
