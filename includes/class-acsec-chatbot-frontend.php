<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ACSEC_Chatbot_Frontend {
    // ... (Singleton pattern here) ...
    /**
     * The single instance of the class.
     *
     * @var ACSEC_Chatbot_Frontend
     */
    protected static $instance = null;

    /**
     * Main ACSEC_Chatbot_Frontend Instance.
     *
     * Ensures only one instance of the admin class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return ACSEC_Chatbot_Frontend - Main instance.
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
        add_shortcode( 'acsec_chatbot', array( $this, 'render_chatbot_container' ) );
    }

    public function enqueue_scripts() {
        // Enqueue your compiled React application (chatbot-app.js) and styles
        wp_enqueue_style( 'acsec-chatbot-style', ACSEC_PLUGIN_URL . 'assets/css/chatbot-styles.css', array(), ACSEC_VERSION );
        wp_enqueue_script( 'acsec-chatbot-app', ACSEC_PLUGIN_URL . 'assets/js/chatbot-app.js', array( 'jquery' ), ACSEC_VERSION, true );

        // Pass configuration data to your React app
        $config = array(
            'position' => get_option( 'acsec_chatbot_chat_position', 'right' ),
            'nodeUrl'  => ACSEC_NODE_URL, //get_option( 'acsec_chatbot_node_url' ),
            'siteId'   => get_option( 'acsec_chatbot_site_id' ), // Site ID is crucial for chat API calls
        );

        wp_localize_script( 'acsec-chatbot-app', 'wpRagAiChatbotConfig', $config );
    }

    public function render_chatbot_container() {
        $chat_position = get_option( 'acsec_chatbot_chat_position', 'right' );

        // This div is the mount point for your React app
        echo '<div id="wp-rag-ai-chatbot-root" class="position-' . esc_attr( $chat_position ) . '">Ta da I ma at footer</div>';
    }
}