<?php
/**
 * Club Frontend Class
 * 
 * Handles frontend display, template overrides, and public functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Frontend {
    
    /**
     * Initialize frontend functionality
     */
    public static function init() {
        add_action( 'wp', array( __CLASS__, 'template_loader' ) );
        add_filter( 'template_include', array( __CLASS__, 'template_include' ) );
        add_action( 'wp_head', array( __CLASS__, 'add_structured_data' ) );
        add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_lightbox_scripts' ) );
    }
    
    /**
     * Template loader
     */
    public static function template_loader() {
        if ( is_singular( 'billiard-club' ) || is_post_type_archive( 'billiard-club' ) || is_tax( 'club-area' ) ) {
            add_filter( 'the_content', array( __CLASS__, 'single_club_content' ) );
        }
    }
    
    /**
     * Template include filter
     */
    public static function template_include( $template ) {
        if ( is_singular( 'billiard-club' ) ) {
            $custom_template = self::locate_template( 'single-club.php' );
            if ( $custom_template ) {
                return $custom_template;
            }
        }
        
        if ( is_post_type_archive( 'billiard-club' ) || is_tax( 'club-area' ) ) {
            $custom_template = self::locate_template( 'archive-clubs.php' );
            if ( $custom_template ) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Locate template files
     */
    private static function locate_template( $template_name ) {
        // Check if theme has the template
        $theme_template = locate_template( array(
            'clubs-manager/' . $template_name,
            $template_name,
        ) );
        
        if ( $theme_template ) {
            return $theme_template;
        }
        
        // Use plugin template
        $plugin_template = CLUBS_MANAGER_DIR . 'templates/' . $template_name;
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
        
        return false;
    }
    
    /**
     * Single club content filter
     */
    public static function single_club_content( $content ) {
        if ( ! is_singular( 'billiard-club' ) || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }
        
        global $post;
        
        $club_content = '<div class="club-details">';
        
        // Basic information
        $club_content .= self::render_basic_info( $post->ID );
        
        // Contact information
        $club_content .= self::render_contact_info( $post->ID );
        
        // Facilities
        $club_content .= self::render_facilities( $post->ID );
        
        // Opening hours
        $club_content .= self::render_opening_hours( $post->ID );
        
        // Gallery
        $club_content .= self::render_gallery( $post->ID );
        
        // Map
        $club_content .= self::render_map( $post->ID );
        
        $club_content .= '</div>';
        
        return $content . $club_content;
    }
    
    /**
     * Render basic information
     */
    public static function render_basic_info( $post_id ) {
        $address = get_post_meta( $post_id, '_club_address', true );
        $price = get_post_meta( $post_id, '_club_price', true );
        $tables = get_post_meta( $post_id, '_club_tables', true );
        $description = get_post_meta( $post_id, '_club_description', true );
        
        if ( empty( $address ) && empty( $price ) && empty( $tables ) && empty( $description ) ) {
            return '';
        }
        
        $output = '<div class="club-basic-info">';
        $output .= '<h3>' . __( 'Thông tin cơ bản', 'clubs-manager' ) . '</h3>';
        $output .= '<div class="info-grid">';
        
        if ( $address ) {
            $output .= '<div class="info-item">';
            $output .= '<span class="info-label">' . __( 'Địa chỉ:', 'clubs-manager' ) . '</span>';
            $output .= '<span class="info-value">' . esc_html( $address ) . '</span>';
            $output .= '</div>';
        }
        
        if ( $price ) {
            $output .= '<div class="info-item">';
            $output .= '<span class="info-label">' . __( 'Giá cả:', 'clubs-manager' ) . '</span>';
            $output .= '<span class="info-value">' . number_format( $price, 0, ',', '.' ) . ' VNĐ/giờ</span>';
            $output .= '</div>';
        }
        
        if ( $tables ) {
            $output .= '<div class="info-item">';
            $output .= '<span class="info-label">' . __( 'Số bàn:', 'clubs-manager' ) . '</span>';
            $output .= '<span class="info-value">' . esc_html( $tables ) . ' bàn</span>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        if ( $description ) {
            $output .= '<div class="club-description">';
            $output .= '<p>' . wp_kses_post( nl2br( $description ) ) . '</p>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render contact information
     */
    public static function render_contact_info( $post_id ) {
        $phone = get_post_meta( $post_id, '_club_phone', true );
        $email = get_post_meta( $post_id, '_club_email', true );
        $website = get_post_meta( $post_id, '_club_website', true );
        $facebook = get_post_meta( $post_id, '_club_facebook', true );
        
        if ( empty( $phone ) && empty( $email ) && empty( $website ) && empty( $facebook ) ) {
            return '';
        }
        
        $output = '<div class="club-contact-info">';
        $output .= '<h3>' . __( 'Thông tin liên hệ', 'clubs-manager' ) . '</h3>';
        $output .= '<div class="contact-grid">';
        
        if ( $phone ) {
            $output .= '<div class="contact-item">';
            $output .= '<span class="contact-icon">📞</span>';
            $output .= '<a href="tel:' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a>';
            $output .= '</div>';
        }
        
        if ( $email ) {
            $output .= '<div class="contact-item">';
            $output .= '<span class="contact-icon">✉️</span>';
            $output .= '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
            $output .= '</div>';
        }
        
        if ( $website ) {
            $output .= '<div class="contact-item">';
            $output .= '<span class="contact-icon">🌐</span>';
            $output .= '<a href="' . esc_url( $website ) . '" target="_blank" rel="noopener">' . __( 'Website', 'clubs-manager' ) . '</a>';
            $output .= '</div>';
        }
        
        if ( $facebook ) {
            $output .= '<div class="contact-item">';
            $output .= '<span class="contact-icon">📘</span>';
            $output .= '<a href="' . esc_url( $facebook ) . '" target="_blank" rel="noopener">Facebook</a>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render facilities
     */
    public static function render_facilities( $post_id ) {
        $facilities = array(
            '_club_parking' => __( 'Chỗ để xe', 'clubs-manager' ),
            '_club_wifi' => __( 'WiFi miễn phí', 'clubs-manager' ),
            '_club_food_service' => __( 'Dịch vụ ăn uống', 'clubs-manager' ),
            '_club_air_conditioning' => __( 'Điều hòa', 'clubs-manager' ),
            '_club_cue_rental' => __( 'Cho thuê cơ', 'clubs-manager' ),
        );
        
        $available_facilities = array();
        foreach ( $facilities as $meta_key => $label ) {
            if ( get_post_meta( $post_id, $meta_key, true ) === '1' ) {
                $available_facilities[] = $label;
            }
        }
        
        if ( empty( $available_facilities ) ) {
            return '';
        }
        
        $output = '<div class="club-facilities">';
        $output .= '<h3>' . __( 'Tiện ích', 'clubs-manager' ) . '</h3>';
        $output .= '<div class="facilities-list">';
        
        foreach ( $available_facilities as $facility ) {
            $output .= '<span class="facility-item">✅ ' . esc_html( $facility ) . '</span>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render opening hours
     */
    public static function render_opening_hours( $post_id ) {
        $days = array(
            'monday'    => __( 'Thứ 2', 'clubs-manager' ),
            'tuesday'   => __( 'Thứ 3', 'clubs-manager' ),
            'wednesday' => __( 'Thứ 4', 'clubs-manager' ),
            'thursday'  => __( 'Thứ 5', 'clubs-manager' ),
            'friday'    => __( 'Thứ 6', 'clubs-manager' ),
            'saturday'  => __( 'Thứ 7', 'clubs-manager' ),
            'sunday'    => __( 'Chủ nhật', 'clubs-manager' ),
        );
        
        $has_hours = false;
        foreach ( $days as $day => $label ) {
            $hours = get_post_meta( $post_id, '_club_hours_' . $day, true );
            $closed = get_post_meta( $post_id, '_club_closed_' . $day, true );
            if ( ! empty( $hours ) || $closed === '1' ) {
                $has_hours = true;
                break;
            }
        }
        
        if ( ! $has_hours ) {
            return '';
        }
        
        $output = '<div class="club-opening-hours">';
        $output .= '<h3>' . __( 'Giờ mở cửa', 'clubs-manager' ) . '</h3>';
        $output .= '<div class="hours-list">';
        
        foreach ( $days as $day => $label ) {
            $hours = get_post_meta( $post_id, '_club_hours_' . $day, true );
            $closed = get_post_meta( $post_id, '_club_closed_' . $day, true );
            
            $output .= '<div class="hours-item">';
            $output .= '<span class="day-label">' . esc_html( $label ) . ':</span>';
            
            if ( $closed === '1' ) {
                $output .= '<span class="hours-closed">' . __( 'Đóng cửa', 'clubs-manager' ) . '</span>';
            } elseif ( ! empty( $hours ) ) {
                $output .= '<span class="hours-time">' . esc_html( $hours ) . '</span>';
            } else {
                $output .= '<span class="hours-unknown">' . __( 'Liên hệ', 'clubs-manager' ) . '</span>';
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render gallery
     */
    public static function render_gallery( $post_id ) {
        $gallery = get_post_meta( $post_id, '_club_gallery', true );
        
        if ( empty( $gallery ) ) {
            return '';
        }
        
        $gallery_ids = explode( ',', $gallery );
        $gallery_ids = array_filter( $gallery_ids );
        
        if ( empty( $gallery_ids ) ) {
            return '';
        }
        
        $output = '<div class="club-gallery">';
        $output .= '<h3>' . __( 'Thư viện ảnh', 'clubs-manager' ) . '</h3>';
        $output .= '<div class="gallery-grid">';
        
        foreach ( $gallery_ids as $attachment_id ) {
            $image_url = wp_get_attachment_image_url( $attachment_id, 'medium' );
            $full_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
            $alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
            
            if ( $image_url ) {
                $output .= '<div class="gallery-item">';
                $output .= '<a href="' . esc_url( $full_image_url ) . '" class="gallery-link" data-lightbox="club-gallery">';
                $output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt_text ) . '" loading="lazy" />';
                $output .= '</a>';
                $output .= '</div>';
            }
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render map
     */
    public static function render_map( $post_id ) {
        $address = get_post_meta( $post_id, '_club_address', true );
        
        if ( empty( $address ) ) {
            return '';
        }
        
        $options = get_option( 'clubs_manager_options' );
        $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
        
        if ( empty( $api_key ) ) {
            return '';
        }
        
        $output = '<div class="club-map">';
        $output .= '<h3>' . __( 'Bản đồ', 'clubs-manager' ) . '</h3>';
        $output .= '<div id="club-map-' . $post_id . '" class="club-map-container" data-address="' . esc_attr( $address ) . '" data-title="' . esc_attr( get_the_title( $post_id ) ) . '"></div>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Add structured data for SEO
     */
    public static function add_structured_data() {
        if ( ! is_singular( 'billiard-club' ) ) {
            return;
        }
        
        global $post;
        
        $address = get_post_meta( $post->ID, '_club_address', true );
        $phone = get_post_meta( $post->ID, '_club_phone', true );
        $email = get_post_meta( $post->ID, '_club_email', true );
        $website = get_post_meta( $post->ID, '_club_website', true );
        $price = get_post_meta( $post->ID, '_club_price', true );
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => get_the_title( $post->ID ),
            'description' => get_the_excerpt( $post->ID ),
            'url' => get_permalink( $post->ID ),
        );
        
        if ( $address ) {
            $structured_data['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
            );
        }
        
        if ( $phone ) {
            $structured_data['telephone'] = $phone;
        }
        
        if ( $email ) {
            $structured_data['email'] = $email;
        }
        
        if ( $website ) {
            $structured_data['url'] = $website;
        }
        
        if ( $price ) {
            $structured_data['priceRange'] = number_format( $price, 0, ',', '.' ) . ' VNĐ/giờ';
        }
        
        if ( has_post_thumbnail( $post->ID ) ) {
            $structured_data['image'] = get_the_post_thumbnail_url( $post->ID, 'large' );
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode( $structured_data ) . '</script>';
    }
    
    /**
     * Add body classes
     */
    public static function body_class( $classes ) {
        if ( is_singular( 'billiard-club' ) ) {
            $classes[] = 'single-billiard-club';
        }
        
        if ( is_post_type_archive( 'billiard-club' ) || is_tax( 'club-area' ) ) {
            $classes[] = 'archive-billiard-club';
        }
        
        return $classes;
    }
    
    /**
     * Enqueue lightbox scripts
     */
    public static function enqueue_lightbox_scripts() {
        if ( is_singular( 'billiard-club' ) ) {
            wp_enqueue_script( 'clubs-lightbox', CLUBS_MANAGER_URL . 'assets/js/lightbox.min.js', array( 'jquery' ), CLUBS_MANAGER_VERSION, true );
            wp_enqueue_style( 'clubs-lightbox', CLUBS_MANAGER_URL . 'assets/css/lightbox.min.css', array(), CLUBS_MANAGER_VERSION );
        }
    }
}