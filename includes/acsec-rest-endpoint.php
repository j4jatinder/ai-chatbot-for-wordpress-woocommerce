<?php
/**
 * ACSEC Chat - REST API Endpoint
 * Defines /wp-json/acsec-chatbot/v1/chat endpoint and securely relays messages to Node server.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


add_action('rest_api_init', function () {
    // Existing endpoint for sending chat messages - PUBLIC endpoint for frontend chat functionality
    register_rest_route('acsec-chatbot/v1', '/api/chat/query', [
        'methods'  => 'POST',
        'callback' => 'acsec_handle_chat_rest',
        'permission_callback' => '__return_true', // Public endpoint - users send chat messages without authentication
    ]);

     /**
     * Registers a public, unauthenticated REST API route for the challenge-response.
     * This endpoint provides the challenge token for Node server verification.
     */
    register_rest_route( 'acsec-chatbot/v1', '/challenge-token', array(
        'methods'  => 'GET',
        'callback' =>  'acsec_get_challenge_token_rest' ,
        'permission_callback' => '__return_true', // Public endpoint - required for server-to-server verification
    ));

    // Endpoint for fetching chat history - PUBLIC endpoint for frontend chat functionality
    register_rest_route('acsec-chatbot/v1', '/api/messages', [
        'methods'  => 'GET',
        'callback' => 'acsec_rag_fetch_chat_history',
        'permission_callback' => '__return_true', // Public endpoint - users fetch their chat history without authentication
    ]);
});

    /**
     * REST API callback function that returns the stored challenge token.
     * This is the "proof of ownership" the Node server will check.
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
     function acsec_get_challenge_token_rest( WP_REST_Request $request ) {
        $challenge_token = get_option( 'acsec_chatbot_challenge_token_temp', false );

        if ( $challenge_token ) {
            // Return the token as plain text in the body for easy Node server verification
            return new WP_REST_Response( $challenge_token, 200 );
        }

        return new WP_REST_Response( 'Challenge token not active or found.', 404 );
    }


/**
 * Handle incoming chat messages from React frontend.
 */
function acsec_handle_chat_rest(WP_REST_Request $request) {

    // 1️⃣ Verify nonce for CSRF protection
    $nonce = $request->get_header('x-wp-nonce');
    $x_session_id = $request->get_header('x-session-id');
    if (empty($nonce) || !wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response(['error' => 'Invalid or missing nonce'], 403);
    }

    // 2️⃣ Extract and sanitize message
    $params  = $request->get_json_params();
    $message = sanitize_text_field($params['message'] ?? '');
    if (empty($message)) {
        return new WP_REST_Response(['error' => 'Message cannot be empty'], 400);
    }

    // 3️⃣ Fetch Node server configuration from options
    $node_url    = ACSEC_NODE_URL; //get_option('acsec_chatbot_node_url');
    $node_secret = get_option('acsec_chatbot_api_key');
    $node_api_url = $node_url.'/api/chat/query';
   

    if (empty($node_url) || empty($node_secret)) {
        return new WP_REST_Response(['error' => 'RAG Node configuration missing'], 500);
    }

    // 4️⃣ Forward the message to Node server
    $response = wp_remote_post($node_api_url, [
        'headers' => [
            'Content-Type'        => 'application/json',
            'x-site-key'=> $node_secret,
            'x-session-id'=> $x_session_id,
        ],
        'body'    => wp_json_encode(['message' => $message]),
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        return new WP_REST_Response([
            'error'   => 'Failed to contact Node server',
            'details' => $response->get_error_message(),
            'node_url' => $node_api_url,
        ], 500);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if ($body === null) {
        return new WP_REST_Response(['error' => 'Invalid JSON response from Node server'], 500);
    }

    // 5️⃣ Return Node’s response back to frontend
    return new WP_REST_Response($body, 200);
}

function acsec_rag_fetch_chat_history(WP_REST_Request $request) {

    $x_session_id = $request->get_header('x-session-id');
    $node_url    = ACSEC_NODE_URL;  //get_option('acsec_chatbot_node_url');
    $node_secret = get_option('acsec_chatbot_api_key');
    $node_api_url = $node_url.'/api/messages';
    

    // Optional: include a user identifier or token
    $user_id = get_current_user_id();

    $response = wp_remote_get($node_api_url . '?user_id=' . $user_id, [
        'timeout' => 10,
        'headers' => [
            'Accept' => 'application/json',
            'x-site-key'=> $node_secret,
            'x-session-id'=> $x_session_id,
        ],
    ]);

    if (is_wp_error($response)) {
        return new WP_REST_Response(['error' => 'Failed to contact Node server',
            'details' => $response->get_error_message(),
            'node_url' => $node_api_url
    ], 500);
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return new WP_REST_Response(['error' => 'Invalid JSON from Node server'], 500);
    }

    return new WP_REST_Response($data, 200);
}
