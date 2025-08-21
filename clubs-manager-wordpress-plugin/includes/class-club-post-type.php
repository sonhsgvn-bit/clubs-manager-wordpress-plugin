<?php
/**
 * Club Post Type Class
 * 
 * Handles the registration of custom post type and taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Post_Type {
    
    /**
     * Register the billiard-club post type and club-area taxonomy
     */
    public static function register_post_type() {
        self::register_club_post_type();
        self::register_club_area_taxonomy();
        self::add_custom_columns();
    }
    
    /**
     * Register billiard-club custom post type
     */
    private static function register_club_post_type() {
        $labels = array(
            'name'                  => _x( 'Câu lạc bộ bi-a', 'Post Type General Name', 'clubs-manager' ),
            'singular_name'         => _x( 'Câu lạc bộ', 'Post Type Singular Name', 'clubs-manager' ),
            'menu_name'             => __( 'Câu lạc bộ bi-a', 'clubs-manager' ),
            'name_admin_bar'        => __( 'Câu lạc bộ', 'clubs-manager' ),
            'archives'              => __( 'Danh sách câu lạc bộ', 'clubs-manager' ),
            'attributes'            => __( 'Thuộc tính', 'clubs-manager' ),
            'parent_item_colon'     => __( 'Câu lạc bộ cha:', 'clubs-manager' ),
            'all_items'             => __( 'Tất cả câu lạc bộ', 'clubs-manager' ),
            'add_new_item'          => __( 'Thêm câu lạc bộ mới', 'clubs-manager' ),
            'add_new'               => __( 'Thêm mới', 'clubs-manager' ),
            'new_item'              => __( 'Câu lạc bộ mới', 'clubs-manager' ),
            'edit_item'             => __( 'Sửa câu lạc bộ', 'clubs-manager' ),
            'update_item'           => __( 'Cập nhật', 'clubs-manager' ),
            'view_item'             => __( 'Xem câu lạc bộ', 'clubs-manager' ),
            'view_items'            => __( 'Xem các câu lạc bộ', 'clubs-manager' ),
            'search_items'          => __( 'Tìm kiếm câu lạc bộ', 'clubs-manager' ),
            'not_found'             => __( 'Không tìm thấy', 'clubs-manager' ),
            'not_found_in_trash'    => __( 'Không tìm thấy trong thùng rác', 'clubs-manager' ),
            'featured_image'        => __( 'Hình đại diện', 'clubs-manager' ),
            'set_featured_image'    => __( 'Đặt hình đại diện', 'clubs-manager' ),
            'remove_featured_image' => __( 'Xóa hình đại diện', 'clubs-manager' ),
            'use_featured_image'    => __( 'Sử dụng làm hình đại diện', 'clubs-manager' ),
            'insert_into_item'      => __( 'Chèn vào câu lạc bộ', 'clubs-manager' ),
            'uploaded_to_this_item' => __( 'Đã tải lên câu lạc bộ này', 'clubs-manager' ),
            'items_list'            => __( 'Danh sách câu lạc bộ', 'clubs-manager' ),
            'items_list_navigation' => __( 'Điều hướng danh sách', 'clubs-manager' ),
            'filter_items_list'     => __( 'Lọc danh sách', 'clubs-manager' ),
        );
        
        $args = array(
            'label'                 => __( 'Câu lạc bộ bi-a', 'clubs-manager' ),
            'description'           => __( 'Quản lý thông tin câu lạc bộ bi-a', 'clubs-manager' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'taxonomies'            => array( 'club-area' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-location-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'clubs',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array(
                'slug'       => 'club',
                'with_front' => false,
            ),
        );
        
        register_post_type( 'billiard-club', $args );
    }
    
    /**
     * Register club-area taxonomy
     */
    private static function register_club_area_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Khu vực', 'Taxonomy General Name', 'clubs-manager' ),
            'singular_name'              => _x( 'Khu vực', 'Taxonomy Singular Name', 'clubs-manager' ),
            'menu_name'                  => __( 'Khu vực', 'clubs-manager' ),
            'all_items'                  => __( 'Tất cả khu vực', 'clubs-manager' ),
            'parent_item'                => __( 'Khu vực cha', 'clubs-manager' ),
            'parent_item_colon'          => __( 'Khu vực cha:', 'clubs-manager' ),
            'new_item_name'              => __( 'Tên khu vực mới', 'clubs-manager' ),
            'add_new_item'               => __( 'Thêm khu vực mới', 'clubs-manager' ),
            'edit_item'                  => __( 'Sửa khu vực', 'clubs-manager' ),
            'update_item'                => __( 'Cập nhật khu vực', 'clubs-manager' ),
            'view_item'                  => __( 'Xem khu vực', 'clubs-manager' ),
            'separate_items_with_commas' => __( 'Phân cách bằng dấu phẩy', 'clubs-manager' ),
            'add_or_remove_items'        => __( 'Thêm hoặc xóa khu vực', 'clubs-manager' ),
            'choose_from_most_used'      => __( 'Chọn từ khu vực hay dùng', 'clubs-manager' ),
            'popular_items'              => __( 'Khu vực phổ biến', 'clubs-manager' ),
            'search_items'               => __( 'Tìm kiếm khu vực', 'clubs-manager' ),
            'not_found'                  => __( 'Không tìm thấy', 'clubs-manager' ),
            'no_terms'                   => __( 'Không có khu vực', 'clubs-manager' ),
            'items_list'                 => __( 'Danh sách khu vực', 'clubs-manager' ),
            'items_list_navigation'      => __( 'Điều hướng danh sách', 'clubs-manager' ),
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
                'slug'       => 'club-area',
                'with_front' => false,
            ),
        );
        
        register_taxonomy( 'club-area', array( 'billiard-club' ), $args );
    }
    
    /**
     * Add custom columns to admin list
     */
    private static function add_custom_columns() {
        add_filter( 'manage_billiard-club_posts_columns', array( __CLASS__, 'set_custom_columns' ) );
        add_action( 'manage_billiard-club_posts_custom_column', array( __CLASS__, 'custom_column_content' ), 10, 2 );
        add_filter( 'manage_edit-billiard-club_sortable_columns', array( __CLASS__, 'sortable_columns' ) );
    }
    
    /**
     * Set custom columns
     */
    public static function set_custom_columns( $columns ) {
        unset( $columns['date'] );
        
        $columns['club_area'] = __( 'Khu vực', 'clubs-manager' );
        $columns['club_price'] = __( 'Giá (VNĐ/giờ)', 'clubs-manager' );
        $columns['club_tables'] = __( 'Số bàn', 'clubs-manager' );
        $columns['club_phone'] = __( 'Điện thoại', 'clubs-manager' );
        $columns['date'] = __( 'Ngày tạo', 'clubs-manager' );
        
        return $columns;
    }
    
    /**
     * Custom column content
     */
    public static function custom_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'club_area':
                $terms = get_the_terms( $post_id, 'club-area' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $area_names = wp_list_pluck( $terms, 'name' );
                    echo implode( ', ', $area_names );
                } else {
                    echo __( 'Chưa phân loại', 'clubs-manager' );
                }
                break;
                
            case 'club_price':
                $price = get_post_meta( $post_id, '_club_price', true );
                if ( $price ) {
                    echo number_format( $price, 0, ',', '.' ) . ' VNĐ';
                } else {
                    echo __( 'Chưa có giá', 'clubs-manager' );
                }
                break;
                
            case 'club_tables':
                $tables = get_post_meta( $post_id, '_club_tables', true );
                echo $tables ? $tables : __( 'Chưa cập nhật', 'clubs-manager' );
                break;
                
            case 'club_phone':
                $phone = get_post_meta( $post_id, '_club_phone', true );
                echo $phone ? $phone : __( 'Chưa có', 'clubs-manager' );
                break;
        }
    }
    
    /**
     * Make columns sortable
     */
    public static function sortable_columns( $columns ) {
        $columns['club_price'] = 'club_price';
        $columns['club_tables'] = 'club_tables';
        
        return $columns;
    }
}