$(document).ready(function() {

	// Смена месяца
	$(document).on('click', '#Calendar .prev a, #Calendar .next a', function() {
		var cal = $(this).parentsUntil('.calendar').parent();
		var href = $(this).attr('href');
		
		$(cal).css('opacity', 0.7);

		var width = $(cal).css('width');
		var height = $(cal).css('height');
		
		$(cal).parent().find('.cover').css({'width': width, 'height': height, 'margin-top': '-' + height}).show();
		
		$.get(href, function(data) {
				$(cal).parent().find('.cover').hide();
				$('#Calendar .calendar').replaceWith(data);
			}
		)
		return false;    
	})

	// Показ событий
	$('#Calendar .isevent').live('mouseenter',function() {
		id = this.id;
		$('#' + id + ' .event').show();
	})
	$('#Calendar .isevent').live('mouseleave',function() {
		id = this.id;
		$('#' + id + ' .event').hide();
	})        

});