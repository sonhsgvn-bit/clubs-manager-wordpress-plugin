<?php
/**
 * Single Club Template
 *
 * @package ClubsManager
 */

get_header(); ?>

<div class="clubs-manager-single">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('club-single'); ?>>
            <header class="club-header">
                <h1 class="club-title"><?php the_title(); ?></h1>
                <?php
                $terms = get_the_terms(get_the_ID(), 'club_area');
                if ($terms && !is_wp_error($terms)) :
                ?>
                    <div class="club-area">
                        <span class="area-label"><?php _e('Khu vực:', 'clubs-manager'); ?></span>
                        <?php
                        $term_links = array();
                        foreach ($terms as $term) {
                            $term_links[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                        }
                        echo implode(', ', $term_links);
                        ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="club-content">
                <div class="club-main">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="club-featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="club-description">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    $club_gallery = get_post_meta(get_the_ID(), 'club_gallery', true);
                    if (!empty($club_gallery)) :
                        $gallery_ids = explode(',', $club_gallery);
                    ?>
                        <div class="club-gallery">
                            <h3><?php _e('Thư viện ảnh', 'clubs-manager'); ?></h3>
                            <div class="gallery-grid">
                                <?php foreach ($gallery_ids as $image_id) : ?>
                                    <?php $image_url = wp_get_attachment_image_src($image_id, 'medium')[0]; ?>
                                    <?php $full_image_url = wp_get_attachment_image_src($image_id, 'large')[0]; ?>
                                    <div class="gallery-item">
                                        <a href="<?php echo esc_url($full_image_url); ?>" data-lightbox="club-gallery">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="club-sidebar">
                    <div class="club-details">
                        <h3><?php _e('Thông tin chi tiết', 'clubs-manager'); ?></h3>
                        
                        <?php
                        $club_address = get_post_meta(get_the_ID(), 'club_address', true);
                        $club_price = get_post_meta(get_the_ID(), 'club_price', true);
                        $club_tables = get_post_meta(get_the_ID(), 'club_tables', true);
                        $club_parking = get_post_meta(get_the_ID(), 'club_parking', true);
                        $club_phone = get_post_meta(get_the_ID(), 'club_phone', true);
                        $club_email = get_post_meta(get_the_ID(), 'club_email', true);
                        $club_website = get_post_meta(get_the_ID(), 'club_website', true);
                        ?>

                        <div class="detail-item">
                            <span class="detail-label"><?php _e('Địa chỉ:', 'clubs-manager'); ?></span>
                            <span class="detail-value"><?php echo esc_html($club_address); ?></span>
                        </div>

                        <?php if ($club_price) : ?>
                            <div class="detail-item">
                                <span class="detail-label"><?php _e('Giá:', 'clubs-manager'); ?></span>
                                <span class="detail-value"><?php echo number_format($club_price); ?> VNĐ/giờ</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($club_tables) : ?>
                            <div class="detail-item">
                                <span class="detail-label"><?php _e('Số bàn bi-a:', 'clubs-manager'); ?></span>
                                <span class="detail-value"><?php echo esc_html($club_tables); ?> bàn</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($club_parking) : ?>
                            <div class="detail-item">
                                <span class="detail-label"><?php _e('Chỗ đậu xe:', 'clubs-manager'); ?></span>
                                <span class="detail-value">
                                    <?php echo $club_parking === 'yes' ? __('Có', 'clubs-manager') : __('Không', 'clubs-manager'); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($club_phone) : ?>
                            <div class="detail-item">
                                <span class="detail-label"><?php _e('Điện thoại:', 'clubs-manager'); ?></span>
                                <span class="detail-value">
                                    <a href="tel:<?php echo esc_attr($club_phone); ?>"><?php echo esc_html($club_phone); ?></a>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($club_email) : ?>
                            <div class="detail-item">
                                <span class="detail-label"><?php _e('Email:', 'clubs-manager'); ?></span>
                                <span class="detail-value">
                                    <a href="mailto:<?php echo esc_attr($club_email); ?>"><?php echo esc_html($club_email); ?></a>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($club_website) : ?>
                            <div class="detail-item">
                                <span class="detail-label"><?php _e('Website:', 'clubs-manager'); ?></span>
                                <span class="detail-value">
                                    <a href="<?php echo esc_url($club_website); ?>" target="_blank"><?php echo esc_html($club_website); ?></a>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    $club_hours = get_post_meta(get_the_ID(), 'club_hours', true);
                    if (!empty($club_hours)) :
                    ?>
                        <div class="club-hours">
                            <h3><?php _e('Giờ mở cửa', 'clubs-manager'); ?></h3>
                            <div class="hours-list">
                                <?php
                                $days = array(
                                    'monday' => __('Thứ 2', 'clubs-manager'),
                                    'tuesday' => __('Thứ 3', 'clubs-manager'),
                                    'wednesday' => __('Thứ 4', 'clubs-manager'),
                                    'thursday' => __('Thứ 5', 'clubs-manager'),
                                    'friday' => __('Thứ 6', 'clubs-manager'),
                                    'saturday' => __('Thứ 7', 'clubs-manager'),
                                    'sunday' => __('Chủ nhật', 'clubs-manager'),
                                );
                                ?>
                                <?php foreach ($days as $day_key => $day_label) : ?>
                                    <?php if (isset($club_hours[$day_key])) : ?>
                                        <div class="hours-item">
                                            <span class="day"><?php echo $day_label; ?>:</span>
                                            <span class="time"><?php echo esc_html($club_hours[$day_key]['open']); ?> - <?php echo esc_html($club_hours[$day_key]['close']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    $club_lat = get_post_meta(get_the_ID(), 'club_lat', true);
                    $club_lng = get_post_meta(get_the_ID(), 'club_lng', true);
                    if ($club_lat && $club_lng) :
                    ?>
                        <div class="club-map">
                            <h3><?php _e('Vị trí', 'clubs-manager'); ?></h3>
                            <div id="club-location-map" data-lat="<?php echo esc_attr($club_lat); ?>" data-lng="<?php echo esc_attr($club_lng); ?>" data-title="<?php echo esc_attr(get_the_title()); ?>"></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>