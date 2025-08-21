<?php
/**
 * Plugin Name: Clubs Manager
 * Description: A plugin to manage clubs with features like custom post types, meta boxes, AJAX search, and Google Maps integration.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define constants
define( 'CLUBS_MANAGER_DIR', plugin_dir_path( __FILE__ ) );
define( 'CLUBS_MANAGER_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once CLUBS_MANAGER_DIR . 'includes/class-club-post-type.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-meta-boxes.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-admin.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-shortcode.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-ajax.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-maps.php';

// Initialize the plugin
function clubs_manager_init() {
    Club_Post_Type::register_post_type();
    Club_Meta_Boxes::register_meta_boxes();
    Club_Admin::init();
    Club_Shortcode::init();
    Club_Ajax::init();
    Club_Maps::init();
}

add_action( 'init', 'clubs_manager_init' );
