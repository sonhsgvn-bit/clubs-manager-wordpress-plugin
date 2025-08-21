<?php
/**
 * Club Ajax Class
 * 
 * Handles AJAX functionality for search and filtering
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Ajax {
    
    /**
     * Initialize AJAX functionality
     */
    public static function init() {
        add_action( 'wp_ajax_club_search', array( __CLASS__, 'handle_club_search' ) );
        add_action( 'wp_ajax_nopriv_club_search', array( __CLASS__, 'handle_club_search' ) );
        add_action( 'wp_ajax_club_load_more', array( __CLASS__, 'handle_load_more' ) );
        add_action( 'wp_ajax_nopriv_club_load_more', array( __CLASS__, 'handle_load_more' ) );
        add_action( 'wp_ajax_club_get_coordinates', array( __CLASS__, 'handle_get_coordinates' ) );
        add_action( 'wp_ajax_nopriv_club_get_coordinates', array( __CLASS__, 'handle_get_coordinates' ) );
    }
    
    /**
     * Handle club search AJAX request
     */
    public static function handle_club_search() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'clubs_ajax_nonce' ) ) {
            wp_die( 'Security check failed' );
        }
        
        $keyword = sanitize_text_field( $_POST['keyword'] ?? '' );
        $area = sanitize_text_field( $_POST['area'] ?? '' );
        $price_max = intval( $_POST['price_max'] ?? 0 );
        $tables_min = intval( $_POST['tables_min'] ?? 0 );
        $facilities = $_POST['facilities'] ?? array();
        $page = intval( $_POST['page'] ?? 1 );
        $per_page = intval( $_POST['per_page'] ?? 12 );
        
        // Sanitize facilities array
        $facilities = array_map( 'sanitize_text_field', $facilities );
        
        // Build query arguments
        $args = array(
            'post_type' => 'billiard-club',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
        );
        
        // Meta query for custom fields
        $meta_query = array( 'relation' => 'AND' );
        
        // Price filter
        if ( $price_max > 0 ) {
            $meta_query[] = array(
                'key' => '_club_price',
                'value' => $price_max,
                'type' => 'NUMERIC',
                'compare' => '<=',
            );
        }
        
        // Tables filter
        if ( $tables_min > 0 ) {
            $meta_query[] = array(
                'key' => '_club_tables',
                'value' => $tables_min,
                'type' => 'NUMERIC',
                'compare' => '>=',
            );
        }
        
        // Facilities filters
        if ( ! empty( $facilities ) ) {
            foreach ( $facilities as $facility ) {
                $meta_key = '_club_' . $facility;
                $meta_query[] = array(
                    'key' => $meta_key,
                    'value' => '1',
                    'compare' => '=',
                );
            }
        }
        
        if ( count( $meta_query ) > 1 ) {
            $args['meta_query'] = $meta_query;
        }
        
        // Tax query for area
        if ( ! empty( $area ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'slug',
                    'terms' => $area,
                ),
            );
        }
        
        // Search query
        if ( ! empty( $keyword ) ) {
            $args['s'] = $keyword;
        }
        
        $clubs_query = new WP_Query( $args );
        
        $response = array(
            'success' => true,
            'data' => array(
                'clubs' => array(),
                'found_posts' => $clubs_query->found_posts,
                'max_pages' => $clubs_query->max_num_pages,
                'current_page' => $page,
            ),
        );
        
        if ( $clubs_query->have_posts() ) {
            while ( $clubs_query->have_posts() ) {
                $clubs_query->the_post();
                $response['data']['clubs'][] = self::format_club_data( get_the_ID() );
            }
        }
        
        wp_reset_postdata();
        
        if ( empty( $response['data']['clubs'] ) ) {
            $response['data']['message'] = __( 'Không tìm thấy câu lạc bộ nào phù hợp với tiêu chí tìm kiếm.', 'clubs-manager' );
        }
        
        wp_send_json( $response );
    }
    
    /**
     * Handle load more AJAX request
     */
    public static function handle_load_more() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'clubs_ajax_nonce' ) ) {
            wp_die( 'Security check failed' );
        }
        
        $page = intval( $_POST['page'] ?? 1 );
        $per_page = intval( $_POST['per_page'] ?? 12 );
        $area = sanitize_text_field( $_POST['area'] ?? '' );
        
        $args = array(
            'post_type' => 'billiard-club',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
        );
        
        if ( ! empty( $area ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'slug',
                    'terms' => $area,
                ),
            );
        }
        
        $clubs_query = new WP_Query( $args );
        
        $response = array(
            'success' => true,
            'data' => array(
                'clubs' => array(),
                'has_more' => $page < $clubs_query->max_num_pages,
            ),
        );
        
        if ( $clubs_query->have_posts() ) {
            while ( $clubs_query->have_posts() ) {
                $clubs_query->the_post();
                $response['data']['clubs'][] = self::format_club_data( get_the_ID() );
            }
        }
        
        wp_reset_postdata();
        wp_send_json( $response );
    }
    
    /**
     * Handle get coordinates AJAX request
     */
    public static function handle_get_coordinates() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'clubs_ajax_nonce' ) ) {
            wp_die( 'Security check failed' );
        }
        
        $address = sanitize_text_field( $_POST['address'] ?? '' );
        
        if ( empty( $address ) ) {
            wp_send_json_error( 'Address is required' );
        }
        
        $options = get_option( 'clubs_manager_options' );
        $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
        
        if ( empty( $api_key ) ) {
            wp_send_json_error( 'Google Maps API Key not configured' );
        }
        
        // Check if coordinates are cached
        $cache_key = 'club_coords_' . md5( $address );
        $cached_coords = get_transient( $cache_key );
        
        if ( $cached_coords !== false ) {
            wp_send_json_success( $cached_coords );
        }
        
        // Geocode the address
        $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query( array(
            'address' => $address,
            'key' => $api_key,
        ) );
        
        $response = wp_remote_get( $geocode_url );
        
        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'Failed to geocode address' );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( $data['status'] === 'OK' && ! empty( $data['results'] ) ) {
            $location = $data['results'][0]['geometry']['location'];
            $coordinates = array(
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            );
            
            // Cache coordinates for 1 week
            set_transient( $cache_key, $coordinates, WEEK_IN_SECONDS );
            
            wp_send_json_success( $coordinates );
        } else {
            wp_send_json_error( 'Address not found' );
        }
    }
    
    /**
     * Format club data for AJAX response
     */
    private static function format_club_data( $club_id ) {
        $address = get_post_meta( $club_id, '_club_address', true );
        $price = get_post_meta( $club_id, '_club_price', true );
        $tables = get_post_meta( $club_id, '_club_tables', true );
        $phone = get_post_meta( $club_id, '_club_phone', true );
        $email = get_post_meta( $club_id, '_club_email', true );
        
        // Get areas
        $areas = get_the_terms( $club_id, 'club-area' );
        $area_names = array();
        if ( $areas && ! is_wp_error( $areas ) ) {
            $area_names = wp_list_pluck( $areas, 'name' );
        }
        
        // Get facilities
        $facilities = array();
        $facility_fields = array(
            '_club_parking' => __( 'Chỗ để xe', 'clubs-manager' ),
            '_club_wifi' => __( 'WiFi miễn phí', 'clubs-manager' ),
            '_club_food_service' => __( 'Dịch vụ ăn uống', 'clubs-manager' ),
            '_club_air_conditioning' => __( 'Điều hòa', 'clubs-manager' ),
            '_club_cue_rental' => __( 'Cho thuê cơ', 'clubs-manager' ),
        );
        
        foreach ( $facility_fields as $meta_key => $label ) {
            if ( get_post_meta( $club_id, $meta_key, true ) === '1' ) {
                $facilities[] = $label;
            }
        }
        
        // Get thumbnail
        $thumbnail = '';
        if ( has_post_thumbnail( $club_id ) ) {
            $thumbnail = get_the_post_thumbnail_url( $club_id, 'medium' );
        }
        
        return array(
            'id' => $club_id,
            'title' => get_the_title( $club_id ),
            'excerpt' => get_the_excerpt( $club_id ),
            'permalink' => get_permalink( $club_id ),
            'thumbnail' => $thumbnail,
            'address' => $address,
            'price' => $price,
            'tables' => $tables,
            'phone' => $phone,
            'email' => $email,
            'areas' => $area_names,
            'facilities' => $facilities,
            'formatted_price' => $price ? number_format( $price, 0, ',', '.' ) . ' VNĐ/giờ' : '',
        );
    }
}