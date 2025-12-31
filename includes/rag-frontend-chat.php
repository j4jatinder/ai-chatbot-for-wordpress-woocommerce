<?php
/**
 * RAG AI Chat - Frontend Embed
 * Registers shortcode [RAG_AI_CHATBOT], enqueues React app, and provides REST endpoint + nonce to JS.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin Frontend Shortcode for RAG Chatbot
 * Shortcode: [RAG_AI_CHATBOT]
 */

add_shortcode('RAG_AI_CHATBOT', 'rag_ai_chatbot_shortcode');

function rag_ai_chatbot_shortcode() {

    $chatbot_title= get_option( 'wp_rag_ai_chatbot_chatbot_title', 'AI Chatbot' );

     $status_value = get_option( 'wp_rag_ai_chatbot_current_status', '0' ); 
     if(!$status_value){
        return false;
     }
    $chat_position = get_option( 'wp_rag_ai_chatbot_chat_position', 'right' );
    ob_start();
    ?>

    <!-- 🧠 Chatbot Popup Structure -->
    <div id="rag-chatbot-wrapper" class="rag-chatbot-<?php echo esc_attr($chat_position); ?>">
        <!-- Floating Button -->
        <button id="rag-chatbot-button" class="rag-chatbot-button">
            💬 <?= $chatbot_title;?>
        </button>

        <!-- Modal -->
        <div id="rag-chatbot-modal" class="rag-chatbot-modal">
            <div class="rag-chatbot-content">
                <button id="rag-chatbot-close" class="rag-chatbot-close">&times;</button>
                <div id="rag-chatbot-app">
                    <!-- React app will render here -->
                     <?php render_rag_chatbot_container(); ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Floating Chat Button */
        .rag-chatbot-button {
            position: fixed;
            bottom: 20px;
            right: 25px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 14px 20px;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 9999;
            transition: all 0.3s ease;
        }
        .rag-chatbot-button:hover {
            background-color: #4338ca;
        }

        
        /* Modal background */
        .rag-chatbot-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        /* Right Position */
        .rag-chatbot-bottom-right .rag-chatbot-button {
            right: 25px; /* Aligns to the right */
            left: auto;
        }

        /* Left Position */
        .rag-chatbot-bottom-left .rag-chatbot-button {
            left: 25px; /* Aligns to the left */
            right: auto;
        }

        /* Right Position Modal */
        .rag-chatbot-bottom-right .rag-chatbot-content {
            right: 25px; /* Aligns the modal to the right side of the screen */
            left: auto;
        }

        /* Left Position Modal */
        .rag-chatbot-bottom-left .rag-chatbot-content {
            left: 25px; /* Aligns the modal to the left side of the screen */
            right: auto;
        }

        /* Modal content */
        .rag-chatbot-content {
            position: fixed;
            margin: 50px auto;
            padding: 0;
            background: #fff;
            width: 90%;
            max-width: 420px;
            height: 80%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            /* Add transition for smooth opening */
            transition: all 0.3s ease-in-out; 
        }

        /* Close button */
        .rag-chatbot-close {
            position: absolute;
            right: 10px;
            top: 10px;
            background: transparent;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #333;
            z-index: 10;
        }

        /* Chat area */
        #rag-chatbot-app {
            flex: 1;
            height: 100%;
            overflow: hidden;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('rag-chatbot-modal');
        const openBtn = document.getElementById('rag-chatbot-button');
        const closeBtn = document.getElementById('rag-chatbot-close');

        openBtn.addEventListener('click', () => modal.style.display = 'block');
        closeBtn.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    });
    </script>

    <?php
    return ob_get_clean();
}



function render_rag_chatbot_container() {
        $chat_position = get_option( 'wp_rag_ai_chatbot_chat_position', 'right' );

        // This div is the mount point for your React app
        echo '<div id="rag-ai-chatbot-root" class="position-' . esc_attr( $chat_position ) . '"></div>';
   
}   

function rag_ai_chatbot_render_in_footer() {
    echo do_shortcode('[RAG_AI_CHATBOT]');
}
add_action( 'wp_footer', 'rag_ai_chatbot_render_in_footer'  );

function set_aichat_session_cookie() {
    $cookie_name = 'aichat_session_id';

    if (!isset($_COOKIE[$cookie_name])) {
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $unique_id = wp_generate_password(10, false);
        $full_id = "session_{$user_id}_{$unique_id}";

        // Set expiration to the end of the current day (midnight)
        $expire = strtotime('tomorrow');

        setcookie($cookie_name, $full_id, $expire, COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE[$cookie_name] = $full_id; // Set for immediate use
        return sanitize_text_field($_COOKIE[$cookie_name]);
    }
    return sanitize_text_field($_COOKIE[$cookie_name]);

}
function get_aichat_session_id() {
    if (isset($_COOKIE['aichat_session_id'])) {
        // Sanitize the cookie value before using it
        return sanitize_text_field($_COOKIE['aichat_session_id']);
    }

    return set_aichat_session_cookie();
}


/**
 * 2️⃣ Enqueue React app and expose REST endpoint & nonce
 */
add_action('wp_enqueue_scripts', function () {
    $plugin_dir = WP_RAG_AI_CHATBOT_PLUGIN_DIR;
    $plugin_url = WP_RAG_AI_CHATBOT_PLUGIN_URL;

    $main_js_file_name='chat-app-build/assets/index-BnRv7LeP.js';
    $main_css_file_name='chat-app-build/assets/index-BxZlaAnu.css';

    $react_js   = $plugin_dir . $main_js_file_name;
    $react_css  = $plugin_dir . $main_css_file_name;

    // Enqueue CSS if exists
    if (file_exists($react_css)) {
        wp_enqueue_style(
            'rag-chat-style',
            $plugin_url .  $main_css_file_name,
            [],
            filemtime($react_css)
        );
    }

    // Enqueue JS if exists
    if (file_exists($react_js)) {
        wp_enqueue_script(
            'rag-chat-script',
            $plugin_url . $main_js_file_name,
            [],
            filemtime($react_js),
            true
        );

        // Localize nonce & REST endpoint for React app
        wp_localize_script('rag-chat-script', 'RAG_AI_CHAT', [
            'wp_server_url'=>esc_url_raw(rest_url('wp-rag-ai-chatbot/v1/')),
            'rest_send_url' => esc_url_raw(rest_url('wp-rag-ai-chatbot/v1/chat/query')),
            'rest_fetch_url'=> esc_url_raw(rest_url('wp-rag-ai-chatbot/v1/mesages')),
            'nonce'    => wp_create_nonce('wp_rest'),
            'session_id' => get_aichat_session_id(),
            'chatbot_title' => get_option( 'wp_rag_ai_chatbot_chatbot_title', 'AI Chatbot' ),
        ]);
    }
});


