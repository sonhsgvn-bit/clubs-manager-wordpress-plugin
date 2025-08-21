<?php
/**
 * Club Shortcode Class
 * 
 * Handles all shortcodes for the clubs manager plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Shortcode {
    
    /**
     * Initialize shortcodes
     */
    public static function init() {
        add_shortcode( 'clubs_list', array( __CLASS__, 'clubs_list_shortcode' ) );
        add_shortcode( 'clubs_search', array( __CLASS__, 'clubs_search_shortcode' ) );
        add_shortcode( 'clubs_map', array( __CLASS__, 'clubs_map_shortcode' ) );
    }
    
    /**
     * Clubs list shortcode
     * [clubs_list area="" limit="12" layout="grid" orderby="title" order="ASC"]
     */
    public static function clubs_list_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'area' => '',
            'limit' => 12,
            'layout' => 'grid', // grid or list
            'orderby' => 'title',
            'order' => 'ASC',
            'show_excerpt' => 'true',
            'show_price' => 'true',
            'show_tables' => 'true',
        ), $atts, 'clubs_list' );
        
        $args = array(
            'post_type' => 'billiard-club',
            'post_status' => 'publish',
            'posts_per_page' => intval( $atts['limit'] ),
            'orderby' => sanitize_text_field( $atts['orderby'] ),
            'order' => strtoupper( sanitize_text_field( $atts['order'] ) ),
        );
        
        // Add taxonomy query if area is specified
        if ( ! empty( $atts['area'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'slug',
                    'terms' => sanitize_text_field( $atts['area'] ),
                ),
            );
        }
        
        $clubs_query = new WP_Query( $args );
        
        if ( ! $clubs_query->have_posts() ) {
            return '<p class="clubs-no-results">' . __( 'Không tìm thấy câu lạc bộ nào.', 'clubs-manager' ) . '</p>';
        }
        
        $layout_class = $atts['layout'] === 'list' ? 'clubs-list-layout' : 'clubs-grid-layout';
        
        ob_start();
        ?>
        <div class="clubs-shortcode-container <?php echo esc_attr( $layout_class ); ?>">
            <div class="clubs-grid">
                <?php while ( $clubs_query->have_posts() ) : $clubs_query->the_post(); ?>
                    <?php echo self::render_club_card( get_the_ID(), $atts ); ?>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Clubs search shortcode
     * [clubs_search show_filters="true" ajax="true" results_per_page="12"]
     */
    public static function clubs_search_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'show_filters' => 'true',
            'ajax' => 'true',
            'results_per_page' => 12,
        ), $atts, 'clubs_search' );
        
        ob_start();
        ?>
        <div class="clubs-search-container" data-ajax="<?php echo esc_attr( $atts['ajax'] ); ?>" data-per-page="<?php echo esc_attr( $atts['results_per_page'] ); ?>">
            <form class="clubs-search-form" id="clubs-search-form">
                <div class="search-row">
                    <div class="search-field">
                        <label for="club-search-keyword"><?php _e( 'Từ khóa', 'clubs-manager' ); ?></label>
                        <input type="text" id="club-search-keyword" name="keyword" placeholder="<?php _e( 'Tên câu lạc bộ...', 'clubs-manager' ); ?>" />
                    </div>
                    
                    <div class="search-field">
                        <label for="club-search-area"><?php _e( 'Khu vực', 'clubs-manager' ); ?></label>
                        <select id="club-search-area" name="area">
                            <option value=""><?php _e( 'Tất cả khu vực', 'clubs-manager' ); ?></option>
                            <?php
                            $areas = get_terms( array(
                                'taxonomy' => 'club-area',
                                'hide_empty' => true,
                            ) );
                            
                            if ( ! is_wp_error( $areas ) && ! empty( $areas ) ) {
                                foreach ( $areas as $area ) {
                                    echo '<option value="' . esc_attr( $area->slug ) . '">' . esc_html( $area->name ) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <?php if ( $atts['show_filters'] === 'true' ) : ?>
                        <div class="search-field">
                            <label for="club-search-price-max"><?php _e( 'Giá tối đa (VNĐ/giờ)', 'clubs-manager' ); ?></label>
                            <input type="number" id="club-search-price-max" name="price_max" placeholder="<?php _e( 'Ví dụ: 100000', 'clubs-manager' ); ?>" min="0" step="10000" />
                        </div>
                        
                        <div class="search-field">
                            <label for="club-search-tables-min"><?php _e( 'Số bàn tối thiểu', 'clubs-manager' ); ?></label>
                            <input type="number" id="club-search-tables-min" name="tables_min" placeholder="<?php _e( 'Ví dụ: 5', 'clubs-manager' ); ?>" min="1" />
                        </div>
                    <?php endif; ?>
                    
                    <div class="search-field">
                        <button type="submit" class="search-submit"><?php _e( 'Tìm kiếm', 'clubs-manager' ); ?></button>
                    </div>
                </div>
                
                <?php if ( $atts['show_filters'] === 'true' ) : ?>
                    <div class="search-filters">
                        <h4><?php _e( 'Tiện ích:', 'clubs-manager' ); ?></h4>
                        <div class="filter-checkboxes">
                            <label><input type="checkbox" name="facilities[]" value="parking" /> <?php _e( 'Chỗ để xe', 'clubs-manager' ); ?></label>
                            <label><input type="checkbox" name="facilities[]" value="wifi" /> <?php _e( 'WiFi miễn phí', 'clubs-manager' ); ?></label>
                            <label><input type="checkbox" name="facilities[]" value="food_service" /> <?php _e( 'Dịch vụ ăn uống', 'clubs-manager' ); ?></label>
                            <label><input type="checkbox" name="facilities[]" value="air_conditioning" /> <?php _e( 'Điều hòa', 'clubs-manager' ); ?></label>
                            <label><input type="checkbox" name="facilities[]" value="cue_rental" /> <?php _e( 'Cho thuê cơ', 'clubs-manager' ); ?></label>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
            
            <div class="clubs-search-results" id="clubs-search-results">
                <!-- Results will be loaded here -->
            </div>
            
            <div class="clubs-search-loading" id="clubs-search-loading" style="display: none;">
                <p><?php _e( 'Đang tìm kiếm...', 'clubs-manager' ); ?></p>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Clubs map shortcode
     * [clubs_map height="400px" zoom="15" center="" show_all="true"]
     */
    public static function clubs_map_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'height' => '400px',
            'zoom' => '15',
            'center' => '',
            'show_all' => 'true',
            'area' => '',
        ), $atts, 'clubs_map' );
        
        $options = get_option( 'clubs_manager_options' );
        $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
        
        if ( empty( $api_key ) ) {
            return '<div class="clubs-map-error"><p>' . __( 'Google Maps API Key chưa được cấu hình.', 'clubs-manager' ) . '</p></div>';
        }
        
        // Get clubs for the map
        $args = array(
            'post_type' => 'billiard-club',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_club_address',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
        );
        
        if ( ! empty( $atts['area'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'slug',
                    'terms' => sanitize_text_field( $atts['area'] ),
                ),
            );
        }
        
        $clubs_query = new WP_Query( $args );
        $clubs_data = array();
        
        if ( $clubs_query->have_posts() ) {
            while ( $clubs_query->have_posts() ) {
                $clubs_query->the_post();
                $address = get_post_meta( get_the_ID(), '_club_address', true );
                $price = get_post_meta( get_the_ID(), '_club_price', true );
                $tables = get_post_meta( get_the_ID(), '_club_tables', true );
                
                if ( ! empty( $address ) ) {
                    $clubs_data[] = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'address' => $address,
                        'price' => $price,
                        'tables' => $tables,
                        'url' => get_permalink(),
                        'excerpt' => get_the_excerpt(),
                    );
                }
            }
        }
        wp_reset_postdata();
        
        $map_id = 'clubs-map-' . uniqid();
        
        ob_start();
        ?>
        <div class="clubs-map-container">
            <div id="<?php echo esc_attr( $map_id ); ?>" class="clubs-map" 
                 style="height: <?php echo esc_attr( $atts['height'] ); ?>;"
                 data-zoom="<?php echo esc_attr( $atts['zoom'] ); ?>"
                 data-center="<?php echo esc_attr( $atts['center'] ); ?>"
                 data-clubs="<?php echo esc_attr( wp_json_encode( $clubs_data ) ); ?>">
            </div>
        </div>
        
        <?php if ( ! wp_script_is( 'google-maps', 'enqueued' ) ) : ?>
            <script>
                function initClubsMap() {
                    if (typeof window.clubsMapInit === 'function') {
                        window.clubsMapInit('<?php echo esc_js( $map_id ); ?>');
                    }
                }
            </script>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( $api_key ); ?>&callback=initClubsMap"></script>
        <?php endif; ?>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Render club card for list/grid display
     */
    private static function render_club_card( $club_id, $atts ) {
        $address = get_post_meta( $club_id, '_club_address', true );
        $price = get_post_meta( $club_id, '_club_price', true );
        $tables = get_post_meta( $club_id, '_club_tables', true );
        $phone = get_post_meta( $club_id, '_club_phone', true );
        
        // Get club areas
        $areas = get_the_terms( $club_id, 'club-area' );
        $area_names = array();
        if ( $areas && ! is_wp_error( $areas ) ) {
            $area_names = wp_list_pluck( $areas, 'name' );
        }
        
        ob_start();
        ?>
        <div class="club-card">
            <?php if ( has_post_thumbnail( $club_id ) ) : ?>
                <div class="club-card-image">
                    <a href="<?php echo get_permalink( $club_id ); ?>">
                        <?php echo get_the_post_thumbnail( $club_id, 'medium', array( 'loading' => 'lazy' ) ); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="club-card-content">
                <h3 class="club-title">
                    <a href="<?php echo get_permalink( $club_id ); ?>"><?php echo get_the_title( $club_id ); ?></a>
                </h3>
                
                <?php if ( ! empty( $area_names ) ) : ?>
                    <div class="club-area">
                        <span class="area-icon">📍</span>
                        <?php echo implode( ', ', $area_names ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( $address ) : ?>
                    <div class="club-address">
                        <span class="address-icon">🏠</span>
                        <?php echo esc_html( wp_trim_words( $address, 8 ) ); ?>
                    </div>
                <?php endif; ?>
                
                <div class="club-meta">
                    <?php if ( $atts['show_price'] === 'true' && $price ) : ?>
                        <div class="club-price">
                            <span class="price-icon">💰</span>
                            <?php echo number_format( $price, 0, ',', '.' ); ?> VNĐ/giờ
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $atts['show_tables'] === 'true' && $tables ) : ?>
                        <div class="club-tables">
                            <span class="tables-icon">🎱</span>
                            <?php echo $tables; ?> bàn
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ( $atts['show_excerpt'] === 'true' ) : ?>
                    <div class="club-excerpt">
                        <?php echo wp_trim_words( get_the_excerpt( $club_id ), 15 ); ?>
                    </div>
                <?php endif; ?>
                
                <div class="club-actions">
                    <a href="<?php echo get_permalink( $club_id ); ?>" class="club-details-btn">
                        <?php _e( 'Xem chi tiết', 'clubs-manager' ); ?>
                    </a>
                    
                    <?php if ( $phone ) : ?>
                        <a href="tel:<?php echo esc_attr( $phone ); ?>" class="club-call-btn">
                            <?php _e( 'Gọi ngay', 'clubs-manager' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
}