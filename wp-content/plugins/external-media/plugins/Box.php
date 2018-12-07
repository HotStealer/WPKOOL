<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * The Box Picker API integration class.
 */
class Box extends WP_ExternalPluginBase {

  protected static $box_loaded = false;

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
    return __('Box');
  }

  /**
   * {@inheritdoc}
   */
  public function weight() {
    return -9;
  }

  /**
   * {@inheritdoc}
   */
  public function importLabel() {
    return __('Import from Box');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to Box');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'box-picker';
  }

  /**
   * {@inheritdoc}
   */
  public function attributes( $items ) {
    $attributes = array();
    foreach ( $items as $attribute => $value ) {
      // Box doesn't support filter files by their types.
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
    if ( $this::$box_loaded ) {
      return;
    }
    $box_client_id = get_option( WP_ExternalMedia_Prefix . 'box_client_id' );
    wp_register_script( get_class($this), plugins_url( '/plugins/js/Box.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js' ) );
    wp_enqueue_script( get_class($this) );
    echo '<script type="text/javascript"> var _box_client_id = \''  . $box_client_id . '\'; </script>';
    echo '<script type="text/javascript" src="https://app.box.com/js/static/select.js"></script>';
    $this::$box_loaded = true;
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {

    $elements['box_client_id'] = array(
      '#title' => __('Box Client ID'),
      '#type' => 'textfield',
      '#description' => __('Please <a href="https://app.box.com/developers/services" target="_blank">create a Box Application</a> to get the Client ID.'),
      '#placeholder' => __('Your Client ID'),
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
