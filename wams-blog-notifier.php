<?php
/**
 * Plugin Name: WAMS Blog Notifier
 * Description: Automatically notify subscribers via WhatsApp when a new blog post is published.
 * Version: 1.0.0
 * Author: Zain Sardar
 * Author URI: https://wa.me/923246270322
 * Plugin URI: https://wams.aztify.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Constants
define( 'WAMS_BN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WAMS_BN_URL', plugin_dir_url( __FILE__ ) );

// Include Files
require_once WAMS_BN_PATH . 'includes/database.php';
require_once WAMS_BN_PATH . 'includes/admin-settings.php';
require_once WAMS_BN_PATH . 'includes/api-client.php';
require_once WAMS_BN_PATH . 'includes/shortcodes.php';
require_once WAMS_BN_PATH . 'includes/notifier.php';
require_once WAMS_BN_PATH . 'includes/logger.php';

// Activation Hook
register_activation_hook( __FILE__, 'wams_blog_notifier_activate' );

function wams_blog_notifier_activate() {
	wams_bn_create_database_table();
}
