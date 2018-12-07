/**
 * @package WP_ExternalMediaSE
 * ExternalLink integration.
 */

jQuery(function ($) {

  $( 'body' ).on( 'click', 'a.em-external-link, button.em-external-link',  function( e ) {
    var $plugin = $( this ).data( 'plugin' );
    var $parent_ = $( this ).parent();
    $parent_.find( '.em-external-link-modal' ).show();
    $parent_.find( '.el-insert' ).click(function( e ) {
      var url = $parent_.find('.em-form #url').val();
      if ( url !== '' ) {
        var filename = url.replace(/^.*[\\\/]/, '');
        external_media_upload( $plugin, url, filename );
        $parent_.find( '.em-external-link-modal' ).hide();
      }
    });
    $parent_.find( '.el-cancel' ).click(function( e ) {
      $parent_.find( '.em-form #url' ).val('');
      $parent_.find( '.em-external-link-modal' ).hide();
    });
    e.preventDefault();
  });

});
