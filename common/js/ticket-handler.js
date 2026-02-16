jQuery(document).ready(function ($) {
    // Add Styles for Dots
    $('<style>.loading-dots:after { content: "."; animation: dots 1.5s steps(5, end) infinite; } @keyframes dots { 0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0);} 40% { color: white; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0);} 60% { text-shadow: .25em 0 0 white, .5em 0 0 rgba(0,0,0,0);} 80%, 100% { text-shadow: .25em 0 0 white, .5em 0 0 white;}}</style>').appendTo("head");

    // Form submission handler
    $(document).on("submit", "#wp-custom-contact-form", function (e) {
        e.preventDefault();

        var name = $("#wp-custom-name").val().trim();
        var email = $("#wp-custom-email").val().trim();
        var message = $("#wp-custom-message").val().trim();

        // Parse URL parameters for UTMs
        const urlParams = new URLSearchParams(window.location.search);
        var utm_source = urlParams.get("utm_source") || "";
        var utm_medium = urlParams.get("utm_medium") || "";
        var utm_campaign = urlParams.get("utm_campaign") || "";

        // Simple validation
        if (!name || !email || !message) {
            alert("Please fill in all fields");
            return false;
        }

        // Show loading
        var submitBtn = $("#wp-custom-submit");
        var originalText = submitBtn.text();
        submitBtn.data("original-text", originalText);
        submitBtn.html("Sending<span class=\"loading-dots\"></span>").prop("disabled", true);

        // AJAX request
        $.ajax({
            url: rock_stars_ticket_ajax.ajax_url,
            type: "POST",
            data: {
                action: "rock_stars_submit_ticket",
                nonce: rock_stars_ticket_ajax.nonce,
                name: name,
                email: email,
                message: message,
                utm_source: utm_source,
                utm_medium: utm_medium,
                utm_campaign: utm_campaign
            },
            success: function (response) {
                if (response.success) {
                    showSuccessModal();
                    $("#wp-custom-contact-form")[0].reset();
                } else {
                    alert("Error: " + (response.data || "Something went wrong"));
                }
            },
            error: function (xhr, status, error) {
                alert("Error: Unable to submit ticket. Check console for details.");
            },
            complete: function () {
                var submitBtn = $("#wp-custom-submit");
                var originalText = submitBtn.data("original-text") || "Submit Ticket";
                submitBtn.text(originalText).prop("disabled", false);
            }
        });
    });

    // Success Modal Function
    function showSuccessModal() {
        var modal = $('<div id="wp-custom-success-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">' +
            '<div style="background: #060607; border-radius: 16px; border: 1px solid #2E3038; padding: 40px; max-width: 420px; margin: 20px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalSlideIn 0.3s ease-out;">' +
            '<div style="margin-bottom: 20px;">' +
            '<svg style="margin: 0 auto; height: 80px; width: 80px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"></circle>' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>' +
            '</svg>' +
            '</div>' +
            '<h3 style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 12px; font-family: -apple-system, BlinkMacSystemFont, system-ui;">Thank You!</h3>' +
            '<p style="color: #d1d5db; margin-bottom: 24px; font-size: 16px; line-height: 1.5;">We have received your message and will contact you soon.</p>' +
            '<button id="close-modal" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 32px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px 0 rgba(116, 79, 168, 0.75);">Close</button>' +
            '</div>' +
            '</div>');

        // Add CSS animations if not already present
        if ($('#modal-anim-style').length === 0) {
            $('<style id="modal-anim-style">').prop('type', 'text/css').html('@keyframes modalSlideIn { from { opacity: 0; transform: translateY(-50px) scale(0.9); } to { opacity: 1; transform: translateY(0) scale(1); } }').appendTo('head');
        }

        $('body').append(modal);

        // Auto close after 5 seconds
        var autoClose = setTimeout(function () {
            modal.fadeOut(400, function () {
                modal.remove();
            });
        }, 5000);

        // Manual close
        $('#close-modal').on('click', function () {
            clearTimeout(autoClose);
            modal.fadeOut(400, function () {
                modal.remove();
            });
        });

        // Close on backdrop click
        modal.on('click', function (e) {
            if (e.target === this) {
                clearTimeout(autoClose);
                modal.fadeOut(400, function () {
                    modal.remove();
                });
            }
        });

        // Close on Escape key
        $(document).on('keydown.modal', function (e) {
            if (e.keyCode === 27) {
                clearTimeout(autoClose);
                modal.fadeOut(400, function () {
                    modal.remove();
                });
                $(document).off('keydown.modal');
            }
        });
    }
});
