<?php
/**
 * Custom Post Type for Billiard Clubs
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Post_Type {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomy'));
        add_filter('single_template', array($this, 'load_single_template'));
        add_filter('archive_template', array($this, 'load_archive_template'));
    }
    
    /**
     * Register the billiard-club post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => __('Câu lạc bộ bi-a', 'clubs-manager'),
            'singular_name'         => __('Câu lạc bộ', 'clubs-manager'),
            'menu_name'             => __('Câu lạc bộ bi-a', 'clubs-manager'),
            'name_admin_bar'        => __('Câu lạc bộ', 'clubs-manager'),
            'archives'              => __('Danh sách câu lạc bộ', 'clubs-manager'),
            'attributes'            => __('Thuộc tính câu lạc bộ', 'clubs-manager'),
            'parent_item_colon'     => __('Câu lạc bộ cha:', 'clubs-manager'),
            'all_items'             => __('Tất cả câu lạc bộ', 'clubs-manager'),
            'add_new_item'          => __('Thêm câu lạc bộ mới', 'clubs-manager'),
            'add_new'               => __('Thêm mới', 'clubs-manager'),
            'new_item'              => __('Câu lạc bộ mới', 'clubs-manager'),
            'edit_item'             => __('Sửa câu lạc bộ', 'clubs-manager'),
            'update_item'           => __('Cập nhật câu lạc bộ', 'clubs-manager'),
            'view_item'             => __('Xem câu lạc bộ', 'clubs-manager'),
            'view_items'            => __('Xem câu lạc bộ', 'clubs-manager'),
            'search_items'          => __('Tìm câu lạc bộ', 'clubs-manager'),
            'not_found'             => __('Không tìm thấy', 'clubs-manager'),
            'not_found_in_trash'    => __('Không tìm thấy trong thùng rác', 'clubs-manager'),
            'featured_image'        => __('Ảnh đại diện', 'clubs-manager'),
            'set_featured_image'    => __('Đặt ảnh đại diện', 'clubs-manager'),
            'remove_featured_image' => __('Xóa ảnh đại diện', 'clubs-manager'),
            'use_featured_image'    => __('Sử dụng làm ảnh đại diện', 'clubs-manager'),
            'insert_into_item'      => __('Chèn vào câu lạc bộ', 'clubs-manager'),
            'uploaded_to_this_item' => __('Đã tải lên câu lạc bộ này', 'clubs-manager'),
            'items_list'            => __('Danh sách câu lạc bộ', 'clubs-manager'),
            'items_list_navigation' => __('Điều hướng danh sách', 'clubs-manager'),
            'filter_items_list'     => __('Lọc danh sách', 'clubs-manager'),
        );
        
        $args = array(
            'label'                 => __('Câu lạc bộ bi-a', 'clubs-manager'),
            'description'           => __('Quản lý thông tin các câu lạc bộ bi-a', 'clubs-manager'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
            'taxonomies'            => array('club-area'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-games',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'clubs',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array(
                'slug' => 'club',
                'with_front' => false,
            ),
        );
        
        register_post_type('billiard-club', $args);
    }
    
    /**
     * Register the club-area taxonomy
     */
    public function register_taxonomy() {
        $labels = array(
            'name'                       => __('Khu vực', 'clubs-manager'),
            'singular_name'              => __('Khu vực', 'clubs-manager'),
            'menu_name'                  => __('Khu vực', 'clubs-manager'),
            'all_items'                  => __('Tất cả khu vực', 'clubs-manager'),
            'parent_item'                => __('Khu vực cha', 'clubs-manager'),
            'parent_item_colon'          => __('Khu vực cha:', 'clubs-manager'),
            'new_item_name'              => __('Tên khu vực mới', 'clubs-manager'),
            'add_new_item'               => __('Thêm khu vực mới', 'clubs-manager'),
            'edit_item'                  => __('Sửa khu vực', 'clubs-manager'),
            'update_item'                => __('Cập nhật khu vực', 'clubs-manager'),
            'view_item'                  => __('Xem khu vực', 'clubs-manager'),
            'separate_items_with_commas' => __('Phân cách các khu vực bằng dấu phẩy', 'clubs-manager'),
            'add_or_remove_items'        => __('Thêm hoặc xóa khu vực', 'clubs-manager'),
            'choose_from_most_used'      => __('Chọn từ khu vực thường dùng', 'clubs-manager'),
            'popular_items'              => __('Khu vực phổ biến', 'clubs-manager'),
            'search_items'               => __('Tìm khu vực', 'clubs-manager'),
            'not_found'                  => __('Không tìm thấy', 'clubs-manager'),
            'no_terms'                   => __('Không có khu vực', 'clubs-manager'),
            'items_list'                 => __('Danh sách khu vực', 'clubs-manager'),
            'items_list_navigation'      => __('Điều hướng danh sách', 'clubs-manager'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array(
                'slug' => 'khu-vuc',
                'with_front' => false,
            ),
        );
        
        register_taxonomy('club-area', array('billiard-club'), $args);
    }
    
    /**
     * Load custom single template
     */
    public function load_single_template($template) {
        global $post;
        
        if ($post->post_type === 'billiard-club') {
            $plugin_template = CLUBS_MANAGER_PLUGIN_DIR . 'templates/single-club.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Load custom archive template
     */
    public function load_archive_template($template) {
        if (is_post_type_archive('billiard-club') || is_tax('club-area')) {
            $plugin_template = CLUBS_MANAGER_PLUGIN_DIR . 'templates/archive-clubs.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
}