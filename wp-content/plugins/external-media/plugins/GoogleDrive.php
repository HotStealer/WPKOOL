<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * Google Picker API integration class.
 */
class GoogleDrive extends WP_ExternalPluginBase {

  protected static $google_drive_loaded = false;

  /**
   * Implements __construct().
   */
  public function __construct() {
    add_action( 'admin_head', array( &$this, 'assets' ) );
  }

  /**
   * {@inheritdoc}
   */
  public function name() {
    return __('GoogleDrive');
  }

  /**
   * {@inheritdoc}
   */
  public function weight() {
    return -6;
  }

  /**
   * {@inheritdoc}
   */
  public function importLabel() {
    return __('Import from Google Drive');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to Google Drive');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'google-picker';
  }

  /**
   * {@inheritdoc}
   */
  public function attributes( $items ) {
    $attributes = array();
    foreach ( $items as $attribute => $value ) {
      if ( $attribute == 'mime-types' ) {
        $mime_types = array();
        foreach ( $value as $exts => $mime_type ) {
          $mime_types[] = $mime_type;
        }
        $attributes[$attribute] = join( ',', $mime_types );
      }
      else {
        $attributes[$attribute] = $value;
      }
    }
    return $this->renderAttributes( $attributes );
  }

  /**
   * {@inheritdoc}
   */
  public function assets() {
    if ( $this::$google_drive_loaded ) {
      return;
    }
    $google_client_id = get_option( WP_ExternalMedia_Prefix . 'google_client_id' );
    $google_app_id = get_option( WP_ExternalMedia_Prefix . 'google_app_id' );
    $google_parent_folder = get_option( WP_ExternalMedia_Prefix . 'google_parent_folder' );
    $google_show_folders = get_option( WP_ExternalMedia_Prefix . 'google_show_folders', 1 );
    $google_owned_by_me = get_option( WP_ExternalMedia_Prefix . 'google_owned_by_me', 0 );
    $google_starred_only = get_option( WP_ExternalMedia_Prefix . 'google_starred_only', 0 );
    $google_view_id = get_option( WP_ExternalMedia_Prefix . 'google_view_id' );
    // Google scopes.
    $google_scope = array('"https://www.googleapis.com/auth/drive.readonly"');
    wp_register_script( get_class( $this ), plugins_url( '/plugins/js/GoogleDrive.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js', 'GoogleDrive_lib' ) );
    wp_register_script( get_class( $this ) . '_lib', 'https://apis.google.com/js/api.js' );
    wp_enqueue_script( get_class( $this ) );
    wp_enqueue_script( get_class( $this ) . '_lib' );
    echo '<script type="text/javascript">
      var _google_client_id = \''  . $google_client_id . '\';
      var _google_app_id = \''  . $google_app_id . '\';
      var _google_scope = [' . join(", ", $google_scope) . '];
      var _google_parent_folder = \''  . ( !empty($google_parent_folder) ? $google_parent_folder : 'root' ) . '\';
      var _google_show_folders = ' . ( !empty($google_show_folders) ? 'true' : 'false' ) . ';
      var _google_owned_by_me = ' . ( !empty($google_owned_by_me) ? 'true' : 'false' ) . ';
      var _google_starred_only = ' . ( !empty($google_starred_only) ? 'true' : 'false' ) . ';
      var _google_view_id = ' . ( !empty($google_view_id) ? $google_view_id : 0 ) . ';
    </script>';
    $this::$google_drive_loaded = true;
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {

    $elements['google_instructions'] = array(
      '#type' => 'markup',
      '#markup' => __('<p>To get started using Google Picker API, you need to first '
        . '<a href="https://console.developers.google.com/flows/enableapi?apiid=picker" target="_blank">'
        . 'create or select a project in the Google Developers Console and enable the API</a>.</p>'
        . '<ul><li>Enable <strong>Google Picker API</strong> <em>(required)</em></li>'
        . '<li>Enable <strong>Drive API</strong> <em>(required)</em></li></ul>'
        . '<p>Read more about <em>Scopes</em> and more details about how to get credentials on the '
        . '<a href="https://developers.google.com/picker/docs/" target="_blank">documentaion page</a>.'),
    );

    $elements['google_client_id'] = array(
      '#title' => __('Client ID'),
      '#type' => 'textfield',
      '#description' => __('The Client ID obtained from the Google Developers Console. e.g. <em>886162316824-pfrtpjns2mqnek6e35gv321tggtmp8vq.apps.googleusercontent.com</em>'),
      '#placeholder' => __('Your Client ID'),
    );

    $elements['google_app_id'] = array(
      '#title' => __('Application ID'),
      '#type' => 'textfield',
      '#description' => __('Its the first number in your Client ID. e.g. <em>886162316824</em>'),
      '#placeholder' => __('Your Application ID'),
    );

    $elements['google_view_id'] = array(
      '#title' => __('View ID'),
      '#type' => 'select',
      '#description' => __('By default: All Google Drive document types.'),
      '#options' => array(
        __('All Google Drive document types.'),
        __('A collection of most recently selected items.'),
        __('Google Drive photos.'),
        __('Google Drive photos and videos.'),
        __('Google Drive videos.'),
        __('Google Drive Documents.'),
        __('Google Drive Drawings.'),
        __('Google Drive Folders.'),
        __('Google Drive Forms.'),
        __('PDF files stored in Google Drive.'),
        __('Google Drive Presentations.'),
        __('Google Drive Spreadsheets.'),
      ),
    );

    $elements['google_starred_only'] = array(
      '#title' => __('Filter'),
      '#type' => 'checkbox',
      '#label' => __('Show starred items only.'),
      '#description' => __('Filters the documents based on whether they are starred by the user.'),
      '#default_value' => FALSE,
    );

    $elements['google_owned_by_me'] = array(
      '#title' => __('Shared Files'),
      '#type' => 'checkbox',
      '#label' => __('Show only files owned by me.'),
      '#description' => __('Filters the documents based on whether they are owned by the user, or shared with the user.'),
      '#default_value' => FALSE,
    );

    $elements['google_show_folders'] = array(
      '#title' => __('Show Folders'),
      '#type' => 'checkbox',
      '#label' => __('Show folders in the view items.'),
      '#default_value' => TRUE,
    );

/*
    $elements['google_parent_folder'] = array(
      '#title' => __('Parent Folder'),
      '#type' => 'textfield',
      '#description' => __('<strong>Important</strong>: Not every user may have the folder you specify here. Set the initial parent folder to display. Folder IDs can be obtain through Drive API. By default it is : root.'),
      '#placeholder' => __('root'),
    );*/

    $elements['google_redirect_uri'] = array(
      '#type' => 'markup',
      '#title' => __('Redirect URL'),
      '#markup' => '<div>' . get_site_url() . '/index.php?external_media_plugin=' . get_class( $this ) . '</div>',
      '#description' => __('This is the URL you will need for the redirect URL/OAuth authentication.'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function download( $file, $filename ) {
    list ( $file_id, $google_token ) = explode( ':::', $file );
    $google_drive_file_url = 'https://www.googleapis.com/drive/v2/files/' . $file_id . '?alt=media';
    $options = array(
      'headers' => array(
        'Authorization: Bearer ' . $google_token,
      ),
    );
    $attachment_id = $this->save_remote_file( $google_drive_file_url, get_class($this), $filename, $options );
    if ( ! $attachment = wp_prepare_attachment_for_js( $attachment_id ) ) {
      wp_send_json_error();
    }
    wp_send_json_success( $attachment );
    
  }

  /**
   * {@inheritdoc}
   */
  public function redirectCallback() {
    $this->assets();
    return ' ';
  }

}
