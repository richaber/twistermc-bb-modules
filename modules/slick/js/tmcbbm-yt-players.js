/**
 * Helper script for dealing with YouTube iFrame API.
 *
 * Modified version of code by Rob Wu <rob@robwu.nl> provided on SO for working with multiple YouTube players.
 *
 * @link https://stackoverflow.com/questions/8948403/youtube-api-target-multiple-existing-iframes#8949636
 */

/**
 * Player storage.
 */
var tmcbbmYtPlayers = {};

/**
 *
 * @param id
 * @returns {*}
 */
function tmcbbmYtGetFrameID( id ) {

	var elem = document.getElementById( id );

	if ( elem ) {
		if ( /^iframe$/i.test( elem.tagName ) ) return id; //Frame, OK
		// else: Look for frame
		var elems = elem.getElementsByTagName( "iframe" );
		if ( !elems.length ) return null; // No iframe found, FAILURE
		for ( var i = 0; i < elems.length; i++ ) {
			if ( /^https?:\/\/(?:www\.)?youtube(?:-nocookie)?\.com(\/|$)/i.test( elems[ i ].src ) ) break;
		}
		elem = elems[ i ]; // The only, or the best iFrame
		if ( elem.id ) return elem.id; // Existing ID, return it
		// else: Create a new ID
		do { // Keep postfixing `-frame` until the ID is unique
			id += "-frame";
		} while ( document.getElementById( id ) );
		elem.id = id;
		return id;
	}
	// If no element, return null.
	return null;
}

/**
 * Define YT_ready function.
 */
var YT_ready = (function() {

	var onReady_funcs = [],
		api_isReady = false;

	/**
	 * @param func function     Function to execute on ready
	 * @param func Boolean      If true, all qeued functions are executed
	 * @param b_before Boolean  If true, the func will added to the first position in the queue.
	 */
	return function( func, b_before ) {
		if ( func === true ) {
			api_isReady = true;
			for ( var i = 0; i < onReady_funcs.length; i++ ) {
				/**
				 * Removes the first func from the array, and execute func.
				 */
				onReady_funcs.shift()();
			}
		}

		else if ( typeof func == "function" ) {
			if ( api_isReady ) func();
			else onReady_funcs[ b_before ? "unshift" : "push" ]( func );
		}
	}
})();

/**
 * This function will be called when the YouTube Iframe API is fully loaded.
 */
function onYouTubePlayerAPIReady() {
	YT_ready( true );
}

/**
 *
 */
YT_ready( function() {

	jQuery( 'iframe[src*="youtube.com"]' ).each( function() {
		var identifier = this.id;
		var frameID = tmcbbmYtGetFrameID( identifier );
		if ( frameID ) {
			tmcbbmYtPlayers[ frameID ] = new YT.Player( frameID, {
				events: {
					"onReady": tmcbbmYtCreateYTEvent( frameID, identifier )
				}
			} );
		}
	} );
} );

/**
 * Returns a function to enable multiple events.
 *
 * @param frameID
 * @param identifier
 * @returns {Function}
 */
function tmcbbmYtCreateYTEvent( frameID, identifier ) {
	return function( event ) {
		var player = tmcbbmYtPlayers[ frameID ];
		var the_div = jQuery( '#' + identifier ).parent();
		the_div.children( '.thumb' ).click( function() {
			var $this = jQuery( this );
			$this.fadeOut().next().addClass( 'play' );
			if ( $this.next().hasClass( 'play' ) ) {
				player.playVideo();
			}
		} );
	}
}
