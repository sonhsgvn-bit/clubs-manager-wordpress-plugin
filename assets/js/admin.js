/**
 * Clubs Manager Admin JavaScript
 */

jQuery(document).ready(function($) {
    
    /**
     * Media gallery functionality
     */
    var galleryUploader;
    
    $('.add-gallery-images').on('click', function(e) {
        e.preventDefault();
        
        if (galleryUploader) {
            galleryUploader.open();
            return;
        }
        
        galleryUploader = wp.media({
            title: 'Chọn ảnh cho thư viện',
            button: {
                text: 'Thêm ảnh'
            },
            multiple: true
        });
        
        galleryUploader.on('select', function() {
            var attachments = galleryUploader.state().get('selection').toJSON();
            var currentGallery = $('#club_gallery').val();
            var galleryIds = currentGallery ? currentGallery.split(',') : [];
            
            attachments.forEach(function(attachment) {
                if (galleryIds.indexOf(attachment.id.toString()) === -1) {
                    galleryIds.push(attachment.id);
                    addGalleryThumbnail(attachment.id, attachment.sizes.thumbnail.url);
                }
            });
            
            $('#club_gallery').val(galleryIds.join(','));
        });
        
        galleryUploader.open();
    });
    
    /**
     * Remove gallery image
     */
    $(document).on('click', '.remove-gallery-image', function() {
        var thumb = $(this).closest('.gallery-thumb');
        var imageId = thumb.data('id');
        var currentGallery = $('#club_gallery').val();
        var galleryIds = currentGallery ? currentGallery.split(',') : [];
        
        // Remove from array
        var index = galleryIds.indexOf(imageId.toString());
        if (index > -1) {
            galleryIds.splice(index, 1);
        }
        
        // Update field
        $('#club_gallery').val(galleryIds.join(','));
        
        // Remove thumbnail
        thumb.remove();
    });
    
    /**
     * Add gallery thumbnail
     */
    function addGalleryThumbnail(imageId, imageUrl) {
        var thumbnail = '<div class="gallery-thumb" data-id="' + imageId + '">';
        thumbnail += '<img src="' + imageUrl + '" alt="" />';
        thumbnail += '<button type="button" class="remove-gallery-image">&times;</button>';
        thumbnail += '</div>';
        
        $('.club-gallery-thumbnails').append(thumbnail);
    }
    
    /**
     * Google Maps for location selection
     */
    var map, marker;
    var mapElement = document.getElementById('club-map');
    
    if (mapElement && typeof google !== 'undefined' && google.maps) {
        initializeAdminMap();
    }
    
    function initializeAdminMap() {
        var lat = parseFloat($('#club_lat').val()) || 10.8231; // Ho Chi Minh City default
        var lng = parseFloat($('#club_lng').val()) || 106.6297;
        
        map = new google.maps.Map(mapElement, {
            center: { lat: lat, lng: lng },
            zoom: 13
        });
        
        marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            draggable: true
        });
        
        // Update coordinates when marker is dragged
        marker.addListener('dragend', function() {
            var position = marker.getPosition();
            $('#club_lat').val(position.lat());
            $('#club_lng').val(position.lng());
        });
        
        // Update marker when coordinates are manually entered
        $('#club_lat, #club_lng').on('input', function() {
            var newLat = parseFloat($('#club_lat').val());
            var newLng = parseFloat($('#club_lng').val());
            
            if (newLat && newLng) {
                var newPosition = { lat: newLat, lng: newLng };
                marker.setPosition(newPosition);
                map.setCenter(newPosition);
            }
        });
        
        // Click on map to set marker position
        map.addListener('click', function(e) {
            var position = e.latLng;
            marker.setPosition(position);
            $('#club_lat').val(position.lat());
            $('#club_lng').val(position.lng());
        });
    }
    
    /**
     * Address geocoding
     */
    $('#club_address').on('blur', function() {
        var address = $(this).val();
        if (address && typeof google !== 'undefined' && google.maps && map) {
            var geocoder = new google.maps.Geocoder();
            
            geocoder.geocode({ address: address + ', Vietnam' }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    var location = results[0].geometry.location;
                    marker.setPosition(location);
                    map.setCenter(location);
                    $('#club_lat').val(location.lat());
                    $('#club_lng').val(location.lng());
                }
            });
        }
    });
    
    /**
     * Price formatting
     */
    $('#club_price').on('input', function() {
        var value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(parseInt(value));
        }
    });
    
    /**
     * Form validation
     */
    $('#post').on('submit', function() {
        var hasErrors = false;
        var errorMessages = [];
        
        // Validate required fields
        if (!$('#title').val().trim()) {
            errorMessages.push('Tên câu lạc bộ là bắt buộc.');
            hasErrors = true;
        }
        
        if (!$('#club_address').val().trim()) {
            errorMessages.push('Địa chỉ là bắt buộc.');
            hasErrors = true;
        }
        
        // Validate price
        var price = $('#club_price').val();
        if (price && (isNaN(price) || parseInt(price) < 0)) {
            errorMessages.push('Giá phải là số dương.');
            hasErrors = true;
        }
        
        // Validate tables
        var tables = $('#club_tables').val();
        if (tables && (isNaN(tables) || parseInt(tables) < 1)) {
            errorMessages.push('Số bàn bi-a phải lớn hơn 0.');
            hasErrors = true;
        }
        
        // Validate coordinates
        var lat = $('#club_lat').val();
        var lng = $('#club_lng').val();
        if ((lat && isNaN(lat)) || (lng && isNaN(lng))) {
            errorMessages.push('Tọa độ vị trí không hợp lệ.');
            hasErrors = true;
        }
        
        if (hasErrors) {
            alert('Có lỗi trong dữ liệu nhập:\n\n' + errorMessages.join('\n'));
            return false;
        }
        
        return true;
    });
    
    /**
     * Copy opening hours to all days
     */
    $('<button type="button" id="copy-hours-all" style="margin-left: 10px;">Sao chép cho tất cả</button>')
        .insertAfter('input[name="club_hours[monday][close]"]');
    
    $('#copy-hours-all').on('click', function() {
        var mondayOpen = $('input[name="club_hours[monday][open]"]').val();
        var mondayClose = $('input[name="club_hours[monday][close]"]').val();
        
        if (mondayOpen && mondayClose) {
            $('input[name^="club_hours"][name$="[open]"]').val(mondayOpen);
            $('input[name^="club_hours"][name$="[close]"]').val(mondayClose);
        }
    });
});