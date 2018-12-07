<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * CloudApp integration class.
 */
class CloudApp extends WP_ExternalPluginBase {

  protected static $cloudapp_loaded = false;
  protected static $cloudapp_lib_loaded = false;
  protected $api_endpoint = 'https://api.instagram.com/v1';
  protected $cloudapp;
  protected $version = 10;

  /**
   * Implements __construct().
   */
  public function __construct() {
    add_action( 'admin_head', array( &$this, 'assets' ) );
    $email = get_option( WP_ExternalMedia_Prefix . 'cloudapp_email' );
    $password = get_option( WP_ExternalMedia_Prefix . 'cloudapp_password' );
    if ( !$this::$cloudapp_lib_loaded ) {
      require_once ( sprintf( "%s/plugins/CloudApp/lib/Exception.php", WP_ExternalMedia_PATH ) );
      require_once ( sprintf( "%s/plugins/CloudApp/lib/API.php", WP_ExternalMedia_PATH ) );
      $this::$cloudapp_lib_loaded = true;
    }
    $this->cloudapp = new CloudApp\API($email, $password, 'ExternalMediaWP');
  }

  /**
   * {@inheritdoc}
   */
  public function name() {
    return __('CloudApp');
  }

  /**
   * {@inheritdoc}
   */
  public function weight() {
    return -7;
  }

  /**
   * {@inheritdoc}
   */
  public function importLabel() {
    return __('Import from CloudApp');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to CloudApp');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'cloudapp-picker';
  }

  /**
   * {@inheritdoc}
   */
  public function attributes( $items ) {
    $attributes = array();
    foreach ( $items as $attribute => $value ) {
      // Instagram File Picker doesn't support filter files by their types.
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
    if ( $this::$cloudapp_loaded ) {
      return;
    }
    wp_register_script( get_class($this), plugins_url( '/plugins/js/CloudApp.js?v=' . $this->version, WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js' ) );
    wp_enqueue_script( get_class($this) );
    echo '<script type="text/javascript">
      var _cloudapp_file_viewer = \''  . $this->redirectUri( get_class($this) ) . '\';
    </script>';
    $this::$cloudapp_loaded = true;
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {

    $elements['warning'] = array(
      '#type' => 'markup',
      '#markup' => '<div><p>' . __('Explain with a screen recording. Answer with a GIF. Clarify with a screenshot. <br/>Seriously this is a really awesome service, <a href="http://www.shareasale.com/r.cfm?B=1027572&U=1597643&M=71652&urllink=" target="_blank">give it a try</a>!') . '</p></div><hr/><br/>',
    );

    $elements['cloudapp_email'] = array(
      '#title' => __('Email'),
      '#type' => 'textfield',
      '#description' => __('<a href="http://www.shareasale.com/r.cfm?B=1027572&U=1597643&M=71652&urllink=" target="_blank">Signup to CloudApp</a>.'),
      '#placeholder' => __('Your Email Address'),
    );

    $elements['cloudapp_password'] = array(
      '#title' => __('Password'),
      '#type' => 'password',
      '#description' => '',
      '#placeholder' => __('Your Password'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function download( $file, $filename ) {
    // Remove parameters from URL.
    // This will prevent from saving a file with "?" parameter which generates
    // broken thumbnails.
    $filename = strtok($filename, '?');
    $attachment_id = $this->save_remote_file( $file, get_class($this), $filename );
    if ( ! $attachment = wp_prepare_attachment_for_js( $attachment_id ) ) {
      wp_send_json_error();
    }
    wp_send_json_success( $attachment );
  }

  /**
   * {@inheritdoc}
   */
  public function redirectContents() {
    return array(
      'title' => __('CloudApp'),
      'data' => array(
        'media' => $this->getCloudAppMedia(),
      ),
      'head'    => '<link rel="stylesheet" type="text/css" href="' . plugins_url( '/plugins/CloudApp/cloudapp-popup.css?v=' . $this->version, WP_ExternalMedia_PluginName ) . '">',
      'content' => '',
      'footer'  => '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="' . plugins_url( '/plugins/CloudApp/cloudapp-popup.js?v=' . $this->version, WP_ExternalMedia_PluginName ) . '"></script>',
    );
  }

  /**
   * Get all user media.
   */
  protected function getCloudAppMedia() {

    $items = wp_cache_get( 'em_cloudapp_media', get_class($this), 3600 );
    if ( false === $items ) {
      try {
        $items = $this->cloudapp->getItems(1, 100);
      }
      catch (\CloudApp\Exception $e) {
        $items['error'] = TRUE;
      }
      //print_r($items);exit;
      wp_cache_set( 'em_cloudapp_media', $items, get_class($this) );
    }

    return $items;
  }

}
