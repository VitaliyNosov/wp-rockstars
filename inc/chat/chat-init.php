<?php
/**
 * Chat Module Initialization
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// 1. Carbon Fields Settings
require_once __DIR__ . '/chat-settings.php';

// 2. Register Custom Post Type and REST API 
require_once __DIR__ . '/chat-cpt.php';

// 3. Bot Logic Handler
require_once __DIR__ . '/chat-bot-handler.php';

// 4. Render Chat Widget HTML in footer
require_once __DIR__ . '/chat-widget.php';
