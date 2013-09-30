(function($) {  // Avoid conflicts with other libraries

"use strict";

/**
 * This callback alternates text - it replaces the current text with the text in
 * the alt-text data attribute, and replaces the text in the attribute with the
 * current text so that the process can be repeated.
 */
$(document).ready(function() {
	$('a[data-toggle-id]').on('click', function(e) {
		e.preventDefault();
		$('#' + $(this).attr('data-toggle-id')).toggle();
	});
});

phpbb.toggleId = function(element) {
	$('#' + $(element).attr('data-toggle-id')).toggle();
	console.log($('#' + $(element).attr('data-toggle-id')));
}

})(jQuery); // Avoid conflicts with other libraries
