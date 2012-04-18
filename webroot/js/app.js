/**
 * 
 */
jQuery(document).ready(function ($) {
	$('img.listing').each(function(){
		$(this).attr('src', $(this).attr('data-src'));
	});
});