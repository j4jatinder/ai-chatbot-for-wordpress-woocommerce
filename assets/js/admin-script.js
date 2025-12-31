jQuery(document).ready(function($) {

    // --- Utility function to show feedback ---
    function showFeedback(message, isSuccess = true) {
        const feedback = $('#wp-rag-ai-chatbot-feedback');
        feedback.removeClass('notice-success notice-error').html('');
        
        if (message) {
            feedback.addClass(isSuccess ? 'notice-success' : 'notice-error');
            feedback.html('<p>' + message + '</p>');
            feedback.slideDown().delay(5000).slideUp();
        } else {
            feedback.slideUp();
        }
    }

    // --- 1. Handle Node URL Registration Form Submission ---
    $('#rag-site-register-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('input[type="submit"]');
        const nodeUrl = $form.find('#rag_node_url').val();
        
        $submitBtn.prop('disabled', true).val('Registering...');
        showFeedback(null); // Clear previous messages

        $.ajax({
            url: wpRagAiChatbotAdmin.ajaxurl, // Localized AJAX URL from PHP
            type: 'POST',
            data: {
                action: 'wp_rag_ai_chatbot_register_site', // Must match the PHP action hook
                security: wpRagAiChatbotAdmin.nonceRegister, // Localized Nonce
                node_url: nodeUrl
            },
            success: function(response) {
                if (response.success) {
                    showFeedback(response.data.message, true);
                } else {
                    showFeedback(response.data.message || 'Registration failed due to an unknown error.', false);
                }
            },
            error: function() {
                showFeedback('An AJAX error occurred. Check server logs.', false);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).val('Register Site');
            }
        });
    });

    // --- 2. Handle Manual Data Push Button Click ---
    $('#rag-data-push-button').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        $button.prop('disabled', true).text('Pushing Data...');
        showFeedback(null); 

        $.ajax({
            url: wpRagAiChatbotAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_rag_ai_chatbot_manual_data_push', // Must match the PHP action hook
                security: wpRagAiChatbotAdmin.nonceDataPush
            },
            success: function(response) {
                if (response.success) {
                    showFeedback(response.data.message, true);
                } else {
                    showFeedback(response.data.message || 'Data push failed.', false);
                }
            },
            error: function() {
                showFeedback('An AJAX error occurred during data push.', false);
            },
            complete: function() {
                $button.prop('disabled', false).text('Manually Push Data');
            }
        });
    });

    // --- 3. Handle API Keys Form Submission ---
    $('#rag-api-keys-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('input[type="submit"]');
        const geminiKey = $form.find('#rag_gemini_key').val();
        const chatGptKey = $form.find('#rag_chatgpt_key').val();

        $submitBtn.prop('disabled', true).val('Sending Keys...');
        showFeedback(null);

        $.ajax({
            url: wpRagAiChatbotAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_rag_ai_chatbot_send_keys', // Must match the PHP action hook
                security: wpRagAiChatbotAdmin.nonceKeysSend,
                gemini_key: geminiKey,
                chatgpt_key: chatGptKey
            },
            success: function(response) {
                if (response.success) {
                    showFeedback(response.data.message, true);
                } else {
                    showFeedback(response.data.message || 'Failed to save/send API keys.', false);
                }
            },
            error: function() {
                showFeedback('An AJAX error occurred when sending API keys.', false);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).val('Save & Send Keys');
            }
        });
    });




});
