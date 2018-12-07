<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * The OneDrive API integration class.
 */
class OneDrive extends WP_ExternalPluginBase {

  protected static $onedrive_loaded = false;

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
    return __('OneDrive');
  }

  /**
   * {@inheritdoc}
   */
  public function weight() {
    return -8;
  }

  /**
   * {@inheritdoc}
   */
  public function importLabel() {
    return __('Import from OneDrive');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to OneDrive');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'one-drive-picker';
  }

  /**
   * {@inheritdoc}
   */
  public function attributes( $items ) {
    $attributes = array();
    foreach ( $items as $attribute => $value ) {
      // OneDrive doesn't support filter files by their types.
      if ( $attribute != 'mime-types' ) {
        $attributes[$attribute] = $value;
      }
    }
    return $this->renderAttributes( $attributes );
  }

  /**
   * {@inheritdoc}
   */
  public function assets() {
    if ( $this::$onedrive_loaded ) {
      return;
    }
    $onedrive_app_id = get_option( WP_ExternalMedia_Prefix . 'onedrive_app_id' );
    wp_register_script( get_class($this), plugins_url( '/plugins/js/OneDrive.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js' ) );
    wp_enqueue_script( get_class($this) );
    echo '<script type="text/javascript" src="https://js.live.net/v5.0/OneDrive.js" id="onedrive-js" client-id="' . $onedrive_app_id . '"></script>';
    $this::$onedrive_loaded = true;
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {

    $elements['warning'] = array(
      '#type' => 'markup',
      '#markup' => '<div><p>' . __('<strong>Warning:</strong> OneDrive button doesn\'t always trigger the popup. You have to keep pressing the button until the popup shows up. ')
        . __('It behaves the same even on the MS\'s website. See <a href="https://dev.onedrive.com/sdk/javascript-picker-saver.htm" target="_blank">https://dev.onedrive.com/sdk/javascript-picker-saver.htm</a> ')
        . __('It might start working well once they fixe the issue.')
        . '</p></div><hr/><br/>',
    );

    $elements['onedrive_app_id'] = array(
      '#title' => __('OneDrive App ID/Client ID'),
      '#type' => 'textfield',
      '#description' => __('Please <a href="https://account.live.com/developers/applications" target="_blank">Register your app</a> to get an app ID (client ID), if you haven\'t already done so. ')
        . __('Ensure that the web page that is going to reference the SDK is a <em>Redirect URL</em> under <strong>Application Settings</strong>.'),
      '#placeholder' => __('Your Application ID'),
    );

    $elements['onedrive_instructions'] = array(
      '#type' => 'markup',
      '#markup' => __('<p>Most people have problems with properly configuring the OneDrive app.'
        . ' - First of all make sure you <a href="https://account.live.com/developers/applications" target="_blank">register your app</a>.<br/>'
        . ' - Set <strong>Mobile or desktop client app</strong> to <strong>No</strong>.<br/> - Leave Target domain empty.<br/>'
        . ' - Set <strong>Restrict JWT issuing</strong> to <strong>Yes</strong>.<br/>'
        . ' Now goes the tricky part - You have to add your wp-admin/edit.php?post_type=page page paths as <strong>Redirect URLs</strong>.'
        . ' For instance: http://example.com/wp-admin/post-new.php, http://wp.local.com:8888/wp-admin/post-new.php?post_type=page. <br/>'
        . 'To <strong>FIX</strong> this issue you would have to provide all the page URLs where you are going to use the uploader.</p>'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function download( $file, $filename ) {
    $attachment_id = $this->save_remote_file( $file, get_class($this), $filename );
    if ( ! $attachment = wp_prepare_attachment_for_js( $attachment_id ) ) {
      wp_send_json_error();
    }
    wp_send_json_success( $attachment );
  }

}
