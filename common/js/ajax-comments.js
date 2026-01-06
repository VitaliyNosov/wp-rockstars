document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('commentform');

    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = commentForm.querySelector('input[type="submit"], button[type="submit"]');
            const originalBtnText = submitBtn.value || submitBtn.innerText;

            // Helper to reset button state
            function resetBtn() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText; // Use innerHTML to overwrite dots
                submitBtn.style.opacity = '1';
            }

            // Validation Helpers
            const setInvalid = (field) => {
                field.classList.remove('border-transparent', 'border-blue-500');
                field.classList.add('border-red-500');
            };
            const setValid = (field) => {
                field.classList.remove('border-transparent', 'border-red-500');
                field.classList.add('border-blue-500');
            };
            const clearValidation = (field) => {
                field.classList.add('border-transparent');
                field.classList.remove('border-red-500', 'border-blue-500');
            };

            // Field Validators
            const validateAuthor = () => {
                const author = commentForm.querySelector('#author');
                if (!author) return true;
                if (author.value.trim() === '') {
                    setInvalid(author);
                    return false;
                }
                setValid(author);
                return true;
            };

            const validateEmail = () => {
                const email = commentForm.querySelector('#email');
                if (!email) return true;
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value.trim() === '' || !emailPattern.test(email.value)) {
                    setInvalid(email);
                    return false;
                }
                setValid(email);
                return true;
            };

            const validateComment = () => {
                const comment = commentForm.querySelector('#comment');
                if (!comment) return true;
                if (comment.value.trim() === '') {
                    setInvalid(comment);
                    return false;
                }
                setValid(comment);
                return true;
            };

            // Attach Live Validation
            const authorInput = commentForm.querySelector('#author');
            const emailInput = commentForm.querySelector('#email');
            const commentInput = commentForm.querySelector('#comment');

            if (authorInput) authorInput.addEventListener('input', validateAuthor);
            if (emailInput) emailInput.addEventListener('input', validateEmail);
            if (commentInput) commentInput.addEventListener('input', validateComment);

            // Validate Fields on Submit
            let isValid = true;
            if (!validateAuthor()) isValid = false;
            if (!validateEmail()) isValid = false;
            if (!validateComment()) isValid = false;

            if (!isValid) return;

            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending <span class="loading-dots"></span>';
            submitBtn.style.opacity = '0.7';

            // Collect data
            const formData = new FormData(commentForm);

            // Manual fallback to ensure fields are grabbed
            const authorField = document.getElementById('author');
            const emailField = document.getElementById('email');

            if (authorField) formData.set('author', authorField.value);
            if (emailField) formData.set('email', emailField.value);

            formData.append('action', 'rock_stars_submit_comment');
            formData.append('_wpnonce', rock_stars_ajax.nonce);

            fetch(rock_stars_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        const listWrapper = document.querySelector('.comment-list-wrapper');

                        // Update comment counter
                        const countSpan = document.getElementById('post-comment-count');
                        if (countSpan) {
                            let currentCount = parseInt(countSpan.innerText.replace(/\D/g, '')); // extract number
                            if (!isNaN(currentCount)) {
                                countSpan.innerText = currentCount + 1;
                            }
                        }

                        if (listWrapper) {
                            listWrapper.insertAdjacentHTML('beforeend', response.data.html);
                        } else {
                            location.reload();
                            return;
                        }

                        commentForm.reset();

                        // Clear borders after success
                        if (author) clearValidation(author);
                        if (email) clearValidation(email);
                        if (comment) clearValidation(comment);

                        resetBtn();
                    } else {
                        alert('Error: ' + response.data);
                        resetBtn();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error. Please try again.');
                    resetBtn();
                });
        });
    }
});
