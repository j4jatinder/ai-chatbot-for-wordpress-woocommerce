<?php
class WP_RAG_AI_Chatbot_Frontend {
    // ... (Singleton pattern here) ...
    /**
     * The single instance of the class.
     *
     * @var WP_RAG_AI_Chatbot_Admin
     */
    protected static $instance = null;

    /**
     * Main WP_RAG_AI_Chatbot_Admin Instance.
     *
     * Ensures only one instance of the admin class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return WP_RAG_AI_Chatbot_Admin - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_footer', array( $this, 'render_chatbot_container' ) );
        add_shortcode( 'wp_rag_chatbot', array( $this, 'render_chatbot_container' ) );
    }

    public function enqueue_scripts() {
        // Enqueue your compiled React application (chatbot-app.js) and styles
        wp_enqueue_style( 'wp-rag-ai-chatbot-style', WP_RAG_AI_CHATBOT_PLUGIN_URL . 'assets/css/chatbot-styles.css', array(), WP_RAG_AI_CHATBOT_VERSION );
        wp_enqueue_script( 'wp-rag-ai-chatbot-app', WP_RAG_AI_CHATBOT_PLUGIN_URL . 'assets/js/chatbot-app.js', array( 'jquery' ), WP_RAG_AI_CHATBOT_VERSION, true );

        // Pass configuration data to your React app
        $config = array(
            'position' => get_option( 'wp_rag_ai_chatbot_chat_position', 'right' ),
            'nodeUrl'  => WP_RAG_AI_CHATBOT_NODE_URL, //get_option( 'wp_rag_ai_chatbot_node_url' ),
            'siteId'   => get_option( 'wp_rag_ai_chatbot_site_id' ), // Site ID is crucial for chat API calls
        );

        wp_localize_script( 'wp-rag-ai-chatbot-app', 'wpRagAiChatbotConfig', $config );
    }

    public function render_chatbot_container() {
        $chat_position = get_option( 'wp_rag_ai_chatbot_chat_position', 'right' );

        // This div is the mount point for your React app
        echo '<div id="wp-rag-ai-chatbot-root" class="position-' . esc_attr( $chat_position ) . '">Ta da I ma at footer</div>';
    }
}