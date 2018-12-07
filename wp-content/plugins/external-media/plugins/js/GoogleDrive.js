/**
 * @package WP_ExternalMediaSE
 * GoogleDrive integration.
 */

jQuery(function ($) {

  // The Client ID obtained from the Google Developers Console. Replace with your own Client ID.
  var clientId = _google_client_id;

  // Replace with your own App ID. (Its the first number in your Client ID)
  var appId = _google_app_id;

  // Scope to use to access user's Drive items.
  var scope = _google_scope;

  // Parent folder to display. 'root' by default.
  var parent_folder = _google_parent_folder;

  // Show folders in the view items. (true/false).
  var show_folders = _google_show_folders;

  // Show files owned by me. (true/false).
  var owned_by_me = _google_owned_by_me;

  // Filter starred items. (true/false).
  var starred_only = _google_starred_only;

  // View Id. By Default 'ViewId.DOCS'.
  var view_id = _google_view_id;

  var pickerApiLoaded = false;
  var oauthToken;
  var _parent_container;

  function onAuthApiLoad() {
    window.gapi.auth.authorize({
        'client_id' : clientId,
        'scope'     : scope,
        'immediate' : false
      },
      handleAuthResult
    );
  }

  function onPickerApiLoad() {
    pickerApiLoaded = true;
    createPicker();
  }

  function handleAuthResult( authResult ) {
    if ( authResult && !authResult.error ) {
      oauthToken = authResult.access_token;
    }
    createPicker();
  }

  // Create and render a Picker object for searching images.
  function createPicker() {
    if ( pickerApiLoaded && oauthToken ) {
      var views = [
        google.picker.ViewId.DOCS,
        google.picker.ViewId.RECENTLY_PICKED,
        google.picker.ViewId.DOCS_IMAGES,
        google.picker.ViewId.DOCS_IMAGES_AND_VIDEOS,
        google.picker.ViewId.DOCS_VIDEOS,
        google.picker.ViewId.DOCUMENTS,
        google.picker.ViewId.DRAWINGS,
        google.picker.ViewId.FOLDERS,
        google.picker.ViewId.FORMS,
        google.picker.ViewId.PDFS,
        google.picker.ViewId.PRESENTATIONS,
        google.picker.ViewId.SPREADSHEETS
      ];
      var view = new google.picker.DocsView(views[view_id]);
      if (show_folders == true) {
        view.setIncludeFolders(show_folders);
      }
      if (owned_by_me == true) {
        view.setOwnedByMe(owned_by_me);
      }
      if (starred_only == true) {
        view.setStarred(starred_only);
      }
      
      if (parent_folder != 'root') {
        view.setParent(parent_folder);
      }

      var $type = _parent_container.data( 'type' );
      var $plugin = _parent_container.data( 'plugin' );
      var $extensions = _parent_container.data( 'mime-types' );
      var $cardinality = _parent_container.data( 'cardinality' );

      // Set allowed extensions.
      if ( $type != 'url' ) {
        view.setMimeTypes( $extensions );
      }
      // Build a file picker.
      var picker = new google.picker.PickerBuilder()
        .enableFeature( google.picker.Feature.NAV_HIDDEN )
        .enableFeature( google.picker.Feature.MULTISELECT_ENABLED )
        .setAppId( appId )
        .setOAuthToken( oauthToken )
        .addView( view )
        .setMaxItems( $cardinality )
        .setCallback(function( data ) {
          if ( data.action == google.picker.Action.PICKED ) {
            var _links = [];
            if ( $type == 'url' ) {
              $( '#embed-url-field' ).val( data.docs[0].url ).change();
            }
            else {
              var _count = 0;
              for ( var i = 0; i < data.docs.length; i++ ) {
                if ( $cardinality > 1 ) {
                  if ( _count < $cardinality ) {
                    external_media_upload( $plugin, data.docs[i].id + ':::' + oauthToken, data.docs[i].name );
                    _count++;
                  }
                }
              }
            }

          }
        })
        .build();
      picker.setVisible( true );

      var elements = document.getElementsByClassName( 'picker-dialog' );
      for ( var i = 0; i < elements.length; i++ ) {
        // Make sure GoogleDrive popup shows up on top of the Media Library popup.
        elements[i].style.zIndex = "999999";
      }
    }
  }

  $( 'body' ).on( 'click', 'a#google-picker, button#google-picker',  function( e ) {
    _parent_container = $( this );
    // Google Drive plugin.
    gapi.load( 'auth', { 'callback': onAuthApiLoad } );
    if ( pickerApiLoaded === false ) {
      gapi.load( 'picker', { 'callback': onPickerApiLoad } );
    }
    e.preventDefault();
  });

});
