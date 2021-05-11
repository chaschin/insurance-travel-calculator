(function ($) {

	$(function () {

		var $cloned_passenger = null,
			$scroll_container = $('.it-calculator__content-info'), //get the offset top of the element
			$scroll_block = $('.it-calculator__content-info-inner');
			
		var init = function() {
			$('form[name="it-calculator"]')[0].reset();

			$cloned_passenger = $('.it-calculator__passenger').last().clone(false, false);

			enable_datepicker();

			change_passendgers_number();

			$(document).on('click', '.it-calculator__passenger-add', function(e) {
				e.preventDefault();
				var $cloned = $cloned_passenger.clone(false, false);
				$('.it-calculator__passengers-list').append($cloned);
				change_passendgers_number();
				enable_datepicker();
			});

			$(document).on('click', '.it-calculator__passenger-delete', function(e) {
				e.preventDefault();
				$(this).closest('.it-calculator__passenger').remove();
				change_passendgers_number();
			});
			
			$(document).on('click', '.it-calculator__options-list-item', function(e) {
				e.preventDefault();
				var $checkbox = $(this).find('input[type="checkbox"]'),
					$checkbox_v = $(this).find('.it-calculator__options-checkbox');

				$checkbox_v.toggleClass('checked');
				if ($checkbox_v.hasClass('checked')) {
					$checkbox.prop('checked', true);
				} else {
					$checkbox.prop('checked', false);
				}
				if ($(this).hasClass('checked')) {
				}
				calc();
			});

			$(document).on('change', 'form[name="it-calculator"] input', function() {
				calc();
			});

			$(window).scroll(function (event) {
				scroll_column();
			});

			$(window).on('resize', function(event) {
				scroll_column();
			});
		};

		var init_tooltip = function() {
			$('.it-calculator__tooltip').tooltip();
		};

		var change_passendgers_number = function() {
			$('.it-calculator__passenger').each(function(i, e) {
				$(e).find('.it-calculator__passenger-number').html(i + 1);
			});
			calc();
		};

		var enable_datepicker = function() {
			$('.it-calculator__passenger-input:not(.hasDatepicker)').datepicker({
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true
			});
			$('.it-calculator__travel-date-input:not(.hasDatepicker)').datepicker({
				numberOfMonths: 2,
				showButtonPanel: true,
			});
			$('.datepicker').datepicker(
				'option',
				$.datepicker.regional['he']
			);
			$('.datepicker').datepicker(
				'option',
				'dateFormat',
				'dd.mm.yy'
			);
		};

		var calc = function() {
			var data = $('form[name="it-calculator"]').serialize();
			data = data + '&action=it_calculate';
			$.post(script_data.admin_ajax_url, data, function (response) {
				var msg = jQuery.parseJSON(response);
				$('.it-calculator__results-inner').html(msg.results);
				$('.it-calculator__companies-inner').html(msg.companies);
			});
		};

		var scroll_column = function() {
			var window_width = $(window).width(),
				scroll = $(window).scrollTop(),
				scroll_container_height = $scroll_container.height();
				scroll_block_height = $scroll_block.height(),
				e_top = $scroll_container.offset().top,
				diff = scroll_container_height - scroll_block_height;
				e_offset = scroll - e_top;
			if ($('#page-header').length > 0) {
				e_offset = e_offset + $('#page-header').height();
			}
			if (window_width > 1199) {
				if (e_offset > 0 && diff > e_offset) {
					$scroll_block.css('margin-top', e_offset + 'px');
				} else if (e_offset <= 0 && diff > e_offset) {
					$scroll_block.css('margin-top', '0');
				}
			} else {
				$scroll_block.css('margin-top', '0');
			}
		};

		if ($('form[name="it-calculator"]').length > 0) {
			init();
			init_tooltip();
			scroll_column();
		}
	});

})(jQuery);
