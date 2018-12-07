/**
 * @package WP_ExternalMediaSE
 * Instagram integration.
 */

jQuery(function ($) {

  $( 'body' ).on( 'click', 'a#instagram-picker, button#instagram-picker',  function( e ) {
    var $type = $( this ).data( 'type' );
    var $plugin = $( this ).data( 'plugin' );
    var $cardinality = $( this ).data( 'cardinality' );
    // Instagram pluigin.
    var _picker_url = _instagram_loginUrl;

    // Generate unique window ID.
    var _window_id = Math.floor(
        Math.random() * 0x10000 /* 65536 */
      ).toString( 16 );

    // Set window ID. This will help me to find this button from a parent window.
    $( this ).addClass( 'popup-opened' ).addClass( 'window-id-' + _window_id );

    // Open a new window.
    var InstagramPicker = window.open( _picker_url, _window_id, "directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,width=810,height=650" );

    InstagramPicker.onbeforeunload = function ( e ) {

      // File objects.
      var _file_string = InstagramPicker.document.getElementById( 'files' ).innerHTML;
      var files = _file_string.split( '|' );

      if ( _file_string !== undefined && _file_string != '' && _file_string != null ) {

        if ( $type == 'url' ) {
          var _item = files[0].split( '::' );
          $( '#embed-url-field' ).val( _item[1] ).change();
        }
        else {
          var _count = 0;
          for ( var i = 0; i < files.length; i++ ) {
            if ( $cardinality > 1 ) {
              if ( _count < $cardinality ) {
                var _item = files[i].split( '::' );
                var filename = _item[0].replace(/^.*[\\\/]/, '');
                external_media_upload( $plugin, _item[0], filename );
                _count++;
              }
            }
          }
        }

      }

      // Remove identifier class name from the button.
      $( 'a.window-id-' + InstagramPicker.name )
        .removeClass( 'popup-opened' )
        .removeClass( 'window-id-' + InstagramPicker.name );
      return message;
    }

    e.preventDefault();
  });

});
