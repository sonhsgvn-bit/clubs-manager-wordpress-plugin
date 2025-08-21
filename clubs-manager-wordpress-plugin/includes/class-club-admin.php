<?php
/**
 * Club Admin Class
 * 
 * Handles admin panel functionality, settings page, and dashboard widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Admin {
    
    /**
     * Initialize admin functionality
     */
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'settings_init' ) );
        add_action( 'wp_dashboard_setup', array( __CLASS__, 'add_dashboard_widget' ) );
        add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=billiard-club',
            __( 'Cài đặt Clubs Manager', 'clubs-manager' ),
            __( 'Cài đặt', 'clubs-manager' ),
            'manage_options',
            'clubs-manager-settings',
            array( __CLASS__, 'settings_page' )
        );
    }
    
    /**
     * Initialize settings
     */
    public static function settings_init() {
        register_setting( 'clubs_manager_settings', 'clubs_manager_options', array(
            'sanitize_callback' => array( __CLASS__, 'sanitize_settings' )
        ) );
        
        add_settings_section(
            'clubs_manager_general_section',
            __( 'Cài đặt chung', 'clubs-manager' ),
            array( __CLASS__, 'general_section_callback' ),
            'clubs_manager_settings'
        );
        
        add_settings_field(
            'google_maps_api_key',
            __( 'Google Maps API Key', 'clubs-manager' ),
            array( __CLASS__, 'google_maps_field_callback' ),
            'clubs_manager_settings',
            'clubs_manager_general_section'
        );
        
        add_settings_field(
            'default_map_zoom',
            __( 'Mức zoom mặc định', 'clubs-manager' ),
            array( __CLASS__, 'map_zoom_field_callback' ),
            'clubs_manager_settings',
            'clubs_manager_general_section'
        );
        
        add_settings_field(
            'clubs_per_page',
            __( 'Số CLB trên mỗi trang', 'clubs-manager' ),
            array( __CLASS__, 'clubs_per_page_field_callback' ),
            'clubs_manager_settings',
            'clubs_manager_general_section'
        );
        
        add_settings_field(
            'enable_lazy_loading',
            __( 'Bật lazy loading', 'clubs-manager' ),
            array( __CLASS__, 'lazy_loading_field_callback' ),
            'clubs_manager_settings',
            'clubs_manager_general_section'
        );
    }
    
    /**
     * Sanitize settings
     */
    public static function sanitize_settings( $input ) {
        $sanitized = array();
        
        if ( isset( $input['google_maps_api_key'] ) ) {
            $sanitized['google_maps_api_key'] = sanitize_text_field( $input['google_maps_api_key'] );
        }
        
        if ( isset( $input['default_map_zoom'] ) ) {
            $sanitized['default_map_zoom'] = absint( $input['default_map_zoom'] );
            if ( $sanitized['default_map_zoom'] < 1 || $sanitized['default_map_zoom'] > 20 ) {
                $sanitized['default_map_zoom'] = 15;
            }
        }
        
        if ( isset( $input['clubs_per_page'] ) ) {
            $sanitized['clubs_per_page'] = absint( $input['clubs_per_page'] );
            if ( $sanitized['clubs_per_page'] < 1 ) {
                $sanitized['clubs_per_page'] = 12;
            }
        }
        
        if ( isset( $input['enable_lazy_loading'] ) ) {
            $sanitized['enable_lazy_loading'] = '1';
        } else {
            $sanitized['enable_lazy_loading'] = '0';
        }
        
        return $sanitized;
    }
    
    /**
     * General section callback
     */
    public static function general_section_callback() {
        echo '<p>' . __( 'Cấu hình các thông số chung cho plugin Clubs Manager.', 'clubs-manager' ) . '</p>';
    }
    
    /**
     * Google Maps API field callback
     */
    public static function google_maps_field_callback() {
        $options = get_option( 'clubs_manager_options' );
        $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
        
        echo '<input type="text" id="google_maps_api_key" name="clubs_manager_options[google_maps_api_key]" value="' . esc_attr( $api_key ) . '" class="regular-text" />';
        echo '<p class="description">' . __( 'Nhập Google Maps API Key để hiển thị bản đồ. <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Lấy API Key</a>', 'clubs-manager' ) . '</p>';
    }
    
    /**
     * Map zoom field callback
     */
    public static function map_zoom_field_callback() {
        $options = get_option( 'clubs_manager_options' );
        $zoom = isset( $options['default_map_zoom'] ) ? $options['default_map_zoom'] : 15;
        
        echo '<select id="default_map_zoom" name="clubs_manager_options[default_map_zoom]">';
        for ( $i = 10; $i <= 20; $i++ ) {
            echo '<option value="' . $i . '" ' . selected( $zoom, $i, false ) . '>' . $i . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __( 'Mức zoom mặc định cho bản đồ (10-20)', 'clubs-manager' ) . '</p>';
    }
    
    /**
     * Clubs per page field callback
     */
    public static function clubs_per_page_field_callback() {
        $options = get_option( 'clubs_manager_options' );
        $per_page = isset( $options['clubs_per_page'] ) ? $options['clubs_per_page'] : 12;
        
        echo '<input type="number" id="clubs_per_page" name="clubs_manager_options[clubs_per_page]" value="' . esc_attr( $per_page ) . '" min="1" max="50" class="small-text" />';
        echo '<p class="description">' . __( 'Số lượng câu lạc bộ hiển thị trên mỗi trang', 'clubs-manager' ) . '</p>';
    }
    
    /**
     * Lazy loading field callback
     */
    public static function lazy_loading_field_callback() {
        $options = get_option( 'clubs_manager_options' );
        $enabled = isset( $options['enable_lazy_loading'] ) ? $options['enable_lazy_loading'] : '1';
        
        echo '<label><input type="checkbox" id="enable_lazy_loading" name="clubs_manager_options[enable_lazy_loading]" value="1" ' . checked( $enabled, '1', false ) . ' /> ' . __( 'Bật lazy loading cho hình ảnh', 'clubs-manager' ) . '</label>';
        echo '<p class="description">' . __( 'Tăng tốc độ tải trang bằng cách tải hình ảnh khi cần thiết', 'clubs-manager' ) . '</p>';
    }
    
    /**
     * Settings page
     */
    public static function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( 'Cài đặt đã được lưu thành công!', 'clubs-manager' ); ?></p>
                </div>
            <?php endif; ?>
            
            <form action="options.php" method="post">
                <?php
                settings_fields( 'clubs_manager_settings' );
                do_settings_sections( 'clubs_manager_settings' );
                submit_button( __( 'Lưu cài đặt', 'clubs-manager' ) );
                ?>
            </form>
            
            <div class="card">
                <h2><?php _e( 'Hướng dẫn sử dụng Shortcodes', 'clubs-manager' ); ?></h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e( 'Shortcode', 'clubs-manager' ); ?></th>
                            <th><?php _e( 'Mô tả', 'clubs-manager' ); ?></th>
                            <th><?php _e( 'Tham số', 'clubs-manager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>[clubs_list]</code></td>
                            <td><?php _e( 'Hiển thị danh sách câu lạc bộ', 'clubs-manager' ); ?></td>
                            <td>area, limit, layout</td>
                        </tr>
                        <tr>
                            <td><code>[clubs_search]</code></td>
                            <td><?php _e( 'Form tìm kiếm câu lạc bộ', 'clubs-manager' ); ?></td>
                            <td>show_filters, ajax</td>
                        </tr>
                        <tr>
                            <td><code>[clubs_map]</code></td>
                            <td><?php _e( 'Bản đồ tất cả câu lạc bộ', 'clubs-manager' ); ?></td>
                            <td>height, zoom, center</td>
                        </tr>
                    </tbody>
                </table>
                
                <h3><?php _e( 'Ví dụ:', 'clubs-manager' ); ?></h3>
                <p><code>[clubs_list limit="6" layout="grid"]</code> - <?php _e( 'Hiển thị 6 câu lạc bộ dưới dạng lưới', 'clubs-manager' ); ?></p>
                <p><code>[clubs_search show_filters="true" ajax="true"]</code> - <?php _e( 'Form tìm kiếm với bộ lọc và AJAX', 'clubs-manager' ); ?></p>
                <p><code>[clubs_map height="400px" zoom="12"]</code> - <?php _e( 'Bản đồ cao 400px với zoom level 12', 'clubs-manager' ); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Add dashboard widget
     */
    public static function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'clubs_manager_stats',
            __( 'Thống kê Clubs Manager', 'clubs-manager' ),
            array( __CLASS__, 'dashboard_widget_content' )
        );
    }
    
    /**
     * Dashboard widget content
     */
    public static function dashboard_widget_content() {
        $total_clubs = wp_count_posts( 'billiard-club' );
        $published_clubs = $total_clubs->publish;
        $draft_clubs = $total_clubs->draft;
        
        $areas = get_terms( array(
            'taxonomy' => 'club-area',
            'hide_empty' => false,
        ) );
        $total_areas = is_array( $areas ) ? count( $areas ) : 0;
        
        // Get recent clubs
        $recent_clubs = get_posts( array(
            'post_type' => 'billiard-club',
            'numberposts' => 5,
            'post_status' => 'publish',
        ) );
        
        ?>
        <div class="clubs-manager-stats">
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                <div class="stat-box" style="text-align: center; padding: 15px; background: #f1f1f1; border-radius: 4px;">
                    <h3 style="margin: 0; color: #0073aa;"><?php echo number_format( $published_clubs ); ?></h3>
                    <p style="margin: 5px 0 0;"><?php _e( 'CLB đã xuất bản', 'clubs-manager' ); ?></p>
                </div>
                <div class="stat-box" style="text-align: center; padding: 15px; background: #f1f1f1; border-radius: 4px;">
                    <h3 style="margin: 0; color: #d54e21;"><?php echo number_format( $draft_clubs ); ?></h3>
                    <p style="margin: 5px 0 0;"><?php _e( 'CLB nháp', 'clubs-manager' ); ?></p>
                </div>
                <div class="stat-box" style="text-align: center; padding: 15px; background: #f1f1f1; border-radius: 4px;">
                    <h3 style="margin: 0; color: #46b450;"><?php echo number_format( $total_areas ); ?></h3>
                    <p style="margin: 5px 0 0;"><?php _e( 'Khu vực', 'clubs-manager' ); ?></p>
                </div>
            </div>
            
            <?php if ( ! empty( $recent_clubs ) ) : ?>
                <h4><?php _e( 'Câu lạc bộ mới nhất', 'clubs-manager' ); ?></h4>
                <ul style="margin: 0;">
                    <?php foreach ( $recent_clubs as $club ) : ?>
                        <li style="margin-bottom: 5px;">
                            <a href="<?php echo get_edit_post_link( $club->ID ); ?>"><?php echo esc_html( $club->post_title ); ?></a>
                            <small style="color: #666;"> - <?php echo get_the_date( 'j/n/Y', $club->ID ); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                <a href="<?php echo admin_url( 'edit.php?post_type=billiard-club' ); ?>" class="button button-secondary"><?php _e( 'Quản lý CLB', 'clubs-manager' ); ?></a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=billiard-club' ); ?>" class="button button-primary"><?php _e( 'Thêm CLB mới', 'clubs-manager' ); ?></a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Admin notices
     */
    public static function admin_notices() {
        $options = get_option( 'clubs_manager_options' );
        $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
        
        // Show notice if Google Maps API key is not set
        if ( empty( $api_key ) && ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'clubs-manager-settings' ) ) {
            $settings_url = admin_url( 'edit.php?post_type=billiard-club&page=clubs-manager-settings' );
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <?php _e( 'Clubs Manager: ', 'clubs-manager' ); ?>
                    <?php printf( 
                        __( 'Vui lòng cấu hình <a href="%s">Google Maps API Key</a> để sử dụng tính năng bản đồ.', 'clubs-manager' ),
                        esc_url( $settings_url )
                    ); ?>
                </p>
            </div>
            <?php
        }
    }
}