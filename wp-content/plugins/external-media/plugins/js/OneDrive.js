/**
 * @package WP_ExternalMediaSE
 * OneDrive integration.
 */

jQuery(function ($) {

  $( 'body' ).on( 'click', 'a#one-drive-picker, button#one-drive-picker',  function( e ) {
    var $type = $( this ).data( 'type' );
    var $plugin = $( this ).data( 'plugin' );
    var $cardinality = $( this ).data( 'cardinality' );
    // OneDrive plugin.
    var pickerOptions = {
      success: function( file ) {
        if ( $type == 'url' ) {
          $( '#embed-url-field' ).val( file.values[0].link ).change();
        }
        else {
          var _count = 0;
          for ( var i = 0; i < file.values.length; i++ ) {
            if ( $cardinality > 1 ) {
              if ( _count < $cardinality ) {
                external_media_upload( $plugin, file.values[i].link, file.values[i].fileName );
                _count++;
              }
            }
          }
        }
      },
      cancel: function() {
         // handle when the user cancels picking a file
      },
      linkType: ( $type == 'url' ) ? 'webViewLink' : 'downloadLink',
      multiSelect: ( $type == 'url' ) ? false : true
    }

    OneDrive.open( pickerOptions );
    e.preventDefault();
  });

});
