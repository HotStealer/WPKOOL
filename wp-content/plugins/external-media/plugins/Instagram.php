<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * Instagram integration class.
 */
class Instagram extends WP_ExternalPluginBase {

  protected static $instagram_loaded = false;
  protected $api_endpoint = 'https://api.instagram.com/v1';
  protected $photos;

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
    return __('Instagram');
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
    return __('Import from Instagram');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to Instagram');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'instagram-picker';
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
    if ( $this::$instagram_loaded ) {
      return;
    }
    wp_register_script( get_class($this), plugins_url( '/plugins/js/Instagram.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js' ) );
    wp_enqueue_script( get_class($this) );
    echo '<script type="text/javascript">
      var _instagram_loginUrl = \''  . $this->getLoginUrl() . '\';
    </script>';
    $this::$instagram_loaded = true;
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {

    $elements['warning'] = array(
      '#type' => 'markup',
      '#markup' => '<div><p>' . __('<strong>Note:</strong> Please note you might need to re-open Instagram file picker popup after you first time login.')
        . '<br/>' . __('Please also note that users only can choose their own photos.') . '</p></div><hr/><br/>',
    );

    $elements['instagram_client_id'] = array(
      '#title' => __('Client ID'),
      '#type' => 'textfield',
      '#description' => __('Instagram Client ID. <a href="https://instagram.com/developer/clients/manage/" target="_blank">Set up a client for use with Instagram\'s API</a>'),
      '#placeholder' => __('Your Client ID'),
    );

    $elements['instagram_client_secret'] = array(
      '#title' => __('Client Secret'),
      '#type' => 'textfield',
      '#description' => __('Instagram Client Secret.'),
      '#placeholder' => __('Your Client Secret Key'),
    );

    $elements['instagram_redirect_uri'] = array(
      '#type' => 'markup',
      '#title' => __('Redirect URL'),
      '#markup' => '<div>' . $this->redirectUri( get_class($this) ) . '</div>',
      '#description' => __('Please use values of the <em>Redirect URL</em> address.'),
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
  public function redirectCallback() {
    $code = isset($_GET['code']) ? $_GET['code'] : FALSE;
    $error = isset($_GET['error']) ? $_GET['error'] : FALSE;
    if ( $code ) {
      $access_token = wp_cache_get( 'access_token', get_class($this), 3600 );
      if ( false === $access_token ) {
        // Get OAuth token.
        $options = array(
          'method'      => 'POST',
          'timeout'     => 45,
          'redirection' => 5,
          'httpversion' => '1.0',
          'blocking'    => true,
          'headers'     => array(),
          'body'        => array(
            'client_id'     => get_option( WP_ExternalMedia_Prefix . 'instagram_client_id' ),
            'client_secret' => get_option( WP_ExternalMedia_Prefix . 'instagram_client_secret' ),
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->redirectUri( get_class($this) ),
            'code'          => $code,
          ),
          'cookies'     => array()
        );
        $response = wp_remote_post( 'https://api.instagram.com/oauth/access_token', $options);
        if ( is_wp_error( $response ) ) {
          $error_message = $response->get_error_message();
          echo "Something went wrong: $error_message";
        }
        else {
          $info = json_decode( $response['body'] );
          if ( isset( $info->access_token ) ) {
            wp_cache_set( 'access_token', $info->access_token, get_class($this) );
            wp_redirect( $this->redirectUri( get_class($this) ) . '&access_token=' . $info->access_token );
          }
        }
      }
      else {
        if ( !empty( $access_token ) ) {
          wp_redirect( $this->redirectUri( get_class($this) ) . '&access_token=' . $access_token );
        }
      }
      exit();
    }
    if ( $error ) {
      echo isset($_GET['error_reason']) ? sanitize_text_field( $_GET['error_reason'] . ': ' . $_GET['error_description'] ) : '';
      exit();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function redirectContents() {
    $access_token = isset( $_GET['access_token'] ) ? strip_tags( $_GET['access_token'] ) : FALSE;
    // Instagram photos.
    $photos = $this->getUserPhotos( $access_token );
    // Currenly logged in user profile.
    $profile = $this->getUserProfile( $access_token );
    $counts = array(
      'media'       => sprintf( _n( '1 post', '%s posts', 'default' ), (int) $profile->counts->media ),
      'followed_by' => sprintf( _n( '1 follower', '%s followers', 'default' ), (int) $profile->counts->followed_by ),
      'follows'     => sprintf( _n( '1 following', '%s following', 'default' ), (int) $profile->counts->follows ),
    );

    return array(
      'title' => __('Instagram photos'),
      'data' => array(
        'photos'  => $photos,
        'counts'  => $counts,
        'profile' => $profile,
      ),
      'head'    => '<link rel="stylesheet" type="text/css" href="' . plugins_url( '/plugins/Instagram/instagram-popup.css', WP_ExternalMedia_PluginName ) . '">',
      'content' => '',
      'footer'  => '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="' . plugins_url( '/plugins/Instagram/instagram-popup.js', WP_ExternalMedia_PluginName ) . '"></script>',
    );
  }

  /**
   * Get Instagram login URL.
   */
  protected function getLoginUrl() {
    $instagram_client_id = get_option( WP_ExternalMedia_Prefix . 'instagram_client_id' );
    $instagram_redirect_uri = $this->redirectUri( get_class($this) );
    if ( !empty( $instagram_client_id ) && !empty( $instagram_redirect_uri ) ) {
      return 'https://api.instagram.com/oauth/authorize/?'
        . 'client_id=' . $instagram_client_id
        . '&redirect_uri=' . $instagram_redirect_uri
        . '&response_type=code';
    }
    else {
      return '';
    }
  }

  /**
   * Get recursive photos.
   */
  protected function getUserPhotosAll($url, $limit) {
    $request = wp_remote_get( $url );
    $media = json_decode( $request['body'] );
    if ( !empty( $media->data ) ) {
      foreach ( $media->data as $item ) {
        if ( isset( $item->images ) ) {
          $this->photos[] = array(
            'id'                  => $item->id,
            'thumbnail'           => $item->images->thumbnail,
            'standard_resolution' => $item->images->standard_resolution,
            'likes'               => $item->likes->count,
            'filter'              => $item->filter,
            'created_time'        => $item->created_time,
            'location'            => isset( $item->location->name ),
            'link'                => $item->link,
          );
        }
      }
    }
    if ( count( $this->photos ) <= $limit && !empty( $media->pagination->next_url ) ) {
      // Walk through all pages.
      return $this->getUserPhotosAll(
        $media->pagination->next_url, 
        $limit
      );
    }
    else {
      return $this->photos;
    }
  }

  /**
   * Get all user photos.
   */
  protected function getUserPhotos( $access_token ) {

    $limit = 200;

    $photos = array();

    if ( $access_token ) {
      $photos = wp_cache_get( 'em_instagram_photos', get_class($this), 3600 );
      if ( false === $photos ) {
        // Get the most recent media published by a user.
        $photos = $this->getUserPhotosAll(
          $this->api_endpoint . '/users/self/media/recent/?access_token=' . $access_token
          . '&count=' . $limit,
          $limit
        );
        wp_cache_set( 'em_instagram_photos', $photos, get_class($this) );
      }
    }

    return $photos;
  }

  /**
   * Profile information.
   */
  protected function getUserProfile( $access_token ) {
    if ( $access_token ) {
      $data = wp_cache_get( 'profile', get_class($this), 3600 );
      if ( false === $data ) {
        // Get the most recent media published by a user.
        $request = wp_remote_get( $this->api_endpoint . '/users/self/?access_token=' . $access_token );
        $data = json_decode( $request['body'] );
        wp_cache_set( 'profile', $data, get_class($this) );
      }
      if ( !empty( $data->data->username ) ) {
        return $data->data;
      }
    }
    return FALSE;
  }

}
