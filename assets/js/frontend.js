/* Frontend JavaScript for Clubs Manager Plugin */

jQuery(document).ready(function($) {
    
    // AJAX Search Form
    if ($('#clubs-search-form[data-ajax="true"]').length) {
        initAjaxSearch();
    }
    
    // Search Form Reset
    $('#reset-search').on('click', function(e) {
        e.preventDefault();
        resetSearchForm();
    });
    
    // Search form initialization
    function initAjaxSearch() {
        $('#clubs-search-form').on('submit', function(e) {
            e.preventDefault();
            performAjaxSearch();
        });
        
        // Auto-search on input change (with debounce)
        var searchTimeout;
        $('#clubs-search-form input, #clubs-search-form select').on('change input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performAjaxSearch();
            }, 500);
        });
    }
    
    // Perform AJAX search
    function performAjaxSearch() {
        var $form = $('#clubs-search-form');
        var $results = $('#clubs-search-results');
        var $loading = $results.find('.loading');
        var $container = $results.find('.results-container');
        
        // Show loading
        $loading.show();
        $container.empty();
        
        // Prepare form data
        var formData = {
            action: 'clubs_search',
            nonce: clubsManager.nonce,
            club_name: $form.find('[name="club_name"]').val(),
            club_area: $form.find('[name="club_area"]').val(),
            min_price: $form.find('[name="min_price"]').val(),
            max_price: $form.find('[name="max_price"]').val(),
            min_tables: $form.find('[name="min_tables"]').val(),
            has_parking: $form.find('[name="has_parking"]').val()
        };
        
        // Make AJAX request
        $.ajax({
            url: clubsManager.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $loading.hide();
                
                if (response.success && response.data.html) {
                    $container.html(response.data.html);
                    // Trigger custom event for other scripts
                    $(document).trigger('clubs_search_complete', [response.data]);
                } else {
                    $container.html('<p class="no-clubs-found">' + clubsManager.strings.no_results + '</p>');
                }
            },
            error: function() {
                $loading.hide();
                $container.html('<p class="error">Có lỗi xảy ra trong quá trình tìm kiếm.</p>');
            }
        });
    }
    
    // Reset search form
    function resetSearchForm() {
        $('#clubs-search-form')[0].reset();
        $('#clubs-search-results .results-container').empty();
        $('#clubs-search-results .loading').hide();
        
        // If not AJAX form, redirect to archive page
        if (!$('#clubs-search-form[data-ajax="true"]').length) {
            window.location.href = window.location.pathname;
        } else {
            // For AJAX forms, trigger a search to show all results
            performAjaxSearch();
        }
    }
    
    // Smooth scrolling for anchor links
    $('a[href*="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Club card hover effects
    $('.club-card').hover(
        function() {
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );
    
    // Lightbox for gallery images (simple implementation)
    if ($('.gallery-item').length) {
        initSimpleLightbox();
    }
    
    function initSimpleLightbox() {
        var $lightbox = $('<div id="clubs-lightbox" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:9999;"><div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);max-width:90%;max-height:90%;"><img src="" style="max-width:100%;max-height:100%;"/><button style="position:absolute;top:10px;right:10px;background:white;border:none;padding:5px 10px;cursor:pointer;">×</button></div></div>');
        $('body').append($lightbox);
        
        $('.gallery-item').on('click', function(e) {
            e.preventDefault();
            var imageUrl = $(this).attr('href');
            $lightbox.find('img').attr('src', imageUrl);
            $lightbox.fadeIn(200);
        });
        
        $lightbox.on('click', function(e) {
            if (e.target === this || $(e.target).is('button')) {
                $lightbox.fadeOut(200);
            }
        });
        
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // ESC key
                $lightbox.fadeOut(200);
            }
        });
    }
    
    // Price formatting
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
    }
    
    // Phone number formatting
    $('.phone-link').each(function() {
        var phone = $(this).text().trim();
        var formatted = formatPhoneNumber(phone);
        if (formatted !== phone) {
            $(this).text(formatted);
        }
    });
    
    function formatPhoneNumber(phone) {
        // Simple Vietnamese phone number formatting
        var cleaned = phone.replace(/\D/g, '');
        if (cleaned.length === 10) {
            return cleaned.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
        } else if (cleaned.length === 11) {
            return cleaned.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
        }
        return phone;
    }
    
    // Scroll to top button
    var $scrollToTop = $('<button id="scroll-to-top" style="display:none;position:fixed;bottom:20px;right:20px;width:50px;height:50px;background:#007cba;color:white;border:none;border-radius:50%;cursor:pointer;z-index:1000;font-size:18px;">↑</button>');
    $('body').append($scrollToTop);
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $scrollToTop.fadeIn();
        } else {
            $scrollToTop.fadeOut();
        }
    });
    
    $scrollToTop.on('click', function() {
        $('html, body').animate({scrollTop: 0}, 500);
    });
    
    // Responsive table wrapper
    $('.hours-table').wrap('<div class="table-responsive"></div>');
    
    // Add loading class to forms during submission
    $('form').on('submit', function() {
        $(this).addClass('loading');
    });
    
    // Initialize tooltips if needed
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Custom event for map initialization
    $(document).on('clubs_maps_loaded', function() {
        // Maps have been loaded, can perform additional map-related operations
        console.log('Clubs Manager: Google Maps loaded successfully');
    });
    
    // Utility function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    // Persist search form values from URL parameters
    function populateSearchFormFromUrl() {
        var $form = $('#clubs-search-form');
        if ($form.length && window.location.search) {
            $form.find('[name="club_name"]').val(getUrlParameter('club_name'));
            $form.find('[name="club_area"]').val(getUrlParameter('club_area'));
            $form.find('[name="min_price"]').val(getUrlParameter('min_price'));
            $form.find('[name="max_price"]').val(getUrlParameter('max_price'));
            $form.find('[name="min_tables"]').val(getUrlParameter('min_tables'));
            $form.find('[name="has_parking"]').val(getUrlParameter('has_parking'));
        }
    }
    
    // Initialize form population
    populateSearchFormFromUrl();
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        populateSearchFormFromUrl();
        if ($('#clubs-search-form[data-ajax="true"]').length) {
            performAjaxSearch();
        }
    });
    
    // Lazy loading for images (simple implementation)
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    // Initialize lazy loading
    initLazyLoading();
    
    // Re-initialize after AJAX search
    $(document).on('clubs_search_complete', function() {
        initLazyLoading();
    });
});