<?php
/**
 * Plugin Name: Clubs Manager - Quản lý câu lạc bộ bi-a
 * Plugin URI: https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin
 * Description: Plugin WordPress hoàn chỉnh cho quản lý câu lạc bộ bi-a với tìm kiếm nâng cao, Google Maps và giao diện responsive.
 * Version: 1.0.0
 * Author: Clubs Manager Team
 * Text Domain: clubs-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define constants
define( 'CLUBS_MANAGER_VERSION', '1.0.0' );
define( 'CLUBS_MANAGER_DIR', plugin_dir_path( __FILE__ ) );
define( 'CLUBS_MANAGER_URL', plugin_dir_url( __FILE__ ) );
define( 'CLUBS_MANAGER_BASENAME', plugin_basename( __FILE__ ) );

// Include necessary files
require_once CLUBS_MANAGER_DIR . 'includes/class-club-post-type.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-meta-boxes.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-admin.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-frontend.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-shortcode.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-ajax.php';
require_once CLUBS_MANAGER_DIR . 'includes/class-club-maps.php';

/**
 * Main ClubsManager class
 */
class ClubsManager {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        
        // Activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        Club_Post_Type::register_post_type();
        Club_Meta_Boxes::register_meta_boxes();
        Club_Admin::init();
        Club_Frontend::init();
        Club_Shortcode::init();
        Club_Ajax::init();
        Club_Maps::init();
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'clubs-manager', false, dirname( CLUBS_MANAGER_BASENAME ) . '/languages' );
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style( 'clubs-manager-frontend', CLUBS_MANAGER_URL . 'assets/css/frontend.css', array(), CLUBS_MANAGER_VERSION );
        wp_enqueue_script( 'clubs-manager-frontend', CLUBS_MANAGER_URL . 'assets/js/frontend.js', array( 'jquery' ), CLUBS_MANAGER_VERSION, true );
        
        // Localize script for AJAX
        wp_localize_script( 'clubs-manager-frontend', 'clubs_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'clubs_ajax_nonce' ),
        ) );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts( $hook ) {
        global $post;
        
        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            if ( 'billiard-club' === $post->post_type ) {
                wp_enqueue_style( 'clubs-manager-admin', CLUBS_MANAGER_URL . 'assets/css/admin.css', array(), CLUBS_MANAGER_VERSION );
                wp_enqueue_script( 'clubs-manager-admin', CLUBS_MANAGER_URL . 'assets/js/admin.js', array( 'jquery' ), CLUBS_MANAGER_VERSION, true );
                wp_enqueue_media();
            }
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        add_option( 'clubs_manager_version', CLUBS_MANAGER_VERSION );
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create custom database tables
     */
    private function create_tables() {
        // Tables will be created here if needed for future features
    }
}

// Initialize the plugin
new ClubsManager();
