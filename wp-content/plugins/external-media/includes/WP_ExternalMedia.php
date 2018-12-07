<?php

/**
 * @package WP_ExternalMedia
 */

class WP_ExternalMedia extends WP_ExternalUtility {

  /**
   * Construct the plugin object.
   */
  public function __construct() {
    add_action( 'admin_menu', array( &$this, 'add_menu' ) );
    add_action( 'admin_init', array( &$this, 'admin_init' ) );
    add_action( 'pre-plupload-upload-ui', array( &$this, 'pre_plupload_upload_ui' ) );
    add_action( 'admin_head', array( &$this, 'admin_head' ) );
    add_action( 'init', array( &$this, 'init' ) );
    add_action( 'template_redirect', array( &$this, 'page_rewrite_redirect' ) );
    // File upload AJAX callback.
    add_action( 'wp_ajax_upload-remote-file', array( &$this, 'upload_remote_file' ) );
    // Add link to plugin settings page.
    add_filter( 'plugin_action_links_' . WP_ExternalMedia_PluginName, array( &$this, 'action_links' ) );
    // Add suport for embeds
    wp_embed_register_handler( 'cloudapp', '#https://cl\.ly/(.*)#i', array( &$this, 'embed_cloudapp' ) );
  }

  /**
   * Generate CloudApp embed
   */
  public function embed_cloudapp( $matches, $attr, $url, $rawattr ) {
    $embed = sprintf(
      '<div class="cloudapp-embed" data-slug="%s"><a href="https://cl.ly/%s">' . __('View in CloudApp') . '</a><script async src="https://embed.cl.ly/embedded.gz.js" charset="utf-8"></script></div>', 
      esc_attr($matches[1]), esc_attr($matches[1])
    );
    return apply_filters( 'embed_cloudapp', $embed, $matches, $attr, $url, $rawattr );
  }

  /**
   * Implements 'init'.
   */
  public function init() {
    if ( get_option( WP_ExternalMedia_Prefix . '_assets_frontend', 0 ) ) {
      $this->loadAssets( 'WP_ExternalMedia_public' );
    }
    // Plugin redirect callback paths.
    add_rewrite_tag( '%external_media_plugin%', '([^&]+)' );
    add_rewrite_rule( '^external\-media/?(.*)?', 'index.php?external_media_plugin=$matches[1]', 'top' );
  }

  /**
   * Implements 'wp_ajax_upload-remote-file'.
   * Upload remote file.
   */
  public function upload_remote_file() {
    $file = $_POST['url'];
    $plugin = $_POST['plugin'];
    $filename = $_POST['filename'];
    $loaded_plugin = $this->load_plugin( $plugin );
    $this->_call_class_method( $loaded_plugin['phpClassName'], 'download', array( $file, $filename ) );
  }

  /**
   * Implements 'template_redirect'.
   * Load plugin callback URI.
   */
  public function page_rewrite_redirect() {
    global $wp;
    $template = $wp->query_vars;
    $this->load_plugins();
    if ( array_key_exists( 'external_media_plugin', $template ) && !empty( $template['external_media_plugin'] ) ) {
      $phpClassName = strip_tags( $template['external_media_plugin'] );
      $this->_call_class_method( $phpClassName, 'redirectCallback' );
      $page = $this->_call_class_method( $phpClassName, 'redirectContents' );
      $callback_template_path = sprintf( "%s/templates/%s.php", WP_ExternalMedia_PATH, $phpClassName );
      if (!file_exists($callback_template_path)) {
        $callback_template_path = sprintf( "%s/templates/callback.php", WP_ExternalMedia_PATH );
      }
      require_once ( $callback_template_path );
      exit();
    }
  }

  /**
   * Implements 'admin_head'.
   */
  public function admin_head() {
    $buttons = array();
    $plugins = $this->load_plugins();
    $button_type = 'url';
    foreach ( $plugins as $plugin => $info ) {
      $showLinkButton = $this->_call_class_method( $info['phpClassName'], 'showLinkButton' );
      // Make sure only enabled plugins show up.
      $enabled = get_option( WP_ExternalMedia_Prefix . $plugin . '_enable', 0 );
      if ($enabled && $showLinkButton) {
        $attrs = array(
          'mime-types'  => get_allowed_mime_types(),
          'plugin'      => $plugin,
          'cardinality' => 1,
          'type'        => $button_type,
        );
        $buttons[] = array(
          'label'      => $this->_call_class_method( $info['phpClassName'], 'chooserLabel' ),
          'id'         => $this->_call_class_method( $info['phpClassName'], 'id' ),
          'attributes' => $this->_call_class_method( $info['phpClassName'], 'attributes', array( $attrs ) ),
        );
      }
    }
    require_once ( sprintf( "%s/templates/button.php", WP_ExternalMedia_PATH ) );
  }

  /**
   * Implements 'add_menu'.
   */     
  public function add_menu() {
    // Settings page.
    $settings_page = add_options_page(
      'External Media Settings',
      'External Media',
      'manage_options',
      'WP_ExternalMedia',
      array( &$this, '_settings_page' )
    );
    add_action( 'load-' . $settings_page, array( &$this, '_settings_page_help' ) );
  }

