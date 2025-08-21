<?php
/**
 * Plugin Name: Clubs Manager
 * Plugin URI: https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin
 * Description: WordPress plugin để quản lý câu lạc bộ bi-a với tính năng tìm kiếm, bản đồ và quản trị hoàn chỉnh.
 * Version: 1.0.0
 * Author: sonhsgvn-bit
 * Text Domain: clubs-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 *
 * @package ClubsManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CLUBS_MANAGER_VERSION', '1.0.0');
define('CLUBS_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLUBS_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CLUBS_MANAGER_PLUGIN_FILE', __FILE__);

/**
 * Main plugin class
 */
class ClubsManager {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Include required files
        $this->includes();
        
        // Initialize components
        $this->init_components();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-post-type.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-meta-boxes.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-admin.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-frontend.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-shortcodes.php';
    }
    
    /**
     * Initialize components
     */
    private function init_components() {
        new Clubs_Post_Type();
        new Clubs_Meta_Boxes();
        new Clubs_Admin();
        new Clubs_Frontend();
        new Clubs_Shortcodes();
        
        // Add theme support for post thumbnails if not already supported
        add_theme_support('post-thumbnails');
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Add template include filter
        add_filter('template_include', array($this, 'template_include'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('clubs-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Template include filter
     */
    public function template_include($template) {
        if (is_singular('billiard_club')) {
            $plugin_template = CLUBS_MANAGER_PLUGIN_DIR . 'templates/single-billiard_club.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        if (is_post_type_archive('billiard_club')) {
            $plugin_template = CLUBS_MANAGER_PLUGIN_DIR . 'templates/archive-billiard_club.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        if (is_tax('club_area')) {
            $plugin_template = CLUBS_MANAGER_PLUGIN_DIR . 'templates/taxonomy-club_area.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
}

/**
 * Plugin activation hook
 */
function clubs_manager_activate() {
    // Register post type and taxonomy
    require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-post-type.php';
    $post_type = new Clubs_Post_Type();
    $post_type->register_post_type();
    $post_type->register_taxonomy();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'clubs_manager_activate');

/**
 * Plugin deactivation hook
 */
function clubs_manager_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'clubs_manager_deactivate');

/**
 * Initialize the plugin
 */
function clubs_manager_init() {
    new ClubsManager();
}
add_action('plugins_loaded', 'clubs_manager_init');