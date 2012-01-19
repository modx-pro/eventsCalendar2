$(document).ready(function() {

	// Внешний вид календаря при загрузке
	$('#Calendar .calendar').ajaxStart(function() {
		$(this).css('opacity', 0.3);
		var width = $(this).css('width');
		var height = $(this).css('height');
		
		$(this).parent().find('.cover').css({'width': width, 'height': height, 'margin-top': '-' + height}).show();
	})

	$('#Calendar .calendar').ajaxStop(function() {
		$(this).css('opacity', 1)
		$(this).parent().find('.cover').hide();
	})
	////

	// Смена месяца
	$('#Calendar .prev a, #Calendar .next a').live('click', function() {
		var href = $(this).attr('href');
		$.get(href, function(data) {
				$('#Calendar').html(data);
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