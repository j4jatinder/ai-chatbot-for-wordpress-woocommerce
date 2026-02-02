<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('rest_api_init', function () {
    


    // 1. Send Chat Messages (Public)
    register_rest_route('acsec-chatbot/v1', '/api/chat/query', [
        'methods'             => 'POST',
        'callback'            => 'acsec_handle_chat_rest',
        'permission_callback' => '__return_true',
    ]);

    // 2. Fetch Chat History (Public)
    register_rest_route('acsec-chatbot/v1', '/api/messages', [
        'methods'             => 'GET',
        'callback'            => 'acsec_rag_fetch_chat_history',
        'permission_callback' => '__return_true',
    ]);

    // 3. Challenge Token Fetch(Restricted)
    register_rest_route( 'acsec-chatbot/v1', '/challenge-token', array(
        'methods'             => 'GET',
        'callback'            => 'acsec_get_challenge_token_rest',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }, 
    ));
});


/**
 * Handle incoming chat messages
 */
function acsec_handle_chat_rest(WP_REST_Request $request) {
    // Sanitize all inputs
    $x_session_id = sanitize_text_field($request->get_header('x-session-id'));
    $params       = $request->get_json_params();
    $message      = sanitize_text_field($params['message'] ?? '');

    if (empty($message)) {
        return new WP_REST_Response(['error' => 'Message cannot be empty'], 400);
    }

    $node_url     = ACSEC_NODE_URL;
    $node_secret  = get_option('acsec_chatbot_api_key');
    $node_api_url = $node_url . '/api/chat/query';

    if (empty($node_url) || empty($node_secret)) {
        return new WP_REST_Response(['error' => 'Configuration missing'], 500);
    }

    $response = wp_remote_post($node_api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'x-site-key'   => $node_secret,
            'x-session-id' => $x_session_id,
        ],
        'body'    => wp_json_encode(['message' => $message]),
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        return new WP_REST_Response(['error' => 'Connection failed'], 500);
    }

    return new WP_REST_Response(json_decode(wp_remote_retrieve_body($response), true), 200);
}

/**
 * Fetch Chat History
 */
function acsec_rag_fetch_chat_history(WP_REST_Request $request) {
    // Sanitize Header
    $x_session_id = sanitize_text_field($request->get_header('x-session-id'));
    
    if (empty($x_session_id)) {
        return new WP_REST_Response(['error' => 'Session ID required'], 400);
    }

    $node_url    = ACSEC_NODE_URL;
    $node_secret = get_option('acsec_chatbot_api_key');
    $node_api_url = $node_url . '/api/messages';

    $response = wp_remote_get(add_query_arg('user_id', $x_session_id, $node_api_url), [
        'timeout' => 10,
        'headers' => [
            'Accept'       => 'application/json',
            'x-site-key'   => $node_secret,
            'x-session-id' => $x_session_id,
        ],
    ]);

    if (is_wp_error($response)) {
        return new WP_REST_Response(['error' => 'Connection failed'], 500);
    }

    return new WP_REST_Response(json_decode(wp_remote_retrieve_body($response), true), 200);
}


function acsec_get_challenge_token_rest( WP_REST_Request $request ) {

    $token = get_option( 'acsec_chatbot_challenge_token_temp' );

    if ( ! $token ) {
        return new WP_REST_Response( 'Not found', 404 );
    }

    delete_option( 'acsec_chatbot_challenge_token_temp' );

    return new WP_REST_Response( $token, 200 );
}
