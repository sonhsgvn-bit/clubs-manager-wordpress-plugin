/**
 * Clubs Manager Frontend JavaScript
 * 
 * Handles AJAX search, filtering, and interactive features
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        ClubsManager.init();
    });

    // Main ClubsManager object
    window.ClubsManager = {
        // Configuration
        config: {
            ajaxUrl: clubs_ajax.ajax_url,
            nonce: clubs_ajax.nonce,
            loadingClass: 'clubs-loading',
            debounceDelay: 500
        },

        // Debounce utility
        debounce: function(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Initialize all functionality
        init: function() {
            this.initSearch();
            this.initFilters();
            this.initViewToggle();
            this.initLazyLoading();
            this.initLightbox();
            this.initMaps();
            this.initQuickView();
            this.initInfiniteScroll();
        },

        // Initialize search functionality
        initSearch: function() {
            var self = this;
            var $searchForm = $('#clubs-search-form');
            var $searchResults = $('#clubs-search-results');
            var $loading = $('#clubs-search-loading');

            if (!$searchForm.length) return;

            // Debounced search function
            var debouncedSearch = this.debounce(function() {
                self.performSearch();
            }, this.config.debounceDelay);

            // Bind search events
            $searchForm.on('submit', function(e) {
                e.preventDefault();
                self.performSearch();
            });

            // Real-time search on input changes
            $searchForm.find('input, select').on('input change', debouncedSearch);

            // Clear results when form is reset
            $searchForm.on('reset', function() {
                $searchResults.empty();
            });
        },

        // Perform AJAX search
        performSearch: function() {
            var $form = $('#clubs-search-form');
            var $results = $('#clubs-search-results');
            var $loading = $('#clubs-search-loading');
            var isAjax = $form.closest('.clubs-search-container').data('ajax') === 'true';

            if (!isAjax) {
                return; // Let form submit normally
            }

            var formData = $form.serializeArray();
            var searchData = {
                action: 'club_search',
                nonce: this.config.nonce,
                page: 1,
                per_page: $form.closest('.clubs-search-container').data('per-page') || 12
            };

            // Convert form data to object
            $.each(formData, function(i, field) {
                if (field.name === 'facilities[]') {
                    if (!searchData.facilities) searchData.facilities = [];
                    searchData.facilities.push(field.value);
                } else {
                    searchData[field.name] = field.value;
                }
            });

            // Show loading state
            $loading.show();
            $results.addClass(this.config.loadingClass);

            // Perform AJAX request
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: searchData,
                success: function(response) {
                    if (response.success) {
                        ClubsManager.renderSearchResults(response.data, $results);
                    } else {
                        $results.html('<div class="clubs-search-error"><p>Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.</p></div>');
                    }
                },
                error: function() {
                    $results.html('<div class="clubs-search-error"><p>Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng.</p></div>');
                },
                complete: function() {
                    $loading.hide();
                    $results.removeClass(ClubsManager.config.loadingClass);
                }
            });
        },

        // Render search results
        renderSearchResults: function(data, $container) {
            if (!data.clubs || data.clubs.length === 0) {
                $container.html('<div class="clubs-no-results"><p>' + (data.message || 'Không tìm thấy câu lạc bộ nào.') + '</p></div>');
                return;
            }

            var html = '<div class="clubs-search-results-header">';
            html += '<p class="results-count">Tìm thấy ' + data.found_posts + ' câu lạc bộ</p>';
            html += '</div>';

            html += '<div class="clubs-grid">';
            $.each(data.clubs, function(index, club) {
                html += ClubsManager.renderClubCard(club);
            });
            html += '</div>';

            if (data.max_pages > 1) {
                html += ClubsManager.renderPagination(data);
            }

            $container.html(html);

            // Reinitialize features for new content
            this.initLazyLoading();
            this.initQuickView();
        },

        // Render club card HTML
        renderClubCard: function(club) {
            var html = '<article class="club-card" data-club-id="' + club.id + '">';
            
            // Image
            html += '<div class="club-card-image">';
            if (club.thumbnail) {
                html += '<a href="' + club.permalink + '" class="image-link">';
                html += '<img src="' + club.thumbnail + '" alt="' + club.title + '" loading="lazy">';
                html += '</a>';
            } else {
                html += '<div class="club-card-no-image"><div class="no-image-placeholder">';
                html += '<span class="dashicons dashicons-format-image"></span>';
                html += '<span class="no-image-text">Không có ảnh</span>';
                html += '</div></div>';
            }
            html += '<div class="club-card-actions">';
            html += '<button class="quick-view-btn" data-club-id="' + club.id + '" title="Xem nhanh">';
            html += '<span class="dashicons dashicons-visibility"></span></button>';
            if (club.phone) {
                html += '<a href="tel:' + club.phone + '" class="quick-call-btn" title="Gọi ngay">';
                html += '<span class="dashicons dashicons-phone"></span></a>';
            }
            html += '</div></div>';

            // Content
            html += '<div class="club-card-content">';
            html += '<header class="club-card-header">';
            html += '<h3 class="club-title"><a href="' + club.permalink + '">' + club.title + '</a></h3>';
            if (club.areas && club.areas.length > 0) {
                html += '<div class="club-areas"><span class="area-icon">📍</span>';
                html += '<span class="area-names">' + club.areas.slice(0, 2).join(', ') + '</span>';
                if (club.areas.length > 2) {
                    html += '<span class="more-areas">+' + (club.areas.length - 2) + '</span>';
                }
                html += '</div>';
            }
            html += '</header>';

            html += '<div class="club-card-body">';
            if (club.address) {
                html += '<div class="club-address"><span class="address-icon">🏠</span>';
                html += '<span class="address-text">' + club.address + '</span></div>';
            }

            html += '<div class="club-meta">';
            if (club.formatted_price) {
                html += '<div class="club-price"><span class="price-icon">💰</span>';
                html += '<span class="price-value">' + club.formatted_price + '</span></div>';
            }
            if (club.tables) {
                html += '<div class="club-tables"><span class="tables-icon">🎱</span>';
                html += '<span class="tables-count">' + club.tables + ' bàn</span></div>';
            }
            html += '</div>';

            if (club.excerpt) {
                html += '<div class="club-excerpt">' + club.excerpt + '</div>';
            }

            if (club.facilities && club.facilities.length > 0) {
                html += '<div class="club-facilities"><div class="facilities-list">';
                club.facilities.slice(0, 3).forEach(function(facility) {
                    html += '<span class="facility-item">✅ ' + facility + '</span>';
                });
                if (club.facilities.length > 3) {
                    html += '<span class="more-facilities">+' + (club.facilities.length - 3) + ' tiện ích</span>';
                }
                html += '</div></div>';
            }
            html += '</div>';

            // Footer
            html += '<footer class="club-card-footer">';
            html += '<div class="club-actions">';
            html += '<a href="' + club.permalink + '" class="club-details-btn primary-btn">Xem chi tiết</a>';
            if (club.phone) {
                html += '<a href="tel:' + club.phone + '" class="club-call-btn secondary-btn">';
                html += '<span class="call-icon">📞</span>Gọi</a>';
            }
            html += '</div></footer>';

            html += '</div></article>';

            return html;
        },

        // Render pagination
        renderPagination: function(data) {
            var html = '<nav class="clubs-pagination">';
            html += '<ul class="page-numbers">';

            // Previous
            if (data.current_page > 1) {
                html += '<li><a href="#" class="page-numbers" data-page="' + (data.current_page - 1) + '">‹ Trước</a></li>';
            }

            // Page numbers
            var startPage = Math.max(1, data.current_page - 2);
            var endPage = Math.min(data.max_pages, data.current_page + 2);

            for (var i = startPage; i <= endPage; i++) {
                if (i === data.current_page) {
                    html += '<li><span class="page-numbers current">' + i + '</span></li>';
                } else {
                    html += '<li><a href="#" class="page-numbers" data-page="' + i + '">' + i + '</a></li>';
                }
            }

            // Next
            if (data.current_page < data.max_pages) {
                html += '<li><a href="#" class="page-numbers" data-page="' + (data.current_page + 1) + '">Sau ›</a></li>';
            }

            html += '</ul></nav>';

            return html;
        },

        // Initialize filters
        initFilters: function() {
            var self = this;

            // Sort dropdown
            $('#clubs-sort').on('change', function() {
                var sortValue = $(this).val();
                var url = new URL(window.location);
                
                var parts = sortValue.split('_');
                url.searchParams.set('orderby', parts[0]);
                url.searchParams.set('order', parts[1].toUpperCase());
                
                window.location.href = url.toString();
            });

            // Filter by facilities
            $('.filter-checkboxes input[type="checkbox"]').on('change', function() {
                self.performSearch();
            });
        },

        // Initialize view toggle
        initViewToggle: function() {
            $('.view-toggle').on('click', function() {
                var $this = $(this);
                var view = $this.data('view');
                var $container = $('.clubs-grid').closest('.clubs-shortcode-container, .clubs-listing-section');

                $this.addClass('active').siblings().removeClass('active');

                if (view === 'list') {
                    $container.addClass('clubs-list-layout').removeClass('clubs-grid-layout');
                } else {
                    $container.addClass('clubs-grid-layout').removeClass('clubs-list-layout');
                }

                // Save preference in localStorage
                localStorage.setItem('clubs_view_preference', view);
            });

            // Load saved view preference
            var savedView = localStorage.getItem('clubs_view_preference');
            if (savedView) {
                $('.view-toggle[data-view="' + savedView + '"]').trigger('click');
            }
        },

        // Initialize lazy loading
        initLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                var imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                $('.club-card img[data-src]').each(function() {
                    imageObserver.observe(this);
                });
            }
        },

        // Initialize lightbox for gallery
        initLightbox: function() {
            if (typeof $.fn.magnificPopup !== 'undefined') {
                $('.gallery-grid').magnificPopup({
                    delegate: '.gallery-link',
                    type: 'image',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0, 1]
                    },
                    image: {
                        tError: '<a href="%url%">Không thể tải ảnh.</a>'
                    }
                });
            }
        },

        // Initialize maps
        initMaps: function() {
            // Maps initialization is handled in the maps.js file
            // This is just a placeholder for any additional map-related JS
        },

        // Initialize quick view modal
        initQuickView: function() {
            var self = this;

            $(document).on('click', '.quick-view-btn', function(e) {
                e.preventDefault();
                var clubId = $(this).data('club-id');
                self.showQuickView(clubId);
            });

            // Close modal events
            $(document).on('click', '.clubs-modal-overlay, .clubs-modal-close', function(e) {
                if (e.target === this) {
                    self.closeQuickView();
                }
            });

            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) { // Escape key
                    self.closeQuickView();
                }
            });
        },

        // Show quick view modal
        showQuickView: function(clubId) {
            // Create modal if it doesn't exist
            if (!$('#clubs-quick-view-modal').length) {
                $('body').append(
                    '<div id="clubs-quick-view-modal" class="clubs-modal-overlay">' +
                    '<div class="clubs-modal-content">' +
                    '<button class="clubs-modal-close">&times;</button>' +
                    '<div class="clubs-modal-body"></div>' +
                    '</div>' +
                    '</div>'
                );
            }

            var $modal = $('#clubs-quick-view-modal');
            var $body = $modal.find('.clubs-modal-body');

            // Show loading
            $body.html('<div class="clubs-modal-loading"><p>Đang tải...</p></div>');
            $modal.fadeIn(300);

            // Load club data via AJAX
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'club_quick_view',
                    club_id: clubId,
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $body.html(response.data.html);
                    } else {
                        $body.html('<div class="clubs-modal-error"><p>Không thể tải thông tin câu lạc bộ.</p></div>');
                    }
                },
                error: function() {
                    $body.html('<div class="clubs-modal-error"><p>Có lỗi xảy ra. Vui lòng thử lại.</p></div>');
                }
            });
        },

        // Close quick view modal
        closeQuickView: function() {
            $('#clubs-quick-view-modal').fadeOut(300);
        },

        // Initialize infinite scroll
        initInfiniteScroll: function() {
            var self = this;
            var $window = $(window);
            var $document = $(document);
            var loading = false;

            if (!$('.clubs-grid').data('infinite-scroll')) return;

            $window.on('scroll', function() {
                if (loading) return;

                var scrollTop = $window.scrollTop();
                var windowHeight = $window.height();
                var documentHeight = $document.height();

                if (scrollTop + windowHeight >= documentHeight - 200) {
                    loading = true;
                    self.loadMoreClubs();
                }
            });
        },

        // Load more clubs for infinite scroll
        loadMoreClubs: function() {
            var self = this;
            var $grid = $('.clubs-grid');
            var currentPage = parseInt($grid.data('current-page') || 1);
            var maxPages = parseInt($grid.data('max-pages') || 1);

            if (currentPage >= maxPages) return;

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'club_load_more',
                    page: currentPage + 1,
                    per_page: 12,
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success && response.data.clubs.length > 0) {
                        var html = '';
                        $.each(response.data.clubs, function(index, club) {
                            html += self.renderClubCard(club);
                        });
                        
                        $grid.append(html);
                        $grid.data('current-page', currentPage + 1);
                        
                        // Reinitialize features for new content
                        self.initLazyLoading();
                        self.initQuickView();
                    }
                },
                complete: function() {
                    loading = false;
                }
            });
        },

        // Utility function to scroll to element
        scrollTo: function(element, offset) {
            offset = offset || 0;
            $('html, body').animate({
                scrollTop: $(element).offset().top + offset
            }, 500);
        },

        // Utility function to show/hide loading state
        setLoading: function($element, state) {
            if (state) {
                $element.addClass(this.config.loadingClass);
            } else {
                $element.removeClass(this.config.loadingClass);
            }
        }
    };

    // Global functions for maps integration
    window.clubsMapInit = function(mapId) {
        // This function is called by Google Maps API
        if (window.ClubsManager && window.ClubsManager.initMaps) {
            window.ClubsManager.initMaps();
        }
    };

})(jQuery);