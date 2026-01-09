jQuery(document).ready(function($) {
    var $msg = $('#rag-admin-message');
    var $msgP = $msg.find('p');

    // Function to display messages
    function displayMessage(type, message) {
        $msg.removeClass('notice-error notice-success').addClass('notice-' + type).show();
        $msgP.text(message);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        setTimeout(function() {
            $msg.fadeOut();
        }, 5000);
    }

    // --- 1. Site Registration Handler (Unchanged) ---
    $('#site-register-button').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalText = $btn.text();

        if ($('#acsec_chatbot_node_url').val() === '') {
            displayMessage('error', 'Please enter and save the RAG Node URL first.');
            return;
        }

        $btn.prop('disabled', true).text('Registering...');

        $.ajax({
            url: acsecWpRagChatbotAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'acsec_chatbot_register',
                security: acsecWpRagChatbotAdmin.siteRegisterNonce,
            },
            success: function(response) {
                if (response.success) {
                    displayMessage('success', response.data.message);
                    $('#site-id-display').text(response.data.siteId);
                    $('#api-key-display').text(response.data.apiKey);
                    $btn.text('Site Registered').prop('disabled', true);
                    // Enable subsequent buttons
                    $('#send-keys-button, #data-push-button').prop('disabled', false);

                } else {
                    displayMessage('error', response.data.message);
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function() {
                displayMessage('error', 'An unknown error occurred during site registration.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });

    // --- 2. Combined Config Save and Send Keys Handler (UPDATED) ---
    $('#send-keys-button').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalText = $btn.html(); // Use html() to preserve button tags
        var openaiKey = $('#openai_key').val();
        var geminiKey = $('#gemini_key').val();

        if ($('#api-key-display').text() === 'N/A') {
            displayMessage('error', 'Please complete Step 1: Register Site first.');
            return;
        }

        if (openaiKey === '' && geminiKey === '') {
            // Note: We allow sending an empty key if a previous key is already configured on the node
            // But we still want to save the configuration settings (models/provider)
            displayMessage('warning', 'No new API keys entered. Sending configuration update only.');
        }

        $btn.prop('disabled', true).text('Saving Config...');

        // Step A: Save Configuration Locally (Active Provider, Models)
        $.ajax({
            url: acsecWpRagChatbotAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'acsec_chatbot_save_config',
                security: acsecWpRagChatbotAdmin.saveConfigNonce,
                active_provider: $('#active_provider').val(),
                openai_model: $('#openai_model').val(),
                gemini_model: $('#gemini_model').val(),
            },
            success: function(response) {
                if (response.success) {
                    // Step B: Send Keys to Node (if provided)
                    var keysToSend = {};
                    if (openaiKey !== '') keysToSend.openai = openaiKey;
                    if (geminiKey !== '') keysToSend.gemini = geminiKey;

                    if (Object.keys(keysToSend).length > 0) {
                        $btn.text('Sending Keys...');
                        $.ajax({
                            url: acsecWpRagChatbotAdmin.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'acsec_chatbot_send_api_keys',
                                security: acsecWpRagChatbotAdmin.sendKeysNonce,
                                keys: keysToSend,
                            },
                            success: function(response2) {
                                if (response2.success) {
                                    displayMessage('success', 'Configuration saved and API keys sent successfully.');
                                    $btn.text('Keys Sent & Config Saved').prop('disabled', true);
                                    $('#data-push-button').prop('disabled', false);
                                } else {
                                    displayMessage('error', 'Configuration saved, but failed to send API keys: ' + response2.data.message);
                                    $btn.text(originalText).prop('disabled', false);
                                }
                            },
                            error: function() {
                                displayMessage('error', 'Configuration saved, but an unknown error occurred while sending API keys.');
                                $btn.text(originalText).prop('disabled', false);
                            }
                        });
                    } else {
                        displayMessage('success', 'Configuration saved successfully.');
                        $btn.text('Config Saved').prop('disabled', true);
                        $('#data-push-button').prop('disabled', false);
                    }
                } else {
                    displayMessage('error', 'Failed to save configuration: ' + response.data.message);
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function() {
                displayMessage('error', 'An unknown error occurred while saving configuration.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });

    // --- 3. Data Push Handler ---
    $('#data-push-button').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalText = $btn.html();
        var selectedTypes = $('input[name="acsec_chatbot_data_push_types[]"]:checked').map(function() {
            return this.value;
        }).get();

        if (selectedTypes.length === 0) {
            displayMessage('error', 'Please select at least one content type to push.');
            return;
        }

        $btn.prop('disabled', true).text('Pushing Data...');

        $.ajax({
            url: acsecWpRagChatbotAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'acsec_chatbot_push_data',
                security: acsecWpRagChatbotAdmin.dataPushNonce,
                data_types: selectedTypes,
            },
            success: function(response) {
                if (response.success) {
                    displayMessage('success', response.data.message);
                    $btn.text('Data Pushed Successfully').prop('disabled', true);
                } else {
                    displayMessage('error', response.data.message);
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function() {
                displayMessage('error', 'An unknown server error occurred during data push.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.wp-rag-ai-chatbot-domain-notice .notice-dismiss', function () {
        console.log("here");
        $.post(
            acsecWpRagChatbotAdmin.ajaxurl,
            {
                action: 'acsec_chatbot_dismiss_domain_notice',
                nonce: acsecWpRagChatbotAdmin.noticeNonce
            }
        );
    });

});