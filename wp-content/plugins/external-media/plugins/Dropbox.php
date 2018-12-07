<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * Dropbox Chooser API integration class.
 */
class Dropbox extends WP_ExternalPluginBase {

  protected static $dropbox_loaded = false;

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
    return __('Dropbox');
  }

  /**
   * {@inheritdoc}
   */
  public function weight() {
    return -10;
  }

  /**
   * {@inheritdoc}
   */
  public function importLabel() {
    return __('Import from Dropbox');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to Dropbox');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'dropbox-file-chooser';
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
          $e = explode( '|', $exts );
          foreach ( $e as $ext ) {
            $mime_types[] = '.' . $ext;
          }
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
    if ( $this::$dropbox_loaded ) {
      return;
    }
    $dropbox_app_key = get_option( WP_ExternalMedia_Prefix . 'dropbox_app_key' );
    wp_register_script( get_class($this), plugins_url( '/plugins/js/Dropbox.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js' ) );
    wp_enqueue_script( get_class($this) );
    echo '<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="' . $dropbox_app_key . '"></script>';
    $this::$dropbox_loaded = true;
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {

    $elements['dropbox_app_key'] = array(
      '#title' => __('Dropbox App Key'),
      '#type' => 'textfield',
      '#description' => __('Please <a href="https://www.dropbox.com/developers/apps" target="_blank">create a Drop-in app</a> to get the App Key.'),
      '#placeholder' => __('Your Application Key'),
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
