/**
 * Скрипт для обработки системы лайков
 */
jQuery(document).ready(function($) {
    // Обработчик клика по кнопке лайка
    $(document).on('click', '.like-button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var post_id = button.data('post-id');
        var count_display = button.siblings('.like-count');
        
        // Отправляем AJAX запрос
        $.ajax({
            url: post_likes.ajax_url,
            type: 'post',
            data: {
                action: 'process_post_like',
                post_id: post_id
            },
            beforeSend: function() {
                // Визуальное отображение процесса
                button.addClass('processing');
            },
            success: function(response) {
                button.removeClass('processing');
                
                var data = JSON.parse(response);
                
                if(data.status === 'liked') {
                    // Пользователь поставил лайк
                    button.addClass('liked');
                    button.find('.like-text').text('Вам нравится');
                } else {
                    // Пользователь убрал лайк
                    button.removeClass('liked');
                    button.find('.like-text').text('Нравится');
                }
                
                // Обновляем счетчик
                count_display.text(data.count);
                
                // Анимируем счетчик для привлечения внимания
                count_display.addClass('updated');
                setTimeout(function() {
                    count_display.removeClass('updated');
                }, 700);
            }
        });
    });
});