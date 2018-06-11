/**
 * Dismiss a notice.
 */
jQuery( function( $ ) {
	// Hook into our class on the notice
	$( document ).on( 'click', '.tmcbmm-notice .notice-dismiss', function() {
		// Read the "data-notice" information to track which notice
		// is being dismissed and send it via AJAX
		var notice_id = $( this ).closest( '.tmcbmm-notice' ).data( 'id' );
		// Make an AJAX call
		// Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.ajax( ajaxurl,
			{
				type: 'POST',
				data: {
					action: 'tmcbbm_dismiss_notice',
					notice_id: notice_id,
				}
			} );
	} );
} );
