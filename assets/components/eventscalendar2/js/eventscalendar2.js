// Month changing
$(document).on('click', '#Calendar .prev a, #Calendar .next a', function() {
	var cal = $(this).parentsUntil('.calendar').parent();
	var href = $(this).attr('href');
	cal.css('opacity', 0.7);

	var width = cal.css('width');
	var height = cal.css('height');
	cal.parent().find('.cover').css({'width': width, 'height': height, 'margin-top': '-' + height}).show();

	$.get(href, function(data) {
			cal.parent().find('.cover').hide();
			cal.replaceWith(data);
		}
	);
	return false;
});

// Display events
$(document).on('mouseenter', '#Calendar .isevent', function() {
	$('#' + this.id + ' .event').show();
});
$(document).on('mouseleave', '#Calendar .isevent', function() {
	$('#' + this.id + ' .event').hide();
});