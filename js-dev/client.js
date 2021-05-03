(function ($) {

	$(function () {

		var $cloned_passenger = null;

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
			$('.datepicker:not(.hasDatepicker)').datepicker({
				numberOfMonths: 2,
				showButtonPanel: true,
				dateFormat: 'dd.mm.yy'
			});
			$('.datepicker').datepicker('option',
				$.datepicker.regional['he']
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

		init();
		init_tooltip();

	});

})(jQuery);
