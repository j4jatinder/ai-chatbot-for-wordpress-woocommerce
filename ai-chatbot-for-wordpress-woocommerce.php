<?php
/*
Plugin Name: AI Chatbot for WordPress & WooCommerce
Plugin URI: https://www.phpsoftsolutions.in/ai-chatbot-for-wordpress-woocommerce/
Description: An AI-powered chatbot for WordPress and WooCommerce using Retrieval-Augmented Generation (RAG). Train the chatbot on FAQs, pages, posts, and products, and answer customer queries using OpenAI or Gemini AI models.
Version: 1.0.0
Author: Jatinder Singh
Author URI: https://www.phpsoftsolutions.in
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-rag-ai-chatbot
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$is_localhost = in_array( $_SERVER['HTTP_HOST'], array( 'localhost','127.0.0.0.1', 'localhost:8000') ); 
// Define plugin constants
define( 'WP_RAG_AI_CHATBOT_VERSION', '1.0.0' );
define( 'WP_RAG_AI_CHATBOT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_RAG_AI_CHATBOT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// define('WP_RAG_AI_CHATBOT_NODE_URL',$is_localhost ? 'http://backend_env:5000': 'https://ragai.phpsoftsolutions.in'); // Node.js server URL
define('WP_RAG_AI_CHATBOT_NODE_URL', 'https://ragai.phpsoftsolutions.in'); // Node.js server URL

// Load the core classes
require_once WP_RAG_AI_CHATBOT_PLUGIN_DIR . 'includes/class-wp-rag-ai-chatbot-admin.php';
require_once WP_RAG_AI_CHATBOT_PLUGIN_DIR . 'includes/class-wp-rag-ai-chatbot-frontend.php';

// Include both parts
require_once plugin_dir_path(__FILE__) . 'includes/rag-rest-endpoint.php';
require_once plugin_dir_path(__FILE__) . 'includes/rag-frontend-chat.php';



/**
 * Initialize all classes.
 */
function wp_rag_ai_chatbot_init() {
    WP_RAG_AI_Chatbot_Admin::instance();
}
add_action( 'plugins_loaded', 'wp_rag_ai_chatbot_init' );
register_activation_hook( __FILE__, function () {
    // Register CPT before flushing
    WP_RAG_AI_Chatbot_Admin::instance()->register_faq_cpt();
    flush_rewrite_rules();
});
register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
});


?>