/* Admin JavaScript for Clubs Manager Plugin */

jQuery(document).ready(function($) {
    
    // Gallery Management
    initGalleryManagement();
    
    // Meta Box Tabs
    initMetaBoxTabs();
    
    // Auto-save functionality
    initAutoSave();
    
    // Gallery Management Functions
    function initGalleryManagement() {
        var mediaUploader;
        
        // Add images button
        $('#add-gallery-images').on('click', function(e) {
            e.preventDefault();
            
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Create the media uploader
            mediaUploader = wp.media({
                title: clubsManagerAdmin.strings.select_images,
                button: {
                    text: clubsManagerAdmin.strings.use_images
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });
            
            // When images are selected
            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toJSON();
                var $container = $('#club-gallery-images');
                
                attachments.forEach(function(attachment) {
                    if (attachment.type === 'image') {
                        var thumbnailUrl = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        var imageHtml = '<div class="gallery-image" data-id="' + attachment.id + '">' +
                                       '<img src="' + thumbnailUrl + '" alt="" />' +
                                       '<button type="button" class="remove-gallery-image">&times;</button>' +
                                       '<input type="hidden" name="club_gallery[]" value="' + attachment.id + '" />' +
                                       '</div>';
                        $container.append(imageHtml);
                    }
                });
                
                updateGalleryEmptyState();
            });
            
            mediaUploader.open();
        });
        
        // Remove image
        $(document).on('click', '.remove-gallery-image', function(e) {
            e.preventDefault();
            $(this).closest('.gallery-image').remove();
            updateGalleryEmptyState();
        });
        
        // Make gallery sortable
        if ($.fn.sortable) {
            $('#club-gallery-images').sortable({
                items: '.gallery-image',
                placeholder: 'gallery-placeholder',
                tolerance: 'pointer',
                cursor: 'move'
            });
        }
        
        function updateGalleryEmptyState() {
            var $container = $('#club-gallery-images');
            if ($container.children().length === 0) {
                $container.addClass('empty');
            } else {
                $container.removeClass('empty');
            }
        }
        
        // Initialize empty state
        updateGalleryEmptyState();
    }
    
    // Meta Box Tabs
    function initMetaBoxTabs() {
        $('.clubs-meta-tabs a').on('click', function(e) {
            e.preventDefault();
            
            var targetTab = $(this).attr('href');
            var $tabsContainer = $(this).closest('.clubs-meta-tabs');
            var $contentContainer = $tabsContainer.next('.clubs-meta-content');
            
            // Update active tab
            $tabsContainer.find('a').removeClass('active');
            $(this).addClass('active');
            
            // Show target content
            $contentContainer.find('.clubs-meta-tab-content').removeClass('active');
            $contentContainer.find(targetTab).addClass('active');
        });
    }
    
    // Auto-save functionality
    function initAutoSave() {
        var autoSaveTimeout;
        
        // Auto-save on form field changes
        $('#post').on('change input', 'input[name^="club_"], select[name^="club_"], textarea[name^="club_"]', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                triggerAutoSave();
            }, 3000); // 3 seconds delay
        });
        
        function triggerAutoSave() {
            if (typeof wp !== 'undefined' && wp.autosave) {
                wp.autosave.server.triggerSave();
            }
        }
    }
    
    // Enhanced form validation
    $('#post').on('submit', function(e) {
        var errors = [];
        
        // Validate required fields
        var $requiredFields = $('input[required], select[required], textarea[required]');
        $requiredFields.each(function() {
            if (!$(this).val().trim()) {
                errors.push('Vui lòng điền đầy đủ thông tin bắt buộc.');
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Validate price
        var price = $('input[name="club_price"]').val();
        if (price && (isNaN(price) || price < 0)) {
            errors.push('Giá phải là số dương.');
            $('input[name="club_price"]').addClass('error');
        }
        
        // Validate tables count
        var tables = $('input[name="club_tables"]').val();
        if (tables && (isNaN(tables) || tables < 1)) {
            errors.push('Số bàn bi-a phải là số nguyên dương.');
            $('input[name="club_tables"]').addClass('error');
        }
        
        // Validate coordinates
        var lat = $('input[name="club_lat"]').val();
        var lng = $('input[name="club_lng"]').val();
        
        if (lat && (isNaN(lat) || lat < -90 || lat > 90)) {
            errors.push('Latitude phải là số từ -90 đến 90.');
            $('input[name="club_lat"]').addClass('error');
        }
        
        if (lng && (isNaN(lng) || lng < -180 || lng > 180)) {
            errors.push('Longitude phải là số từ -180 đến 180.');
            $('input[name="club_lng"]').addClass('error');
        }
        
        // Validate email
        var email = $('input[name="club_email"]').val();
        if (email && !isValidEmail(email)) {
            errors.push('Email không hợp lệ.');
            $('input[name="club_email"]').addClass('error');
        }
        
        // Validate URL
        var website = $('input[name="club_website"]').val();
        if (website && !isValidUrl(website)) {
            errors.push('Website URL không hợp lệ.');
            $('input[name="club_website"]').addClass('error');
        }
        
        // Show errors if any
        if (errors.length > 0) {
            e.preventDefault();
            alert('Vui lòng sửa các lỗi sau:\n\n' + errors.join('\n'));
            return false;
        }
    });
    
    // Utility functions
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    // Phone number formatting
    $('input[name="club_phone"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        var formatted = formatPhoneNumber(value);
        $(this).val(formatted);
    });
    
    function formatPhoneNumber(phone) {
        if (phone.length <= 10) {
            return phone.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
        } else {
            return phone.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
        }
    }
    
    // Price formatting
    $('input[name="club_price"]').on('blur', function() {
        var value = parseInt($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toLocaleString('vi-VN'));
        }
    }).on('focus', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(value);
    });
    
    // Hours validation
    $('.clubs-hours-table input[type="time"]').on('change', function() {
        var $row = $(this).closest('tr');
        var openTime = $row.find('input[name$="[open]"]').val();
        var closeTime = $row.find('input[name$="[close]"]').val();
        
        if (openTime && closeTime && openTime >= closeTime) {
            alert('Giờ đóng cửa phải sau giờ mở cửa.');
            $(this).focus();
        }
    });
    
    // Closed checkbox handling
    $('.clubs-hours-table input[type="checkbox"]').on('change', function() {
        var $row = $(this).closest('tr');
        var $timeInputs = $row.find('input[type="time"]');
        
        if ($(this).is(':checked')) {
            $timeInputs.prop('disabled', true).addClass('disabled');
        } else {
            $timeInputs.prop('disabled', false).removeClass('disabled');
        }
    });
    
    // Initialize disabled states
    $('.clubs-hours-table input[type="checkbox"]:checked').each(function() {
        var $row = $(this).closest('tr');
        var $timeInputs = $row.find('input[type="time"]');
        $timeInputs.prop('disabled', true).addClass('disabled');
    });
    
    // Location helper
    if (navigator.geolocation) {
        var $getLocationBtn = $('<button type="button" class="button" id="get-current-location">Lấy vị trí hiện tại</button>');
        $('input[name="club_lng"]').after($getLocationBtn);
        
        $getLocationBtn.on('click', function() {
            $(this).prop('disabled', true).text('Đang lấy vị trí...');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    $('input[name="club_lat"]').val(position.coords.latitude.toFixed(6));
                    $('input[name="club_lng"]').val(position.coords.longitude.toFixed(6));
                    $getLocationBtn.prop('disabled', false).text('Lấy vị trí hiện tại');
                    alert('Đã cập nhật tọa độ thành công!');
                },
                function(error) {
                    $getLocationBtn.prop('disabled', false).text('Lấy vị trí hiện tại');
                    alert('Không thể lấy vị trí hiện tại: ' + error.message);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
    
    // Character counter for text areas
    $('textarea').each(function() {
        var $textarea = $(this);
        var maxLength = $textarea.attr('maxlength');
        
        if (maxLength) {
            var $counter = $('<div class="char-counter"></div>');
            $textarea.after($counter);
            
            function updateCounter() {
                var remaining = maxLength - $textarea.val().length;
                $counter.text(remaining + ' ký tự còn lại');
                
                if (remaining < 20) {
                    $counter.addClass('warning');
                } else {
                    $counter.removeClass('warning');
                }
            }
            
            $textarea.on('input', updateCounter);
            updateCounter();
        }
    });
    
    // Help tooltips
    $('.clubs-help-trigger').on('click', function(e) {
        e.preventDefault();
        var helpText = $(this).data('help');
        if (helpText) {
            alert(helpText);
        }
    });
    
    // Quick edit functionality enhancements
    if ($('.wp-list-table').length) {
        $('.wp-list-table').on('click', '.editinline', function() {
            var postId = $(this).closest('tr').attr('id').replace('post-', '');
            // Custom quick edit logic here if needed
        });
    }
    
    // Bulk actions
    $('#doaction, #doaction2').on('click', function(e) {
        var action = $(this).siblings('select').val();
        var selected = $('.wp-list-table input[type="checkbox"]:checked').length;
        
        if (action !== '-1' && selected === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một câu lạc bộ.');
        }
    });
    
    // Settings page enhancements
    if ($('.clubs-manager-settings').length) {
        // API key validation
        $('input[name="clubs_manager_options[google_maps_api_key]"]').on('blur', function() {
            var apiKey = $(this).val().trim();
            if (apiKey.length > 0 && apiKey.length < 30) {
                $(this).addClass('error');
                alert('Google Maps API Key có vẻ không hợp lệ. Vui lòng kiểm tra lại.');
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Coordinate validation
        $('input[name*="lat"], input[name*="lng"]').on('blur', function() {
            var value = parseFloat($(this).val());
            var isLat = $(this).attr('name').includes('lat');
            
            if (!isNaN(value)) {
                if (isLat && (value < -90 || value > 90)) {
                    $(this).addClass('error');
                    alert('Latitude phải nằm trong khoảng -90 đến 90.');
                } else if (!isLat && (value < -180 || value > 180)) {
                    $(this).addClass('error');
                    alert('Longitude phải nằm trong khoảng -180 đến 180.');
                } else {
                    $(this).removeClass('error');
                }
            }
        });
    }
    
    // Initialize any existing error states
    $('.error').each(function() {
        $(this).addClass('highlight-error');
    });
    
    // Remove error highlighting on focus
    $(document).on('focus', '.error', function() {
        $(this).removeClass('error highlight-error');
    });
});