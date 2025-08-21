<?php
/**
 * Plugin Name: Clubs Manager - Quản lý câu lạc bộ bi-a
 * Plugin URI: https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin
 * Description: Plugin WordPress hoàn chỉnh để quản lý và hiển thị thông tin các câu lạc bộ bi-a với tính năng tìm kiếm nâng cao.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: clubs-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
 * Main Clubs Manager class
 */
class ClubsManager {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize plugin
     */
    private function init() {
        // Load text domain
        add_action('init', array($this, 'load_textdomain'));
        
        // Include required files
        $this->include_files();
        
        // Initialize components
        add_action('init', array($this, 'init_components'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Include required files
     */
    private function include_files() {
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-post-type.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-meta-boxes.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-admin.php';
        require_once CLUBS_MANAGER_PLUGIN_DIR . 'includes/class-clubs-frontend.php';
    }
    
    /**
     * Initialize components
     */
    public function init_components() {
        // Initialize post type
        Clubs_Post_Type::get_instance();
        
        // Initialize meta boxes
        Clubs_Meta_Boxes::get_instance();
        
        // Initialize admin
        Clubs_Admin::get_instance();
        
        // Initialize frontend
        Clubs_Frontend::get_instance();
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain('clubs-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'clubs-manager-frontend',
            CLUBS_MANAGER_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CLUBS_MANAGER_VERSION
        );
        
        wp_enqueue_script(
            'clubs-manager-frontend',
            CLUBS_MANAGER_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            CLUBS_MANAGER_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('clubs-manager-frontend', 'clubsManager', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('clubs_search_nonce'),
            'strings' => array(
                'searching' => __('Đang tìm kiếm...', 'clubs-manager'),
                'no_results' => __('Không tìm thấy câu lạc bộ nào.', 'clubs-manager'),
            )
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        if ($post_type === 'billiard-club' || strpos($hook, 'clubs-manager') !== false) {
            wp_enqueue_style(
                'clubs-manager-admin',
                CLUBS_MANAGER_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                CLUBS_MANAGER_VERSION
            );
            
            wp_enqueue_script(
                'clubs-manager-admin',
                CLUBS_MANAGER_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery', 'wp-media'),
                CLUBS_MANAGER_VERSION,
                true
            );
            
            // Localize script for admin
            wp_localize_script('clubs-manager-admin', 'clubsManagerAdmin', array(
                'nonce' => wp_create_nonce('clubs_admin_nonce'),
                'strings' => array(
                    'select_images' => __('Chọn hình ảnh', 'clubs-manager'),
                    'use_images' => __('Sử dụng hình ảnh', 'clubs-manager'),
                    'remove_image' => __('Xóa hình ảnh', 'clubs-manager'),
                )
            ));
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Flush rewrite rules to register new post type
        flush_rewrite_rules();
        
        // Create default terms for club-area taxonomy
        $default_areas = array(
            'Quận 1', 'Quận 2', 'Quận 3', 'Quận 4', 'Quận 5',
            'Quận 6', 'Quận 7', 'Quận 8', 'Quận 9', 'Quận 10',
            'Quận 11', 'Quận 12', 'Bình Thạnh', 'Phú Nhuận',
            'Tân Bình', 'Tân Phú', 'Gò Vấp', 'Thủ Đức'
        );
        
        foreach ($default_areas as $area) {
            if (!term_exists($area, 'club-area')) {
                wp_insert_term($area, 'club-area');
            }
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize the plugin
ClubsManager::get_instance();