/**
 * @package WP_ExternalMedia
 * Admin JS.
 */

jQuery(function ($) {

  var tabs = $( '#wp-external-media-settings-tabs li' );
  var contents = $( '#wp-external-media-settings-tab-contents li' );
  var _wp_http_referer = $( 'input[name="_wp_http_referer"]' ).attr( 'value' );

  currentIndex = window.location.hash.replace( '#plugin', '' );
  if ( currentIndex ) {
    $( tabs ).eq( currentIndex ).show();
    $( tabs ).eq( currentIndex ).addClass( 'current' );
    $( contents ).eq( currentIndex ).show();
    $( contents ).eq( currentIndex ).addClass( 'current' );
    $( 'input[name="_wp_http_referer"]' ).attr( 'value', _wp_http_referer + '#plugin' + currentIndex );
  }
  else {
    tabs.first().addClass( 'current' );
    contents.first().addClass( 'current' ).show();
  }

  tabs.on( 'click', function( e ) {
    var index = $( this ).index();
    window.location.hash = 'plugin' + index;
    contents.hide();
    tabs.removeClass( 'current' );
    contents.removeClass( 'current' );
    $( contents ).eq( index ).show();
    $( this ).addClass( 'current' );
    $( contents ).eq( index ).addClass( 'current' );
    $( 'input[name="_wp_http_referer"]' ).attr( 'value', _wp_http_referer + '#plugin' + index );
  });

});
