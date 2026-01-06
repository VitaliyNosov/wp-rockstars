document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.getElementById('post-like-btn');

    if (likeBtn) {
        const postId = likeBtn.dataset.postId;
        const countSpan = likeBtn.querySelector('.like-count');

        // Ключ для LocalStorage
        const storageKey = 'rock_stars_liked_' + postId;

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
