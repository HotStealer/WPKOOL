/**
 * @package WP_ExternalMediaSE
 * Dropbox integration.
 */

jQuery(function ($) {

  $( 'body' ).on( 'click', 'a#dropbox-file-chooser, button#dropbox-file-chooser',  function( e ) {
    var $type = $( this ).data( 'type' );
    var $plugin = $( this ).data( 'plugin' );
    var $cardinality = $( this ).data( 'cardinality' );
    var $extensions = $( this ).data( 'mime-types' );
    // Dropbox plugin.
    Dropbox.choose({
      success: function( files ) {
        if ( $type == 'url' ) {
          $( '#embed-url-field' ).val( files[0].link ).change();
        }
        else {
          var _count = 0;
          for ( var i = 0; i < files.length; i++ ) {
            if ( $cardinality > 1 ) {
              if ( _count < $cardinality ) {
                external_media_upload( $plugin, files[i].link, files[i].name );
                _count++;
              }
            }
          }
        }
      },
      extensions: ( $type == 'url' ) ? '' : $extensions.split( ',' ),
      multiselect: ( $type == 'url' ) ? false : true,
      linkType: ( $type == 'url' ) ? 'preview' : 'direct'
    });
    e.preventDefault();
  });

});
