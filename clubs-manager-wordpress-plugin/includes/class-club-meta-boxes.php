<?php
/**
 * Club Meta Boxes Class
 * 
 * Handles the creation and management of meta boxes for club custom fields
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Meta_Boxes {
    
    /**
     * Register meta boxes
     */
    public static function register_meta_boxes() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post', array( __CLASS__, 'save_meta_data' ) );
    }
    
    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'club-basic-info',
            __( 'Thông tin cơ bản', 'clubs-manager' ),
            array( __CLASS__, 'basic_info_callback' ),
            'billiard-club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club-contact-info',
            __( 'Thông tin liên hệ', 'clubs-manager' ),
            array( __CLASS__, 'contact_info_callback' ),
            'billiard-club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club-facilities',
            __( 'Tiện ích', 'clubs-manager' ),
            array( __CLASS__, 'facilities_callback' ),
            'billiard-club',
            'normal',
            'default'
        );
        
        add_meta_box(
            'club-opening-hours',
            __( 'Giờ mở cửa', 'clubs-manager' ),
            array( __CLASS__, 'opening_hours_callback' ),
            'billiard-club',
            'normal',
            'default'
        );
        
        add_meta_box(
            'club-gallery',
            __( 'Thư viện ảnh', 'clubs-manager' ),
            array( __CLASS__, 'gallery_callback' ),
            'billiard-club',
            'side',
            'default'
        );
    }
    
    /**
     * Basic info meta box callback
     */
    public static function basic_info_callback( $post ) {
        wp_nonce_field( 'club_meta_box_nonce', 'club_meta_box_nonce' );
        
        $address = get_post_meta( $post->ID, '_club_address', true );
        $price = get_post_meta( $post->ID, '_club_price', true );
        $tables = get_post_meta( $post->ID, '_club_tables', true );
        $description = get_post_meta( $post->ID, '_club_description', true );
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="club_address"><?php _e( 'Địa chỉ', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="text" id="club_address" name="club_address" value="<?php echo esc_attr( $address ); ?>" class="regular-text" />
                    <p class="description"><?php _e( 'Địa chỉ đầy đủ của câu lạc bộ', 'clubs-manager' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="club_price"><?php _e( 'Giá cả (VNĐ/giờ)', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="number" id="club_price" name="club_price" value="<?php echo esc_attr( $price ); ?>" class="regular-text" min="0" step="1000" />
                    <p class="description"><?php _e( 'Giá thuê bàn bi-a theo giờ (VNĐ)', 'clubs-manager' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="club_tables"><?php _e( 'Số bàn bi-a', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="number" id="club_tables" name="club_tables" value="<?php echo esc_attr( $tables ); ?>" class="small-text" min="1" />
                    <p class="description"><?php _e( 'Tổng số bàn bi-a có sẵn', 'clubs-manager' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="club_description"><?php _e( 'Mô tả chi tiết', 'clubs-manager' ); ?></label></th>
                <td>
                    <textarea id="club_description" name="club_description" rows="4" class="large-text"><?php echo esc_textarea( $description ); ?></textarea>
                    <p class="description"><?php _e( 'Mô tả chi tiết về câu lạc bộ, dịch vụ và tiện ích', 'clubs-manager' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Contact info meta box callback
     */
    public static function contact_info_callback( $post ) {
        $phone = get_post_meta( $post->ID, '_club_phone', true );
        $email = get_post_meta( $post->ID, '_club_email', true );
        $website = get_post_meta( $post->ID, '_club_website', true );
        $facebook = get_post_meta( $post->ID, '_club_facebook', true );
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="club_phone"><?php _e( 'Số điện thoại', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="tel" id="club_phone" name="club_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="club_email"><?php _e( 'Email', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="email" id="club_email" name="club_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="club_website"><?php _e( 'Website', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="url" id="club_website" name="club_website" value="<?php echo esc_url( $website ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="club_facebook"><?php _e( 'Facebook', 'clubs-manager' ); ?></label></th>
                <td>
                    <input type="url" id="club_facebook" name="club_facebook" value="<?php echo esc_url( $facebook ); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Facilities meta box callback
     */
    public static function facilities_callback( $post ) {
        $parking = get_post_meta( $post->ID, '_club_parking', true );
        $wifi = get_post_meta( $post->ID, '_club_wifi', true );
        $food_service = get_post_meta( $post->ID, '_club_food_service', true );
        $air_conditioning = get_post_meta( $post->ID, '_club_air_conditioning', true );
        $cue_rental = get_post_meta( $post->ID, '_club_cue_rental', true );
        
        ?>
        <table class="form-table">
            <tr>
                <th><?php _e( 'Tiện ích có sẵn', 'clubs-manager' ); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="club_parking" value="1" <?php checked( $parking, '1' ); ?> />
                            <?php _e( 'Chỗ để xe', 'clubs-manager' ); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="club_wifi" value="1" <?php checked( $wifi, '1' ); ?> />
                            <?php _e( 'WiFi miễn phí', 'clubs-manager' ); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="club_food_service" value="1" <?php checked( $food_service, '1' ); ?> />
                            <?php _e( 'Dịch vụ ăn uống', 'clubs-manager' ); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="club_air_conditioning" value="1" <?php checked( $air_conditioning, '1' ); ?> />
                            <?php _e( 'Điều hòa', 'clubs-manager' ); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="club_cue_rental" value="1" <?php checked( $cue_rental, '1' ); ?> />
                            <?php _e( 'Cho thuê cơ', 'clubs-manager' ); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Opening hours meta box callback
     */
    public static function opening_hours_callback( $post ) {
        $days = array(
            'monday'    => __( 'Thứ 2', 'clubs-manager' ),
            'tuesday'   => __( 'Thứ 3', 'clubs-manager' ),
            'wednesday' => __( 'Thứ 4', 'clubs-manager' ),
            'thursday'  => __( 'Thứ 5', 'clubs-manager' ),
            'friday'    => __( 'Thứ 6', 'clubs-manager' ),
            'saturday'  => __( 'Thứ 7', 'clubs-manager' ),
            'sunday'    => __( 'Chủ nhật', 'clubs-manager' )
        );
        
        ?>
        <table class="form-table">
            <?php foreach ( $days as $day => $label ) : 
                $hours = get_post_meta( $post->ID, '_club_hours_' . $day, true );
                $closed = get_post_meta( $post->ID, '_club_closed_' . $day, true );
            ?>
            <tr>
                <th><label for="club_hours_<?php echo $day; ?>"><?php echo $label; ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="club_closed_<?php echo $day; ?>" value="1" <?php checked( $closed, '1' ); ?> />
                        <?php _e( 'Đóng cửa', 'clubs-manager' ); ?>
                    </label>
                    <br>
                    <input type="text" id="club_hours_<?php echo $day; ?>" name="club_hours_<?php echo $day; ?>" value="<?php echo esc_attr( $hours ); ?>" placeholder="8:00 - 24:00" class="regular-text" />
                    <p class="description"><?php _e( 'Ví dụ: 8:00 - 24:00', 'clubs-manager' ); ?></p>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
    
    /**
     * Gallery meta box callback
     */
    public static function gallery_callback( $post ) {
        $gallery = get_post_meta( $post->ID, '_club_gallery', true );
        $gallery_ids = ! empty( $gallery ) ? explode( ',', $gallery ) : array();
        
        ?>
        <div id="club-gallery-container">
            <div id="club-gallery-images">
                <?php if ( ! empty( $gallery_ids ) ) : ?>
                    <?php foreach ( $gallery_ids as $attachment_id ) : 
                        $image = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                        if ( $image ) :
                    ?>
                        <div class="gallery-image" data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
                            <?php echo $image; ?>
                            <a href="#" class="remove-image" title="<?php _e( 'Xóa ảnh', 'clubs-manager' ); ?>">&times;</a>
                        </div>
                    <?php endif; endforeach; ?>
                <?php endif; ?>
            </div>
            
            <p>
                <button type="button" id="add-gallery-images" class="button"><?php _e( 'Thêm ảnh', 'clubs-manager' ); ?></button>
            </p>
            
            <input type="hidden" id="club_gallery" name="club_gallery" value="<?php echo esc_attr( $gallery ); ?>" />
        </div>
        
        <style>
        #club-gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .gallery-image {
            position: relative;
            border: 1px solid #ddd;
            padding: 5px;
            background: #fff;
        }
        .gallery-image img {
            display: block;
            max-width: 100px;
            height: auto;
        }
        .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3232;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .remove-image:hover {
            background: #a00;
            color: white;
        }
        </style>
        <?php
    }
    
    /**
     * Save meta data
     */
    public static function save_meta_data( $post_id ) {
        // Verify nonce
        if ( ! isset( $_POST['club_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['club_meta_box_nonce'], 'club_meta_box_nonce' ) ) {
            return;
        }
        
        // Check if user has permission
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        // Check if not autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check post type
        if ( get_post_type( $post_id ) !== 'billiard-club' ) {
            return;
        }
        
        // Basic info fields
        $basic_fields = array(
            'club_address', 'club_price', 'club_tables', 'club_description'
        );
        
        foreach ( $basic_fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                $value = sanitize_text_field( $_POST[$field] );
                if ( $field === 'club_description' ) {
                    $value = sanitize_textarea_field( $_POST[$field] );
                } elseif ( $field === 'club_price' || $field === 'club_tables' ) {
                    $value = absint( $_POST[$field] );
                }
                update_post_meta( $post_id, '_' . $field, $value );
            }
        }
        
        // Contact info fields
        $contact_fields = array(
            'club_phone', 'club_email', 'club_website', 'club_facebook'
        );
        
        foreach ( $contact_fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                $value = sanitize_text_field( $_POST[$field] );
                if ( in_array( $field, array( 'club_website', 'club_facebook' ) ) ) {
                    $value = esc_url_raw( $_POST[$field] );
                } elseif ( $field === 'club_email' ) {
                    $value = sanitize_email( $_POST[$field] );
                }
                update_post_meta( $post_id, '_' . $field, $value );
            }
        }
        
        // Facilities fields
        $facility_fields = array(
            'club_parking', 'club_wifi', 'club_food_service', 'club_air_conditioning', 'club_cue_rental'
        );
        
        foreach ( $facility_fields as $field ) {
            $value = isset( $_POST[$field] ) ? '1' : '0';
            update_post_meta( $post_id, '_' . $field, $value );
        }
        
        // Opening hours
        $days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
        
        foreach ( $days as $day ) {
            $hours_field = 'club_hours_' . $day;
            $closed_field = 'club_closed_' . $day;
            
            if ( isset( $_POST[$hours_field] ) ) {
                update_post_meta( $post_id, '_' . $hours_field, sanitize_text_field( $_POST[$hours_field] ) );
            }
            
            $closed_value = isset( $_POST[$closed_field] ) ? '1' : '0';
            update_post_meta( $post_id, '_' . $closed_field, $closed_value );
        }
        
        // Gallery
        if ( isset( $_POST['club_gallery'] ) ) {
            $gallery = sanitize_text_field( $_POST['club_gallery'] );
            update_post_meta( $post_id, '_club_gallery', $gallery );
        }
    }
}