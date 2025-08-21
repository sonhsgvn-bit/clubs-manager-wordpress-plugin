<?php
/**
 * Frontend functionality
 *
 * @package ClubsManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Frontend {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_clubs_search', array($this, 'ajax_search'));
        add_action('wp_ajax_nopriv_clubs_search', array($this, 'ajax_search'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Only load on club pages
        if (is_singular('billiard_club') || is_post_type_archive('billiard_club') || is_tax('club_area')) {
            wp_enqueue_style('clubs-frontend', CLUBS_MANAGER_PLUGIN_URL . 'assets/css/frontend.css', array(), CLUBS_MANAGER_VERSION);
            wp_enqueue_script('clubs-frontend', CLUBS_MANAGER_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), CLUBS_MANAGER_VERSION, true);
            
            // AJAX search script
            wp_enqueue_script('clubs-ajax-search', CLUBS_MANAGER_PLUGIN_URL . 'assets/js/ajax-search.js', array('jquery'), CLUBS_MANAGER_VERSION, true);
            
            // Localize script for AJAX
            wp_localize_script('clubs-ajax-search', 'clubs_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('clubs_search_nonce'),
                'loading_text' => __('Đang tải...', 'clubs-manager'),
                'no_results_text' => __('Không tìm thấy câu lạc bộ nào.', 'clubs-manager'),
            ));
            
            // Google Maps API
            $options = get_option('clubs_manager_options');
            $api_key = isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : '';
            
            if (!empty($api_key)) {
                wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places', array(), null, true);
            }
        }
    }
    
    /**
     * AJAX search handler
     */
    public function ajax_search() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'clubs_search_nonce')) {
            wp_die('Security check failed');
        }
        
        $search_name = sanitize_text_field($_POST['search_name']);
        $search_area = intval($_POST['search_area']);
        $search_price_min = intval($_POST['search_price_min']);
        $search_price_max = intval($_POST['search_price_max']);
        $search_tables = intval($_POST['search_tables']);
        $search_parking = sanitize_text_field($_POST['search_parking']);
        $paged = intval($_POST['paged']);
        
        $args = array(
            'post_type' => 'billiard_club',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'paged' => $paged,
            'meta_query' => array(),
        );
        
        // Search by name
        if (!empty($search_name)) {
            $args['s'] = $search_name;
        }
        
        // Search by area
        if (!empty($search_area)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club_area',
                    'field' => 'term_id',
                    'terms' => $search_area,
                ),
            );
        }
        
        // Search by price range
        if (!empty($search_price_min) || !empty($search_price_max)) {
            $price_query = array('key' => 'club_price', 'compare' => 'EXISTS');
            
            if (!empty($search_price_min) && !empty($search_price_max)) {
                $price_query['value'] = array($search_price_min, $search_price_max);
                $price_query['compare'] = 'BETWEEN';
                $price_query['type'] = 'NUMERIC';
            } elseif (!empty($search_price_min)) {
                $price_query['value'] = $search_price_min;
                $price_query['compare'] = '>=';
                $price_query['type'] = 'NUMERIC';
            } elseif (!empty($search_price_max)) {
                $price_query['value'] = $search_price_max;
                $price_query['compare'] = '<=';
                $price_query['type'] = 'NUMERIC';
            }
            
            $args['meta_query'][] = $price_query;
        }
        
        // Search by minimum tables
        if (!empty($search_tables)) {
            $args['meta_query'][] = array(
                'key' => 'club_tables',
                'value' => $search_tables,
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        
        // Search by parking
        if (!empty($search_parking)) {
            $args['meta_query'][] = array(
                'key' => 'club_parking',
                'value' => $search_parking,
                'compare' => '=',
            );
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include CLUBS_MANAGER_PLUGIN_DIR . 'templates/parts/club-card.php';
            }
            wp_reset_postdata();
        } else {
            echo '<div class="no-clubs-found"><p>' . __('Không tìm thấy câu lạc bộ nào.', 'clubs-manager') . '</p></div>';
        }
        
        $results = ob_get_clean();
        
        // Pagination
        $pagination = '';
        if ($query->max_num_pages > 1) {
            $pagination = paginate_links(array(
                'base' => '%_%',
                'format' => '?paged=%#%',
                'current' => $paged,
                'total' => $query->max_num_pages,
                'prev_text' => __('&laquo; Trước', 'clubs-manager'),
                'next_text' => __('Sau &raquo;', 'clubs-manager'),
                'type' => 'plain',
            ));
        }
        
        wp_send_json_success(array(
            'results' => $results,
            'pagination' => $pagination,
            'found_posts' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
        ));
    }
    
    /**
     * Get clubs for map display
     */
    public function get_clubs_for_map($args = array()) {
        $default_args = array(
            'post_type' => 'billiard_club',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'club_lat',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => 'club_lng',
                    'compare' => 'EXISTS',
                ),
            ),
        );
        
        $args = wp_parse_args($args, $default_args);
        $query = new WP_Query($args);
        
        $clubs = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $club_lat = get_post_meta(get_the_ID(), 'club_lat', true);
                $club_lng = get_post_meta(get_the_ID(), 'club_lng', true);
                
                if (!empty($club_lat) && !empty($club_lng)) {
                    $clubs[] = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'url' => get_permalink(),
                        'lat' => floatval($club_lat),
                        'lng' => floatval($club_lng),
                        'address' => get_post_meta(get_the_ID(), 'club_address', true),
                        'price' => get_post_meta(get_the_ID(), 'club_price', true),
                        'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    );
                }
            }
            wp_reset_postdata();
        }
        
        return $clubs;
    }
}