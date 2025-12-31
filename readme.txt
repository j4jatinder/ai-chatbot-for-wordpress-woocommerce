=== AI Chatbot for WordPress & WooCommerce ===
Contributors: phpsoftsol
Tags: ai chatbot, wordpress chatbot, woocommerce chatbot, ai assistant, live chat, chat widget, faq chatbot, product chatbot, gemini ai, openai, customer support, ai support
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.6
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Note: A publicly accessible HTTPS domain is required. Localhost and local development environments are not supported.

AI Chatbot for WordPress & WooCommerce adds an AI-powered chat assistant to your website using Retrieval-Augmented Generation (RAG).

The plugin allows site owners to train a chatbot on their own WordPress data — including FAQs, pages, posts, and WooCommerce products — and provide accurate, contextual answers to visitor questions using Gemini or OpenAI models.

Content is securely sent to an external AI processing service for embedding generation and chat responses.

An API token is required to enable AI functionality.

### Key Features

* Frontend chatbot widget with configurable position
* Supports Gemini and OpenAI AI models
* Retrieval-Augmented Generation (RAG) based answers
* Learn from FAQs, pages, posts, and WooCommerce products
* Manual content sync and embedding generation
* Email notification when AI training is completed
* HTTPS-only communication with the external service

== How It Works ==

1. Install and activate the plugin
2. Configure the chatbot position on the frontend
3. Enter AI provider details (Gemini or OpenAI API key and model)
4. Choose which content types to send for learning
5. Submit selected content for processing
6. Receive an email once embeddings are ready
7. Enable the chatbot on the frontend

== Supported Content Types ==

You can choose which data to send for AI learning:

* FAQs (required)
* Pages
* Posts
* WooCommerce products

Each content item is automatically truncated to a maximum of **1000 words** before processing.

== Usage Limits ==

The free service tier includes the following limits:

* Up to **1500 chat requests per day**
* Maximum **100 FAQs**
* Maximum **100 posts**
* Maximum **100 pages**
* Maximum **100 WooCommerce products**
* Total data size limited to **2 MB**

All limits are enforced by the external AI service.

== External Services ==

This plugin connects to an external service to process content, generate embeddings, and provide AI responses.

**Service Endpoint**
https://ragai.phpsoftsolutions.in

**Purpose**
* Store selected WordPress content
* Generate embeddings
* Process chat queries using AI models

**Data Sent**
* FAQ questions and answers
* Selected page, post, and product content
* Site identifier
* Selected AI provider and model

AI provider API keys are used only for request processing and are not exposed publicly.

**Service Provider**
PHPSOFT SOLUTIONS  
https://www.phpsoftsolutions.in/privacy-policy

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the Plugins menu
3. Open the plugin settings page
4. Configure AI provider credentials and chatbot options
5. Select content and submit for learning

== Frequently Asked Questions ==

= Does this plugin require an API key? =
Yes. A valid Gemini or OpenAI API key is required.

= Does this plugin work on localhost or local development environments? =
No. This plugin requires a publicly accessible domain with HTTPS enabled.
Localhost or local development URLs are not supported because the external AI service verifies the site domain during API registration.

= Can the chatbot work without sending content? =
No. Content embeddings are required to generate meaningful answers.

= Are there usage limits? =
Yes. The free tier includes daily request and content limits.

= Is WooCommerce supported? =
Yes. WooCommerce products can be used as a knowledge source.

= Is there a paid version? =
Additional features and higher limits may be offered in future versions.

== Screenshots ==
1. Plugin settings page
2. Content selection for AI learning
3. Chatbot widget on the frontend
4. Embedding status and notification

== Changelog ==

= 1.0.0 =
* Initial public release

== Upgrade Notice ==

= 1.0.0 =
Initial public release.
