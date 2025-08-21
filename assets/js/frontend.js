/**
 * Clubs Manager Frontend JavaScript
 */

jQuery(document).ready(function($) {
    
    // Initialize Google Maps if available
    if (typeof google !== 'undefined' && google.maps) {
        initializeMaps();
    }
    
    /**
     * Initialize Google Maps
     */
    function initializeMaps() {
        // Single club location map
        var singleMap = $('#club-location-map');
        if (singleMap.length) {
            var lat = parseFloat(singleMap.data('lat'));
            var lng = parseFloat(singleMap.data('lng'));
            var title = singleMap.data('title');
            
            if (lat && lng) {
                var map = new google.maps.Map(singleMap[0], {
                    center: { lat: lat, lng: lng },
                    zoom: 15
                });
                
                var marker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    title: title
                });
                
                var infoWindow = new google.maps.InfoWindow({
                    content: '<div style="font-weight: bold;">' + title + '</div>'
                });
                
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });
            }
        }
        
        // Multiple clubs map (shortcode)
        var clubsMap = $('#clubs-map');
        if (clubsMap.length) {
            var zoom = parseInt(clubsMap.data('zoom')) || 10;
            var centerLat = parseFloat(clubsMap.data('center-lat')) || 10.8231;
            var centerLng = parseFloat(clubsMap.data('center-lng')) || 106.6297;
            var clubs = clubsMap.data('clubs') || [];
            
            var map = new google.maps.Map(clubsMap[0], {
                center: { lat: centerLat, lng: centerLng },
                zoom: zoom
            });
            
            var bounds = new google.maps.LatLngBounds();
            
            clubs.forEach(function(club) {
                var marker = new google.maps.Marker({
                    position: { lat: club.lat, lng: club.lng },
                    map: map,
                    title: club.title
                });
                
                var infoContent = '<div class="map-info-window">';
                if (club.thumbnail) {
                    infoContent += '<img src="' + club.thumbnail + '" style="width: 80px; height: 60px; object-fit: cover; margin-bottom: 5px;">';
                }
                infoContent += '<div style="font-weight: bold; margin-bottom: 3px;">' + club.title + '</div>';
                if (club.address) {
                    infoContent += '<div style="font-size: 12px; color: #666; margin-bottom: 3px;">' + club.address + '</div>';
                }
                if (club.price) {
                    infoContent += '<div style="font-size: 12px; color: #007cba; margin-bottom: 5px;">' + parseInt(club.price).toLocaleString() + ' VNĐ/giờ</div>';
                }
                infoContent += '<a href="' + club.url + '" style="font-size: 12px;">Xem chi tiết</a>';
                infoContent += '</div>';
                
                var infoWindow = new google.maps.InfoWindow({
                    content: infoContent
                });
                
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });
                
                bounds.extend(marker.getPosition());
            });
            
            if (clubs.length > 1) {
                map.fitBounds(bounds);
            }
        }
    }
    
    /**
     * Smooth scrolling for anchor links
     */
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    /**
     * Image lazy loading
     */
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
        
        $('.club-card img[data-src], .gallery-item img[data-src]').each(function() {
            imageObserver.observe(this);
        });
    }
    
    /**
     * Back to top button
     */
    var backToTop = $('<button id="back-to-top" title="Lên đầu trang">↑</button>');
    backToTop.css({
        'position': 'fixed',
        'bottom': '20px',
        'right': '20px',
        'width': '50px',
        'height': '50px',
        'background': '#007cba',
        'color': 'white',
        'border': 'none',
        'border-radius': '50%',
        'cursor': 'pointer',
        'font-size': '18px',
        'display': 'none',
        'z-index': 1000
    });
    
    $('body').append(backToTop);
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            backToTop.fadeIn();
        } else {
            backToTop.fadeOut();
        }
    });
    
    backToTop.click(function() {
        $('html, body').animate({ scrollTop: 0 }, 600);
    });
});