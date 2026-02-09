jQuery(document).ready(function ($) {
	jQuery(document).ready(function ($) {
		$(document).on(
			'change',
			'[data-name="group_product_attribute"] select',
			function () {
				const select2 = $(this).select2('data');
				const value = select2[0].id;
				const title = select2[0].text;
				console.log(value, title);
				$(this)
					.closest('.acf-fields')
					.find('[data-name="select_product_attribute"] select optgroup')
					.each(function () {
						$(this)
							.find('option')
							.each(function () {
								if ($(this).text() === title) {
									$(this).prop('selected', true);
								}
							});
					});
				$(this)
					.closest('.acf-fields')
					.find('[data-name="select_product_attribute"] select')
					.trigger('change');
				$(this).closest('.acf-fields').find('.values ul').children().remove();
			}
		);
	});
});
