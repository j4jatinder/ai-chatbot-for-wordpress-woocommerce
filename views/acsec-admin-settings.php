<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Admin Settings View for the WP RAG AI Chatbot Plugin.
 *
 * This file contains the HTML, CSS, and JavaScript for the settings page.
 * It is included by ACSEC_Chatbot_Admin::settings_page_html().
 */
?>
<div class="wrap">
    <h1><?php esc_html_e( 'AI Chatbot for Support & E-Commerce', 'ai-chatbot-for-support-e-commerce' ); ?></h1>

    <div id="rag-admin-message" class="notice" style="display:none;">
        <p></p>
    </div>

    <form method="post" action="options.php">
        <?php
        // Security fields and hidden settings fields
        settings_fields( 'acsec-chatbot-group_static' );
        // The main section defined in settings_init()
        do_settings_sections( 'acsec-chatbot-settings' );
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="acsec_chatbot_chat_position"><?php esc_html_e( 'Chat Position', 'ai-chatbot-for-support-e-commerce' ); ?></label></th>
                <td>
                    <?php $acsec_chat_position = get_option( 'acsec_chatbot_chat_position', 'bottom-right' ); ?>
                    <select name="acsec_chatbot_chat_position" id="acsec_chatbot_chat_position">
                        <option value="bottom-right" <?php selected( $acsec_chat_position, 'bottom-right' ); ?>>Bottom Right</option>
                        <option value="bottom-left" <?php selected( $acsec_chat_position, 'bottom-left' ); ?>>Bottom Left</option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Where the chatbot icon will appear on the frontend.', 'ai-chatbot-for-support-e-commerce' ); ?></p>
                </td>
            </tr>
        </table>
        <?php submit_button( __( 'Save Display Settings', 'ai-chatbot-for-support-e-commerce' ) ); ?>
    </form>

    <hr>

    <h2><?php esc_html_e( 'Site Registration Status', 'ai-chatbot-for-support-e-commerce' ); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e( 'Site ID', 'ai-chatbot-for-support-e-commerce' ); ?></th>
            <td>
                <code id="site-id-display"><?php echo esc_html( get_option( 'acsec_chatbot_site_id', 'N/A' ) ); ?></code>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'API Key', 'ai-chatbot-for-support-e-commerce' ); ?></th>
            <td>
                <code id="api-key-display"><?php echo esc_html( get_option( 'acsec_chatbot_api_key', 'N/A' ) ); ?></code>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Registration Status', 'ai-chatbot-for-support-e-commerce' ); ?></th>
            <td>
                <button id="site-register-button" class="button button-primary" <?php echo get_option( 'acsec_chatbot_api_key' ) ? 'disabled' : ''; ?>>
                    <?php echo get_option( 'acsec_chatbot_api_key' ) ? 'Site Registered' : '1. Register Site'; ?>
                </button>
                <p class="description"><?php esc_html_e( 'Registers this WordPress site with the RAG Node to get the Site ID and API Key.', 'ai-chatbot-for-support-e-commerce' ); ?></p>
            </td>
        </tr>
    </table>

    <hr>

    <h2><?php esc_html_e( 'AI Provider Configuration', 'ai-chatbot-for-support-e-commerce' ); ?></h2>
    <p class="description">Enter your AI keys and configure the active model. Keys are immediately sent to the RAG Node and **NOT** stored in this WordPress database.</p>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="active_provider"><?php esc_html_e( 'Active AI Provider', 'ai-chatbot-for-support-e-commerce' ); ?></label></th>
            <td>
                <?php $acsec_active_provider = get_option( 'acsec_chatbot_active_provider', 'gemini' ); ?>
                <select id="active_provider">
                    <option value="gemini" <?php selected( $acsec_active_provider, 'gemini' ); ?>>Google Gemini</option>
                    <option value="openai" <?php selected( $acsec_active_provider, 'openai' ); ?>>OpenAI</option>
                </select>
                <p class="description"><?php esc_html_e( 'Select the primary provider the chatbot should use.', 'ai-chatbot-for-support-e-commerce' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="openai_key">OpenAI API Key</label></th>
            <td><input type="password" id="openai_key" class="regular-text" placeholder="sk-..." value="">
        <p class="hint">We are not storing theses keys into DB, its directly going to Chat Server.</p>
    <a href="https://platform.openai.com/api-keys"
		   target="_blank"
		   rel="noopener noreferrer">
			<?php esc_html_e( 'Get OpenAI API Key', 'ai-chatbot-for-support-e-commerce' ); ?>
		</a>
    </td>
        </tr>
        <tr>
            <th scope="row"><label for="openai_model">OpenAI Model</label></th>
            <td>
                <?php $acsec_openai_model = get_option( 'acsec_chatbot_openai_model', 'gpt-5-nano' ); ?>
                <input type="text" id="openai_model" value="<?php echo esc_attr( $acsec_openai_model ); ?>" class="regular-text" placeholder="gpt-5-nano" />
                <p class="description"><?php esc_html_e( 'Specify the OpenAI model to use (e.g., gpt-5-nano or gpt-5-mini).', 'ai-chatbot-for-support-e-commerce' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="gemini_key">Gemini API Key</label></th>
            <td><input type="password" id="gemini_key" class="regular-text" placeholder="AIza..." value="">
        <p class="hint">We are not storing theses keys into DB, its directly going to Chat Server.</p>
    <a href="https://aistudio.google.com/app/apikey"
		   target="_blank"
		   rel="noopener noreferrer">
			<?php esc_html_e( 'Get Google Gemini API Key', 'ai-chatbot-for-support-e-commerce' ); ?>
		</a>
    </td>
        </tr>
        <tr>
            <th scope="row"><label for="gemini_model">Gemini Model</label></th>
            <td>
                <?php $acsec_gemini_model = get_option( 'acsec_chatbot_gemini_model', 'gemini-2.5-flash-lite' ); ?>
                <input type="text" id="gemini_model" value="<?php echo esc_attr( $acsec_gemini_model ); ?>" class="regular-text" placeholder="gemini-2.5-flash-lite" />
                <p class="description"><?php esc_html_e( 'Specify the Gemini model to use (e.g., gemini-2.5-flash-lite).', 'ai-chatbot-for-support-e-commerce' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row"><?php esc_html_e( 'Key & Config Action', 'ai-chatbot-for-support-e-commerce' ); ?></th>
            <td>
                <?php $acsec_keys_sent_time = get_option( 'acsec_chatbot_keys_sent' ); ?>
                <button id="send-keys-button" class="button button-secondary" <?php echo empty( get_option( 'acsec_chatbot_api_key' ) ) ? 'disabled' : ''; ?>>
                    <?php esc_html_e( '2. Save AI Settings & Verify', 'ai-chatbot-for-support-e-commerce' ); ?>
                </button>
                <p class="description">
                    <?php
                    if ( $acsec_keys_sent_time ) {
                        echo 'Last successful config/key transmission: ' . esc_html( human_time_diff( $acsec_keys_sent_time ) ) . ' ago.';
                    } else {
                        esc_html_e( 'Keys and configuration have not been sent yet.', 'ai-chatbot-for-support-e-commerce' );
                    }
                    ?>
                </p>
            </td>
        </tr>
    </table>

    <hr>

    <h2><?php esc_html_e( 'Content Indexing (RAG)', 'ai-chatbot-for-support-e-commerce' ); ?></h2>
    <div class="form-control">
        <?php 
        foreach([ 'pages' => 'Pages(Selected Above)', 'faqs' => 'AI Chatbot FAQs','posts' => 'Posts(With tag "AI Chatbot Content")','products' => 'Products'] as $type => $acsec_label): ?>

            <div>
                <input type="checkbox" id="data_push_<?php echo esc_attr( $type ); ?>" name="acsec_chatbot_data_push_types[]" value="<?php echo esc_attr( $type ); ?>" <?php checked( in_array( $type, (array) get_option( 'acsec_chatbot_data_push_types', [] ) ) ); ?> />
                <label for="data_push_<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $acsec_label ); ?></label>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="description"><?php esc_html_e( 'Push all selected WordPress content (Posts, Pages, Products, FAQs) to the RAG Node for indexing.', 'ai-chatbot-for-support-e-commerce' ); ?></p>
    <button id="data-push-button" class="button button-hero" <?php echo empty( get_option( 'acsec_chatbot_api_key' ) ) ? 'disabled' : ''; ?>>
        3. Index Selected Content/Data Now
    </button>

</div>

<?php
