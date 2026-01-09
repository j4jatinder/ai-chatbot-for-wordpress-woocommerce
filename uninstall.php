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
$acsec_plugin_options = [
    'acsec_chatbot_site_id',
    'acsec_chatbot_api_key',
    'acsec_chatbot_keys_sent',
    'acsec_chatbot_data_push_types',
    'acsec_chatbot_active_provider',
    'acsec_chatbot_openai_model',
    'acsec_chatbot_gemini_model',
    'acsec_chatbot_current_status',
    'acsec_chatbot_policy_pages',
    'acsec_chatbot_challenge_token_temp',
];

foreach ( $acsec_plugin_options as $acsec_option ) {
    delete_option( $acsec_option );
}

// ----------------------------
// 2. Delete user meta (notices, temporary data)
// ----------------------------
global $wpdb;
if ( current_user_can( 'manage_options' ) ) {
    // Setting $user_id to 0 and $delete_all to true removes the key for ALL users
    delete_metadata( 'user', 0, 'acsec_chatbot_domain_notice_dismissed', '', true );
}



// ----------------------------
// 3. Delete CPT content (Optional: skip if testing)
// ----------------------------
$acsec_delete_cpts = false; // set false to skip CPT deletion
if ( $acsec_delete_cpts ) {
    $acsec_faq_posts = get_posts([
        'post_type'      => 'acsec_chatbot_faq',
        'post_status'    => 'any',
        'numberposts'    => -1,
        'fields'         => 'ids',
    ]);

    foreach ( $acsec_faq_posts as $post_id ) {
        wp_delete_post( $post_id, true ); // true = force delete (bypass trash)
    }
}

// ----------------------------
// 4. Optional: Flush rewrite rules (if CPTs registered)
// ----------------------------
flush_rewrite_rules();