  /**
   * Implements 'admin_init'.
   */
  public function admin_init() {
    register_setting( 'WPExternalMedia', WP_ExternalMedia_Prefix . '_prepend_plugin_name' );
    register_setting( 'WPExternalMedia', WP_ExternalMedia_Prefix . '_assets_frontend' );
    // Register plugin settings.
    $plugins = $this->load_plugins();
    foreach ( $plugins as $plugin => $info ) {
      $settings = $this->_call_class_method( $info['phpClassName'], 'configForm', array() );
      register_setting( 'WPExternalMedia', WP_ExternalMedia_Prefix . $plugin . '_enable' );
      register_setting( 'WPExternalMedia', WP_ExternalMedia_Prefix . $plugin . '_insert_url_only' );
      foreach ($settings as $setting_name => $options) {
        register_setting( 'WPExternalMedia', WP_ExternalMedia_Prefix . $setting_name );
      }
    }
    $this->loadAssets();
  }

  /**
   * Load External Media plugin JS/CSS assets.
   */
  protected function loadAssets($prefix = 'WP_ExternalMedia_admin') {
    // Admin assets.
    wp_register_style( $prefix . '_css', plugins_url( '/css/admin.css', WP_ExternalMedia_PluginName ) );
    wp_register_script( $prefix . '_js', plugins_url( '/js/external-media.js', WP_ExternalMedia_PluginName ), array( 'jquery' ) );
    wp_register_script( $prefix . '_view_js', plugins_url( '/js/external-media-view.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'media-views' ) );
    wp_enqueue_style( $prefix . '_css' );
    wp_enqueue_script( $prefix . '_js' );
    wp_enqueue_script( $prefix . '_view_js' );
  }

  /**
   * Implements 'pre-plupload-upload-ui'.
   * Add External Media plugin buttons to Upload File tab.
   */
  public function pre_plupload_upload_ui() {
    $plugins = $this->load_plugins();
    $button_type = 'upload';
    if ( count( $plugins ) ) {
      $count_enabled = 0;
      foreach ( $plugins as $plugin => $info ) {
        $enabled = get_option( WP_ExternalMedia_Prefix . $plugin . '_enable', 0 );
        $insert_only = get_option( WP_ExternalMedia_Prefix . $plugin . '_insert_url_only', 0 );
        if ( $enabled && !$insert_only ) {
          $count_enabled++;
        }
      }
      if ( $count_enabled ) {
        echo '<p class="external-media-text">' . __('or choose file(s) from External Media') . '</p>';
      }
      else {
        echo '<p class="external-media-text">' . __('Please configure External Media plugin in order to be able to upload files from third-party services.') . '</p>';
      }
    }
    foreach ( $plugins as $plugin => $info ) {
      // Make sure only enabled plugins show up.
      $enabled = get_option( WP_ExternalMedia_Prefix . $plugin . '_enable', 0 );
      $insert_only = get_option( WP_ExternalMedia_Prefix . $plugin . '_insert_url_only', 0 );
      if ( $enabled && !$insert_only ) {
        $showImportButton = $this->_call_class_method( $info['phpClassName'], 'showImportButton' );
        if ($showImportButton) {
          $label = $this->_call_class_method( $info['phpClassName'], 'importLabel' );
          $html = $this->_call_class_method( $info['phpClassName'], 'html' );
          $id = $this->_call_class_method( $info['phpClassName'], 'id' );
          $attrs = array(
            'mime-types'  => get_allowed_mime_types(),
            'plugin'      => $plugin,
            'cardinality' => 50,
            'type'        => $button_type,
          );
          $attributes = $this->_call_class_method( $info['phpClassName'], 'attributes', array( $attrs ) );
          include ( sprintf( "%s/templates/button.php", WP_ExternalMedia_PATH ) );
        }
      }
    }
  }

  /**
   * Implements 'plugin_action_links_[plagin-name]'.
   * Add settings page link.
   */
  public function action_links( $links ) {
    $links[] = '<a href="options-general.php?page=WP_ExternalMedia">' . __('Settings'). '</a>'; 
    return $links; 
  }

  /**
   * Settings page.
   */
  public function _settings_page() {
    if ( !current_user_can('manage_options') ) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    $prefix = WP_ExternalMedia_Prefix;
    $plugins = $this->load_plugins();
    require_once ( sprintf( "%s/templates/settings.php", WP_ExternalMedia_PATH ) );
  }

  /**
   * Settings page help.
   */
  public function _settings_page_help() {
    $screen = get_current_screen();

    // Add my_help_tab if current screen is My Admin Page.
    $screen->add_help_tab( array(
        'id'      => 'wp_external_media',
        'title'   => __('Settings'),
        'content' => '<p>'
          . __( 'Please read each description in each field to properly configure the plugin.' )
          . ' '
          . __( 'More information on how to configure each plugin can be found in README.txt file.' )
          . '</p>',
      ) 
    );
  }

  /**
   * Activate the plugin.
   */
  public function activate() {
    global $wp_rewrite;
    // Refresh path rules.
    $wp_rewrite->flush_rules();
  }

  /**
   * Deactivate the plugin.
   */     
  public function deactivate() {
    global $wp_rewrite;
    // Refresh path rules.
    $wp_rewrite->flush_rules();
    delete_option( WP_ExternalMedia_Prefix . '_prepend_plugin_name' );
    delete_option( WP_ExternalMedia_Prefix . '_assets_frontend' );
    // Cleanup varibles after plugin deactivation.
    $plugins = $this->load_plugins();
    foreach ( $plugins as $plugin => $info ) {
      $settings = $this->_call_class_method( $info['phpClassName'], 'configForm', array() );
      delete_option( WP_ExternalMedia_Prefix . $plugin . '_enable' );
      delete_option( WP_ExternalMedia_Prefix . $plugin . '_insert_url_only' );
      foreach ($settings as $setting_name => $options) {
        delete_option( WP_ExternalMedia_Prefix . $setting_name );
      }
    }
  }

}
