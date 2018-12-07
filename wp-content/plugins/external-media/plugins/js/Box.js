/**
 * @package WP_ExternalMediaSE
 * Box integration.
 */

jQuery(function ($) {

  $( 'body' ).on( 'click', 'a#box-picker, button#box-picker',  function( e ) {
    var $type = $( this ).data( 'type' );
    var $plugin = $( this ).data( 'plugin' );
    var $cardinality = $( this ).data( 'cardinality' );
    // Box pluigin.
    var boxSelect = new BoxSelect({
      clientId: _box_client_id,
      linkType: ( $type == 'url' ) ? 'shared' : 'direct',
      multiselect: ( $type == 'url' ) ? false : true
    });
    boxSelect.success( function( files ) {
      if ( $type == 'url' ) {
        $( '#embed-url-field' ).val( files[0].url ).change();
      }
      else {
        var _count = 0;
        for ( var i = 0; i < files.length; i++ ) {
          if ( $cardinality > 1 ) {
            if ( _count < $cardinality ) {
              external_media_upload( $plugin, files[i].url, files[i].name );
              _count++;
            }
          }
        }
      }
    });

    boxSelect.launchPopup();
    e.preventDefault();
  });

});
