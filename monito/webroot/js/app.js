/**
 *
 */
!function($){
	$(function() {
		//prototype dom hacking
		$('.summary table').addClass('table table-bordered table-striped');
		$('ul.actions li:first-child').addClass('active').find('strong').wrap('<a></a>');
		$('ul.actions').addClass('nav nav-tabs nav-stacked');
		$('ul.actions').wrap('<div class="span3"></div>');
		$('.scaffold-index, .scaffold-form, .scaffold-view').addClass('row-fluid');
		$('.navbar').after($('h2', $('.scaffold-index, .scaffold-form, .scaffold-view')));
		$('.scaffold-index .summary, .scaffold-form .form, .scaffold-view .details').addClass('span9');
		$('.scaffold-form .form input[type="submit"]').addClass('btn');
		$('.scaffold-form .form input[type="text"]').addClass('input-xlarge');
	});
}(jQuery);