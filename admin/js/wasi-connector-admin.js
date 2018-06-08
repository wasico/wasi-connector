(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function(){
		var btn = $('#wasi-clear-cache');
		if(btn.length>0) {
			btn.click(wasiClearCache);
		}
	});


	function wasiClearCache(e) {
		e.stopPropagation();
		var msg = jQuery('#clear-cache-results');
		var params = {action: 'wasi_clear_cache'};
		msg.html('');
		$.post({
			url: ajaxurl,
			data: params
		}).done(function(res){
			// console.log('Aamzing!!', res);
			msg.html('Removed '+res.total+' files in cache!');
		}).fail(function(err){
			// console.log('ERROR Clearing cache:', err);
			msg.html('ERROR:' + err);
		});
		return false;
	}

})( jQuery );
