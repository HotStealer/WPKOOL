/**
 * @package WP_ExternalMedia
 * Admin View JS.
 */

/**
 * File uploader.
 */
function external_media_upload( _plugin, _url, _filename ) {

  var _frame = wp.media.frame || wp.media.library;
  _frame.content.mode('browse');
  // @TODO: Refine this to use toolbar controller to set spinner on and off.
  jQuery( '.media-toolbar .spinner' ).css({ 'visibility' : 'visible', 'display' : 'block' });
  wp.media.post( 'upload-remote-file', {
    url: _url,
    plugin: _plugin,
    filename: _filename
  })
  .done( function( resp ) {
    var attachment = wp.media.model.Attachment.create( resp );
    attachment.fetch();
    _frame.state().get( 'library' ).add( attachment ? [ attachment ] : [] );
    if ( wp.media.frame._state != 'library' ) {
      _frame.state().get( 'selection' ).add( attachment );
    }
    // _frame.setState( 'library' );
    jQuery( '.media-toolbar .spinner' ).css({ 'visibility' : 'hidden', 'display' : 'none' });
  });
}

jQuery(function ($) {

  wp.media.view.EmbedUrl = wp.media.view.EmbedUrl.extend({
    focus: function() {
      var $input = this.$input;
      if ( $input.is( ':visible' ) ) {
        $input.focus()[0].select();
      }
      if ( !$( '#embed-url-field' ).hasClass( "external-media-processed" ) ) {
        var template = $( '#tmpl-external-media-links' ).html();
        $( '#embed-url-field' ).after( '<div style="position: absolute; top: 74px; font-size: 12px;">' + template + '</div>' );
        $( '#embed-url-field' ).addClass( "external-media-processed" );
      }
    }
  });

});
