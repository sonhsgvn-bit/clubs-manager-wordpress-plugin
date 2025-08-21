/**
 * Clubs Manager AJAX Search JavaScript
 */

jQuery(document).ready(function($) {
    
    var currentPage = 1;
    var isLoading = false;
    
    /**
     * Handle search form submission
     */
    $('#clubs-search-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        performSearch();
    });
    
    /**
     * Handle reset button
     */
    $('#reset-search').on('click', function() {
        $('#clubs-search-form')[0].reset();
        currentPage = 1;
        performSearch();
    });
    
    /**
     * Handle pagination clicks
     */
    $(document).on('click', '.clubs-pagination .page-numbers', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if (href && href.indexOf('paged=') !== -1) {
            var page = href.split('paged=')[1];
            currentPage = parseInt(page) || 1;
            performSearch();
        }
    });
    
    /**
     * Perform AJAX search
     */
    function performSearch() {
        if (isLoading) return;
        
        isLoading = true;
        showLoading();
        
        var formData = {
            action: 'clubs_search',
            nonce: clubs_ajax.nonce,
            search_name: $('#search_name').val(),
            search_area: $('#search_area').val(),
            search_price_min: $('#search_price_min').val(),
            search_price_max: $('#search_price_max').val(),
            search_tables: $('#search_tables').val(),
            search_parking: $('#search_parking').val(),
            paged: currentPage
        };
        
        // Add area_id if we're on a taxonomy page
        var areaId = $('input[name="area_id"]').val();
        if (areaId) {
            formData.area_id = areaId;
        }
        
        $.ajax({
            url: clubs_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#clubs-results').html(response.data.results);
                    
                    // Update pagination
                    if (response.data.pagination) {
                        $('.clubs-pagination').html(response.data.pagination).show();
                    } else {
                        $('.clubs-pagination').hide();
                    }
                    
                    // Scroll to results
                    $('html, body').animate({
                        scrollTop: $('#clubs-results').offset().top - 100
                    }, 500);
                    
                } else {
                    showError('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
                }
            },
            error: function() {
                showError('Không thể kết nối đến máy chủ. Vui lòng thử lại.');
            },
            complete: function() {
                isLoading = false;
                hideLoading();
            }
        });
    }
    
    /**
     * Show loading indicator
     */
    function showLoading() {
        $('#loading-indicator').show();
        $('#clubs-results').css('opacity', '0.5');
    }
    
    /**
     * Hide loading indicator
     */
    function hideLoading() {
        $('#loading-indicator').hide();
        $('#clubs-results').css('opacity', '1');
    }
    
    /**
     * Show error message
     */
    function showError(message) {
        $('#clubs-results').html(
            '<div class="search-error" style="text-align: center; padding: 20px; color: #dc3545; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px 0;">' +
            '<p>' + message + '</p>' +
            '</div>'
        );
    }
    
    /**
     * Auto-search on input change (with debounce)
     */
    var searchTimeout;
    $('#search_name').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            performSearch();
        }, 500);
    });
    
    /**
     * Auto-search on select change
     */
    $('#search_area, #search_parking').on('change', function() {
        currentPage = 1;
        performSearch();
    });
    
    /**
     * Auto-search on number input change (with debounce)
     */
    $('#search_price_min, #search_price_max, #search_tables').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            performSearch();
        }, 1000);
    });
    
    /**
     * Initialize search filters from URL parameters
     */
    function initializeFromURL() {
        var urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('search_name')) {
            $('#search_name').val(urlParams.get('search_name'));
        }
        if (urlParams.get('search_area')) {
            $('#search_area').val(urlParams.get('search_area'));
        }
        if (urlParams.get('search_price_min')) {
            $('#search_price_min').val(urlParams.get('search_price_min'));
        }
        if (urlParams.get('search_price_max')) {
            $('#search_price_max').val(urlParams.get('search_price_max'));
        }
        if (urlParams.get('search_tables')) {
            $('#search_tables').val(urlParams.get('search_tables'));
        }
        if (urlParams.get('search_parking')) {
            $('#search_parking').val(urlParams.get('search_parking'));
        }
        
        // Perform search if any parameters are set
        if (urlParams.toString()) {
            currentPage = parseInt(urlParams.get('paged')) || 1;
            performSearch();
        }
    }
    
    // Initialize on page load
    initializeFromURL();
    
    /**
     * Update URL without page reload
     */
    function updateURL() {
        var params = new URLSearchParams();
        
        var searchName = $('#search_name').val();
        var searchArea = $('#search_area').val();
        var searchPriceMin = $('#search_price_min').val();
        var searchPriceMax = $('#search_price_max').val();
        var searchTables = $('#search_tables').val();
        var searchParking = $('#search_parking').val();
        
        if (searchName) params.set('search_name', searchName);
        if (searchArea) params.set('search_area', searchArea);
        if (searchPriceMin) params.set('search_price_min', searchPriceMin);
        if (searchPriceMax) params.set('search_price_max', searchPriceMax);
        if (searchTables) params.set('search_tables', searchTables);
        if (searchParking) params.set('search_parking', searchParking);
        if (currentPage > 1) params.set('paged', currentPage);
        
        var newURL = window.location.pathname;
        if (params.toString()) {
            newURL += '?' + params.toString();
        }
        
        window.history.replaceState({}, '', newURL);
    }
    
    // Update URL when performing search
    var originalPerformSearch = performSearch;
    performSearch = function() {
        updateURL();
        originalPerformSearch();
    };
});