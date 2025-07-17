/**
 * Admin scripts for Guest Checkout Blocker by Product Type
 *
 * @package GuestCheckoutBlockerByProductType
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Cache DOM elements
		var $enabledCheckbox = $('#gcbpt_enabled');
		var $settingsSections = $('.gcbpt-settings-section');
		var $messageTextarea = $('#gcbpt_message');
		var $previewMessage = $('.gcbpt-preview-message');

		// Toggle settings visibility based on enabled checkbox
		function toggleSettings() {
			if ($enabledCheckbox.is(':checked')) {
				$settingsSections.removeClass('disabled');
			} else {
				$settingsSections.addClass('disabled');
			}
		}

		// Initial state
		toggleSettings();

		// Toggle on change
		$enabledCheckbox.on('change', toggleSettings);

		// Live message preview
		$messageTextarea.on('input', function() {
			var message = $(this).val();
			if (message) {
				$previewMessage.html(message);
			} else {
				$previewMessage.html($messageTextarea.attr('placeholder') || 'Your message will appear here...');
			}
		});

		// Select/Deselect all product types
		$('#gcbpt-select-all').on('click', function(e) {
			e.preventDefault();
			$('input[name="gcbpt_product_types[]"]').prop('checked', true);
		});

		$('#gcbpt-select-none').on('click', function(e) {
			e.preventDefault();
			$('input[name="gcbpt_product_types[]"]').prop('checked', false);
		});

		// Form validation
		$('.gcbpt-settings-form').on('submit', function(e) {
			if ($enabledCheckbox.is(':checked')) {
				var checkedTypes = $('input[name="gcbpt_product_types[]"]:checked').length;
				if (checkedTypes === 0) {
					if (!confirm('No product types selected. The plugin will not block any checkouts. Continue?')) {
						e.preventDefault();
						return false;
					}
				}
			}
		});

		// Add smooth scroll to sections
		$('.gcbpt-card').on('click', function() {
			$('html, body').animate({
				scrollTop: $(this).offset().top - 50
			}, 300);
		});

		// Highlight saved settings
		if (window.location.search.indexOf('settings-updated=true') > -1) {
			$('.gcbpt-save-container').addClass('highlight');
			setTimeout(function() {
				$('.gcbpt-save-container').removeClass('highlight');
			}, 2000);
		}
	});

})(jQuery);