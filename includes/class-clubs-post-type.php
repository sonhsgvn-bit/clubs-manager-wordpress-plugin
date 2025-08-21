<?php
/**
 * Custom Post Type and Taxonomy Registration
 *
 * @package ClubsManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Post_Type {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomy'));
    }
    
    /**
     * Register billiard_club custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Câu lạc bộ', 'Post type general name', 'clubs-manager'),
            'singular_name'         => _x('Câu lạc bộ', 'Post type singular name', 'clubs-manager'),
            'menu_name'            => _x('Câu lạc bộ', 'Admin Menu text', 'clubs-manager'),
            'name_admin_bar'       => _x('Câu lạc bộ', 'Add New on Toolbar', 'clubs-manager'),
            'add_new'              => __('Thêm mới', 'clubs-manager'),
            'add_new_item'         => __('Thêm câu lạc bộ mới', 'clubs-manager'),
            'new_item'             => __('Câu lạc bộ mới', 'clubs-manager'),
            'edit_item'            => __('Sửa câu lạc bộ', 'clubs-manager'),
            'view_item'            => __('Xem câu lạc bộ', 'clubs-manager'),
            'all_items'            => __('Tất cả câu lạc bộ', 'clubs-manager'),
            'search_items'         => __('Tìm kiếm câu lạc bộ', 'clubs-manager'),
            'parent_item_colon'    => __('Câu lạc bộ cha:', 'clubs-manager'),
            'not_found'            => __('Không tìm thấy câu lạc bộ nào.', 'clubs-manager'),
            'not_found_in_trash'   => __('Không tìm thấy câu lạc bộ nào trong thùng rác.', 'clubs-manager'),
            'featured_image'       => _x('Ảnh đại diện', 'Overrides the "Featured Image" phrase', 'clubs-manager'),
            'set_featured_image'   => _x('Đặt ảnh đại diện', 'Overrides the "Set featured image" phrase', 'clubs-manager'),
            'remove_featured_image' => _x('Xóa ảnh đại diện', 'Overrides the "Remove featured image" phrase', 'clubs-manager'),
            'use_featured_image'   => _x('Sử dụng làm ảnh đại diện', 'Overrides the "Use as featured image" phrase', 'clubs-manager'),
            'archives'             => _x('Kho lưu trữ câu lạc bộ', 'The post type archive label used in nav menus', 'clubs-manager'),
            'insert_into_item'     => _x('Chèn vào câu lạc bộ', 'Overrides the "Insert into post" phrase', 'clubs-manager'),
            'uploaded_to_this_item' => _x('Tải lên cho câu lạc bộ này', 'Overrides the "Uploaded to this post" phrase', 'clubs-manager'),
            'filter_items_list'    => _x('Lọc danh sách câu lạc bộ', 'Screen reader text for the filter links', 'clubs-manager'),
            'items_list_navigation' => _x('Điều hướng danh sách câu lạc bộ', 'Screen reader text for the pagination', 'clubs-manager'),
            'items_list'           => _x('Danh sách câu lạc bộ', 'Screen reader text for the items list', 'clubs-manager'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'club'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-location-alt',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'       => true,
        );

        register_post_type('billiard_club', $args);
    }
    
    /**
     * Register club_area taxonomy
     */
    public function register_taxonomy() {
        $labels = array(
            'name'                       => _x('Khu vực', 'Taxonomy General Name', 'clubs-manager'),
            'singular_name'              => _x('Khu vực', 'Taxonomy Singular Name', 'clubs-manager'),
            'menu_name'                  => __('Khu vực', 'clubs-manager'),
            'all_items'                  => __('Tất cả khu vực', 'clubs-manager'),
            'parent_item'                => __('Khu vực cha', 'clubs-manager'),
            'parent_item_colon'          => __('Khu vực cha:', 'clubs-manager'),
            'new_item_name'              => __('Tên khu vực mới', 'clubs-manager'),
            'add_new_item'               => __('Thêm khu vực mới', 'clubs-manager'),
            'edit_item'                  => __('Sửa khu vực', 'clubs-manager'),
            'update_item'                => __('Cập nhật khu vực', 'clubs-manager'),
            'view_item'                  => __('Xem khu vực', 'clubs-manager'),
            'separate_items_with_commas' => __('Ngăn cách khu vực bằng dấu phấy', 'clubs-manager'),
            'add_or_remove_items'        => __('Thêm hoặc xóa khu vực', 'clubs-manager'),
            'choose_from_most_used'      => __('Chọn từ khu vực được sử dụng nhiều nhất', 'clubs-manager'),
            'popular_items'              => __('Khu vực phổ biến', 'clubs-manager'),
            'search_items'               => __('Tìm kiếm khu vực', 'clubs-manager'),
            'not_found'                  => __('Không tìm thấy', 'clubs-manager'),
            'no_terms'                   => __('Không có khu vực', 'clubs-manager'),
            'items_list'                 => __('Danh sách khu vực', 'clubs-manager'),
            'items_list_navigation'      => __('Điều hướng danh sách khu vực', 'clubs-manager'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'rewrite'                    => array('slug' => 'club-area'),
            'show_in_rest'               => true,
        );

        register_taxonomy('club_area', array('billiard_club'), $args);
    }
}