(function( $ ) {
	$(function() {

		verifyItensVisible();

		function verifyItensVisible() {
			$('.dsw-item').each(function( index ) {
				if ($(this).hasClass('dsw-item-show')){
					$('.dsw-no-data').fadeOut();
					return false;
				}
			});
		}

		$('.dsw-btn-remove').click(function(e) {
			$(this).parent().siblings('input.dsw-field-text').val('');
			$(this).parent().parent('.dsw-item').fadeOut();
			verifyItensVisible();
		});

		$('.dsw-btn-add').click(function(e) {
			var selectEl = $(this).siblings('select[name="dsw_settings_options-social-networks"]');
			$('.dsw-social-item-' + selectEl.val()).fadeIn();
			verifyItensVisible();
		})
	});
})( jQuery );