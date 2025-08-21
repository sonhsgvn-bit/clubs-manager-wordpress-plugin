/**
 * Clubs Manager Admin JavaScript
 * 
 * Handles admin interface functionality
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        ClubsAdmin.init();
    });

    // Main ClubsAdmin object
    window.ClubsAdmin = {
        // Initialize all admin functionality
        init: function() {
            this.initGallery();
            this.initOpeningHours();
            this.initMetaBoxes();
            this.initValidation();
            this.initTooltips();
            this.initSortable();
        },

        // Initialize gallery management
        initGallery: function() {
            var self = this;
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
                    title: 'Chọn ảnh cho thư viện',
                    button: {
                        text: 'Thêm vào thư viện'
                    },
                    multiple: true
                });

                // When files are selected
                mediaUploader.on('select', function() {
                    var attachments = mediaUploader.state().get('selection').toJSON();
                    var galleryIds = self.getGalleryIds();

                    $.each(attachments, function(index, attachment) {
                        if (galleryIds.indexOf(attachment.id.toString()) === -1) {
                            self.addImageToGallery(attachment);
                            galleryIds.push(attachment.id.toString());
                        }
                    });

                    self.updateGalleryInput(galleryIds);
                });

                mediaUploader.open();
            });

            // Remove image
            $(document).on('click', '.remove-image', function(e) {
                e.preventDefault();
                var $imageContainer = $(this).closest('.gallery-image');
                var attachmentId = $imageContainer.data('attachment-id');
                var galleryIds = self.getGalleryIds();

                $imageContainer.fadeOut(300, function() {
                    $(this).remove();
                });

                // Remove from array
                var index = galleryIds.indexOf(attachmentId.toString());
                if (index > -1) {
                    galleryIds.splice(index, 1);
                }

                self.updateGalleryInput(galleryIds);
            });

            // Make gallery sortable
            if (typeof $.fn.sortable !== 'undefined') {
                $('#club-gallery-images').sortable({
                    items: '.gallery-image',
                    cursor: 'move',
                    placeholder: 'gallery-placeholder',
                    update: function() {
                        var galleryIds = [];
                        $('#club-gallery-images .gallery-image').each(function() {
                            galleryIds.push($(this).data('attachment-id').toString());
                        });
                        self.updateGalleryInput(galleryIds);
                    }
                });
            }
        },

        // Add image to gallery display
        addImageToGallery: function(attachment) {
            var thumbnail = attachment.sizes && attachment.sizes.thumbnail ? 
                           attachment.sizes.thumbnail.url : attachment.url;
            
            var imageHtml = '<div class="gallery-image" data-attachment-id="' + attachment.id + '">' +
                           '<img src="' + thumbnail + '" alt="' + attachment.alt + '">' +
                           '<a href="#" class="remove-image" title="Xóa ảnh">&times;</a>' +
                           '</div>';
            
            $('#club-gallery-images').append(imageHtml);
        },

        // Get current gallery IDs
        getGalleryIds: function() {
            var galleryValue = $('#club_gallery').val();
            return galleryValue ? galleryValue.split(',').filter(function(id) {
                return id.trim() !== '';
            }) : [];
        },

        // Update gallery input field
        updateGalleryInput: function(galleryIds) {
            $('#club_gallery').val(galleryIds.join(','));
        },

        // Initialize opening hours functionality
        initOpeningHours: function() {
            var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            $.each(days, function(index, day) {
                var $closedCheckbox = $('input[name="club_closed_' + day + '"]');
                var $hoursInput = $('input[name="club_hours_' + day + '"]');

                // Toggle hours input based on closed checkbox
                $closedCheckbox.on('change', function() {
                    if ($(this).is(':checked')) {
                        $hoursInput.prop('disabled', true).addClass('disabled');
                    } else {
                        $hoursInput.prop('disabled', false).removeClass('disabled');
                    }
                });

                // Initialize state
                $closedCheckbox.trigger('change');
            });

            // Copy hours functionality
            this.addCopyHoursButton();
        },

        // Add copy hours functionality
        addCopyHoursButton: function() {
            var $hoursTable = $('.form-table').has('input[name^="club_hours_"]');
            
            if ($hoursTable.length) {
                var $copyButton = $('<button type="button" class="button copy-hours-btn">Sao chép giờ từ ngày khác</button>');
                $hoursTable.before($copyButton);

                $copyButton.on('click', function() {
                    ClubsAdmin.showCopyHoursModal();
                });
            }
        },

        // Show copy hours modal
        showCopyHoursModal: function() {
            var days = {
                'monday': 'Thứ 2',
                'tuesday': 'Thứ 3', 
                'wednesday': 'Thứ 4',
                'thursday': 'Thứ 5',
                'friday': 'Thứ 6',
                'saturday': 'Thứ 7',
                'sunday': 'Chủ nhật'
            };

            var modalHtml = '<div id="copy-hours-modal" class="clubs-modal">' +
                           '<div class="clubs-modal-content">' +
                           '<h3>Sao chép giờ mở cửa</h3>' +
                           '<p>Chọn ngày nguồn và ngày đích:</p>' +
                           '<div class="copy-hours-controls">' +
                           '<label>Từ ngày: <select id="source-day">';

            $.each(days, function(key, label) {
                modalHtml += '<option value="' + key + '">' + label + '</option>';
            });

            modalHtml += '</select></label>' +
                        '<label>Đến ngày: <select id="target-days" multiple>';

            $.each(days, function(key, label) {
                modalHtml += '<option value="' + key + '">' + label + '</option>';
            });

            modalHtml += '</select></label>' +
                        '</div>' +
                        '<div class="modal-buttons">' +
                        '<button type="button" class="button button-primary" id="copy-hours-confirm">Sao chép</button>' +
                        '<button type="button" class="button" id="copy-hours-cancel">Hủy</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

            $('body').append(modalHtml);

            // Bind events
            $('#copy-hours-confirm').on('click', function() {
                ClubsAdmin.copyHours();
            });

            $('#copy-hours-cancel, .clubs-modal').on('click', function(e) {
                if (e.target === this) {
                    $('#copy-hours-modal').remove();
                }
            });
        },

        // Copy hours between days
        copyHours: function() {
            var sourceDay = $('#source-day').val();
            var targetDays = $('#target-days').val();

            if (!targetDays || targetDays.length === 0) {
                alert('Vui lòng chọn ít nhất một ngày đích.');
                return;
            }

            var sourceHours = $('input[name="club_hours_' + sourceDay + '"]').val();
            var sourceClosed = $('input[name="club_closed_' + sourceDay + '"]').is(':checked');

            $.each(targetDays, function(index, day) {
                $('input[name="club_hours_' + day + '"]').val(sourceHours);
                $('input[name="club_closed_' + day + '"]').prop('checked', sourceClosed).trigger('change');
            });

            $('#copy-hours-modal').remove();
            
            // Show success message
            this.showNotice('Đã sao chép giờ mở cửa thành công!', 'success');
        },

        // Initialize meta boxes
        initMetaBoxes: function() {
            // Add responsive classes to meta boxes
            $('.clubs-manager-metabox .form-table').addClass('responsive-table');

            // Initialize conditional fields
            this.initConditionalFields();

            // Initialize price formatting
            this.initPriceFormatting();

            // Initialize phone number formatting
            this.initPhoneFormatting();
        },

        // Initialize conditional fields
        initConditionalFields: function() {
            // Example: Show/hide fields based on other field values
            // This can be extended based on specific requirements
        },

        // Initialize price formatting
        initPriceFormatting: function() {
            $('input[name="club_price"]').on('input', function() {
                var value = $(this).val().replace(/[^\d]/g, '');
                if (value) {
                    var formattedValue = parseInt(value).toLocaleString('vi-VN');
                    $(this).next('.price-preview').remove();
                    $(this).after('<span class="price-preview" style="margin-left: 10px; color: #666;">' + 
                                 formattedValue + ' VNĐ</span>');
                } else {
                    $(this).next('.price-preview').remove();
                }
            });
        },

        // Initialize phone number formatting
        initPhoneFormatting: function() {
            $('input[name="club_phone"]').on('input', function() {
                var value = $(this).val().replace(/[^\d]/g, '');
                
                // Basic Vietnamese phone number formatting
                if (value.length >= 10) {
                    var formatted;
                    if (value.startsWith('84')) {
                        // International format
                        formatted = '+84 ' + value.substring(2, 5) + ' ' + 
                                   value.substring(5, 8) + ' ' + value.substring(8);
                    } else if (value.startsWith('0')) {
                        // National format
                        formatted = value.substring(0, 4) + ' ' + 
                                   value.substring(4, 7) + ' ' + value.substring(7);
                    }
                    
                    if (formatted) {
                        $(this).next('.phone-preview').remove();
                        $(this).after('<span class="phone-preview" style="margin-left: 10px; color: #666;">' + 
                                     formatted + '</span>');
                    }
                } else {
                    $(this).next('.phone-preview').remove();
                }
            });
        },

        // Initialize form validation
        initValidation: function() {
            var self = this;

            // Validate on form submit
            $('form#post').on('submit', function(e) {
                var isValid = self.validateForm();
                if (!isValid) {
                    e.preventDefault();
                    self.showNotice('Vui lòng kiểm tra lại thông tin đã nhập.', 'error');
                    $('html, body').animate({
                        scrollTop: $('.clubs-field-error').first().offset().top - 100
                    }, 500);
                }
            });

            // Real-time validation
            $('input[name^="club_"], textarea[name^="club_"], select[name^="club_"]').on('blur', function() {
                self.validateField($(this));
            });
        },

        // Validate entire form
        validateForm: function() {
            var isValid = true;
            var self = this;

            $('input[name^="club_"], textarea[name^="club_"], select[name^="club_"]').each(function() {
                if (!self.validateField($(this))) {
                    isValid = false;
                }
            });

            return isValid;
        },

        // Validate individual field
        validateField: function($field) {
            var fieldName = $field.attr('name');
            var value = $field.val().trim();
            var isValid = true;
            var errorMessage = '';

            // Remove existing error styling
            $field.removeClass('clubs-field-error clubs-field-success');
            $field.next('.clubs-error-message').remove();

            // Validation rules
            switch (fieldName) {
                case 'club_price':
                    if (value && (isNaN(value) || parseInt(value) < 0)) {
                        isValid = false;
                        errorMessage = 'Giá phải là số không âm.';
                    }
                    break;

                case 'club_tables':
                    if (value && (isNaN(value) || parseInt(value) < 1)) {
                        isValid = false;
                        errorMessage = 'Số bàn phải là số nguyên dương.';
                    }
                    break;

                case 'club_email':
                    if (value && !this.isValidEmail(value)) {
                        isValid = false;
                        errorMessage = 'Email không hợp lệ.';
                    }
                    break;

                case 'club_phone':
                    if (value && !this.isValidPhone(value)) {
                        isValid = false;
                        errorMessage = 'Số điện thoại không hợp lệ.';
                    }
                    break;

                case 'club_website':
                case 'club_facebook':
                    if (value && !this.isValidUrl(value)) {
                        isValid = false;
                        errorMessage = 'URL không hợp lệ.';
                    }
                    break;
            }

            // Apply styling and error message
            if (!isValid) {
                $field.addClass('clubs-field-error');
                $field.after('<span class="clubs-error-message">' + errorMessage + '</span>');
            } else if (value) {
                $field.addClass('clubs-field-success');
            }

            return isValid;
        },

        // Email validation
        isValidEmail: function(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        // Phone validation (Vietnamese format)
        isValidPhone: function(phone) {
            var phoneRegex = /^(\+84|84|0)(3|5|7|8|9)[0-9]{8}$/;
            return phoneRegex.test(phone.replace(/[\s-]/g, ''));
        },

        // URL validation
        isValidUrl: function(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        },

        // Initialize tooltips
        initTooltips: function() {
            // Add help tooltips to form fields
            $('input[name^="club_"], textarea[name^="club_"], select[name^="club_"]').each(function() {
                var $field = $(this);
                var fieldName = $field.attr('name');
                var tooltipText = ClubsAdmin.getTooltipText(fieldName);

                if (tooltipText) {
                    var $tooltip = $('<span class="clubs-help-tooltip">' +
                                    '<span class="dashicons dashicons-editor-help"></span>' +
                                    '<span class="tooltip-text">' + tooltipText + '</span>' +
                                    '</span>');
                    $field.after($tooltip);
                }
            });
        },

        // Get tooltip text for fields
        getTooltipText: function(fieldName) {
            var tooltips = {
                'club_address': 'Nhập địa chỉ đầy đủ để hiển thị chính xác trên bản đồ.',
                'club_price': 'Giá thuê bàn bi-a theo giờ tính bằng VNĐ.',
                'club_tables': 'Tổng số bàn bi-a có sẵn tại câu lạc bộ.',
                'club_phone': 'Số điện thoại liên hệ (định dạng: 0xxx xxx xxx).',
                'club_email': 'Địa chỉ email chính thức của câu lạc bộ.',
                'club_website': 'Địa chỉ website chính thức (bắt đầu bằng http:// hoặc https://).',
                'club_facebook': 'Đường dẫn đến trang Facebook của câu lạc bộ.'
            };

            return tooltips[fieldName] || '';
        },

        // Initialize sortable functionality
        initSortable: function() {
            // Make gallery images sortable (already implemented in initGallery)
            // This can be extended for other sortable elements
        },

        // Show admin notice
        showNotice: function(message, type) {
            type = type || 'info';
            var noticeClass = 'notice-' + type;
            
            var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible clubs-manager-notice">' +
                           '<p>' + message + '</p>' +
                           '<button type="button" class="notice-dismiss">' +
                           '<span class="screen-reader-text">Dismiss this notice.</span>' +
                           '</button>' +
                           '</div>');

            $('.wrap h1').after($notice);

            // Auto-dismiss after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(function() {
                    $notice.fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // Handle dismiss button
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            });
        },

        // Utility function to format currency
        formatCurrency: function(amount) {
            return parseInt(amount).toLocaleString('vi-VN') + ' VNĐ';
        },

        // Utility function to format phone number
        formatPhone: function(phone) {
            var cleaned = phone.replace(/[^\d]/g, '');
            if (cleaned.startsWith('84')) {
                return '+84 ' + cleaned.substring(2, 5) + ' ' + 
                       cleaned.substring(5, 8) + ' ' + cleaned.substring(8);
            } else if (cleaned.startsWith('0')) {
                return cleaned.substring(0, 4) + ' ' + 
                       cleaned.substring(4, 7) + ' ' + cleaned.substring(7);
            }
            return phone;
        }
    };

    // Admin-specific jQuery extensions
    $.fn.clubsLoadingState = function(loading) {
        return this.each(function() {
            var $this = $(this);
            if (loading) {
                $this.prop('disabled', true).addClass('clubs-admin-loading');
            } else {
                $this.prop('disabled', false).removeClass('clubs-admin-loading');
            }
        });
    };

})(jQuery);

// WordPress media uploader compatibility
if (typeof wp !== 'undefined' && wp.media) {
    wp.media.clubsManager = {
        // Custom media frame for gallery management
        frame: function() {
            if (this._frame) {
                return this._frame;
            }

            this._frame = wp.media({
                title: 'Quản lý thư viện ảnh',
                button: {
                    text: 'Cập nhật thư viện'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            return this._frame;
        }
    };
}