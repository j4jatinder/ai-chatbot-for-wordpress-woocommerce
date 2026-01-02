<?php
/**
 * Uninstall script for WP RAG AI Chatbot
 *
 * This file is executed automatically by WordPress when the plugin is deleted.
 * It cleans up all plugin-related options, custom post types, and user meta.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// ----------------------------
// 1. Delete Plugin Options
// ----------------------------
$plugin_options = [
    'wp_rag_ai_chatbot_site_id',
    'wp_rag_ai_chatbot_api_key',
    'wp_rag_ai_chatbot_keys_sent',
    'wp_rag_ai_chatbot_data_push_types',
    'wp_rag_ai_chatbot_active_provider',
    'wp_rag_ai_chatbot_openai_model',
    'wp_rag_ai_chatbot_gemini_model',
    'wp_rag_ai_chatbot_current_status',
    'wp_rag_ai_chatbot_policy_pages',
    'wp_rag_ai_chatbot_challenge_token_temp',
];

foreach ( $plugin_options as $option ) {
    delete_option( $option );
}

// ----------------------------
// 2. Delete user meta (notices, temporary data)
// ----------------------------
global $wpdb;
if ( current_user_can( 'manage_options' ) ) {
    // Setting $user_id to 0 and $delete_all to true removes the key for ALL users
    delete_metadata( 'user', 0, 'wp_rag_ai_chatbot_domain_notice_dismissed', '', true );
}



// ----------------------------
// 3. Delete CPT content (Optional: skip if testing)
// ----------------------------
$delete_cpts = false; // set false to skip CPT deletion
if ( $delete_cpts ) {
    $faq_posts = get_posts([
        'post_type'      => 'wp_rag_ai_chatbot_faq',
        'post_status'    => 'any',
        'numberposts'    => -1,
        'fields'         => 'ids',
    ]);

    foreach ( $faq_posts as $post_id ) {
        wp_delete_post( $post_id, true ); // true = force delete (bypass trash)
    }
}

// ----------------------------
// 4. Optional: Flush rewrite rules (if CPTs registered)
// ----------------------------
flush_rewrite_rules();
