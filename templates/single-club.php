<?php
/**
 * Template for displaying a single billiard club
 */

get_header(); ?>

<div class="container clubs-single-container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('club-single'); ?>>
            
            <header class="club-header">
                <h1 class="club-title"><?php the_title(); ?></h1>
                
                <?php $areas = get_the_terms(get_the_ID(), 'club-area'); ?>
                <?php if ($areas && !is_wp_error($areas)) : ?>
                    <div class="club-areas">
                        <?php foreach ($areas as $area) : ?>
                            <span class="club-area-tag"><?php echo esc_html($area->name); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </header>
            
            <div class="club-content-wrapper">
                <div class="club-main-content">
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="club-featured-image">
                            <?php the_post_thumbnail('large', array('class' => 'featured-img')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php $gallery = get_post_meta(get_the_ID(), '_club_gallery', true); ?>
                    <?php if ($gallery && is_array($gallery)) : ?>
                        <div class="club-gallery">
                            <h3><?php _e('Thư viện hình ảnh', 'clubs-manager'); ?></h3>
                            <div class="gallery-grid">
                                <?php foreach ($gallery as $image_id) : ?>
                                    <?php $image_url = wp_get_attachment_image_url($image_id, 'medium'); ?>
                                    <?php $full_url = wp_get_attachment_image_url($image_id, 'full'); ?>
                                    <?php if ($image_url) : ?>
                                        <a href="<?php echo esc_url($full_url); ?>" class="gallery-item" data-lightbox="club-gallery">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="club-description">
                        <h3><?php _e('Mô tả', 'clubs-manager'); ?></h3>
                        <?php the_content(); ?>
                    </div>
                    
                    <?php $hours = get_post_meta(get_the_ID(), '_club_hours', true); ?>
                    <?php if ($hours && is_array($hours)) : ?>
                        <div class="club-hours">
                            <h3><?php _e('Giờ mở cửa', 'clubs-manager'); ?></h3>
                            <table class="hours-table">
                                <?php
                                $days = array(
                                    'monday' => __('Thứ Hai', 'clubs-manager'),
                                    'tuesday' => __('Thứ Ba', 'clubs-manager'),
                                    'wednesday' => __('Thứ Tư', 'clubs-manager'),
                                    'thursday' => __('Thứ Năm', 'clubs-manager'),
                                    'friday' => __('Thứ Sáu', 'clubs-manager'),
                                    'saturday' => __('Thứ Bảy', 'clubs-manager'),
                                    'sunday' => __('Chủ Nhật', 'clubs-manager'),
                                );
                                ?>
                                <?php foreach ($days as $day => $label) : ?>
                                    <?php $day_hours = isset($hours[$day]) ? $hours[$day] : array(); ?>
                                    <tr>
                                        <td class="day-label"><?php echo esc_html($label); ?></td>
                                        <td class="day-hours">
                                            <?php if (isset($day_hours['closed']) && $day_hours['closed']) : ?>
                                                <span class="closed"><?php _e('Đóng cửa', 'clubs-manager'); ?></span>
                                            <?php elseif (!empty($day_hours['open']) && !empty($day_hours['close'])) : ?>
                                                <?php echo esc_html($day_hours['open'] . ' - ' . $day_hours['close']); ?>
                                            <?php else : ?>
                                                <span class="no-info">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <div class="club-sidebar">
                    
                    <div class="club-info-card">
                        <h3><?php _e('Thông tin chi tiết', 'clubs-manager'); ?></h3>
                        
                        <?php $price = get_post_meta(get_the_ID(), '_club_price', true); ?>
                        <?php if ($price) : ?>
                            <div class="info-item price">
                                <span class="label"><?php _e('Giá:', 'clubs-manager'); ?></span>
                                <span class="value price-value"><?php echo number_format($price, 0, ',', '.'); ?> VNĐ/giờ</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php $tables = get_post_meta(get_the_ID(), '_club_tables', true); ?>
                        <?php if ($tables) : ?>
                            <div class="info-item">
                                <span class="label"><?php _e('Số bàn bi-a:', 'clubs-manager'); ?></span>
                                <span class="value"><?php echo esc_html($tables); ?> bàn</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php $parking = get_post_meta(get_the_ID(), '_club_parking', true); ?>
                        <div class="info-item">
                            <span class="label"><?php _e('Chỗ đậu xe:', 'clubs-manager'); ?></span>
                            <span class="value parking-<?php echo esc_attr($parking); ?>">
                                <?php echo $parking === 'yes' ? __('Có', 'clubs-manager') : __('Không', 'clubs-manager'); ?>
                            </span>
                        </div>
                        
                        <?php $address = get_post_meta(get_the_ID(), '_club_address', true); ?>
                        <?php if ($address) : ?>
                            <div class="info-item address">
                                <span class="label"><?php _e('Địa chỉ:', 'clubs-manager'); ?></span>
                                <span class="value"><?php echo esc_html($address); ?></span>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <div class="club-contact-card">
                        <h3><?php _e('Liên hệ', 'clubs-manager'); ?></h3>
                        
                        <?php $phone = get_post_meta(get_the_ID(), '_club_phone', true); ?>
                        <?php if ($phone) : ?>
                            <div class="contact-item">
                                <span class="icon">📞</span>
                                <a href="tel:<?php echo esc_attr($phone); ?>" class="contact-link"><?php echo esc_html($phone); ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php $email = get_post_meta(get_the_ID(), '_club_email', true); ?>
                        <?php if ($email) : ?>
                            <div class="contact-item">
                                <span class="icon">✉️</span>
                                <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-link"><?php echo esc_html($email); ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php $website = get_post_meta(get_the_ID(), '_club_website', true); ?>
                        <?php if ($website) : ?>
                            <div class="contact-item">
                                <span class="icon">🌐</span>
                                <a href="<?php echo esc_url($website); ?>" target="_blank" class="contact-link"><?php _e('Website', 'clubs-manager'); ?></a>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <?php
                    $lat = get_post_meta(get_the_ID(), '_club_lat', true);
                    $lng = get_post_meta(get_the_ID(), '_club_lng', true);
                    $options = get_option('clubs_manager_options');
                    $api_key = isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : '';
                    ?>
                    
                    <?php if ($lat && $lng && $api_key) : ?>
                        <div class="club-map-card">
                            <h3><?php _e('Vị trí', 'clubs-manager'); ?></h3>
                            <div id="single-club-map" style="height: 250px; width: 100%;"></div>
                            
                            <script>
                            function initSingleClubMap() {
                                var clubLocation = {lat: <?php echo floatval($lat); ?>, lng: <?php echo floatval($lng); ?>};
                                var map = new google.maps.Map(document.getElementById('single-club-map'), {
                                    zoom: 15,
                                    center: clubLocation
                                });
                                
                                var marker = new google.maps.Marker({
                                    position: clubLocation,
                                    map: map,
                                    title: '<?php echo esc_js(get_the_title()); ?>'
                                });
                            }
                            </script>
                            
                            <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr($api_key); ?>&callback=initSingleClubMap"></script>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
            
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>