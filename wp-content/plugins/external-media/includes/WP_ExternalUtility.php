<?php

/**
 * @package WP_ExternalMedia
 */

class WP_ExternalUtility {

  /**
   * Load plugins.
   */
  public function load_plugins() {
    $plugins = $this->supported_plugins();
    foreach ( $plugins as $plugin => $info ) {
      $plugin_file = $info['path'];
      if ( file_exists( $plugin_file ) ) {
        require_once ( $plugin_file );
      }
      else {
        // If plugin declared but the file doesn't exists.
        unset( $plugins[$plugin] );
      }
    }
    // Sort plugins by weight.
    uasort( $plugins,  array($this, 'sortPlugins') );
    return $plugins;
  }

  /**
   * Load plugin.
   */
  public function load_plugin( $plugin = '' ) {
    $plugins = $this->supported_plugins();
    if ( !empty( $plugins[$plugin] ) ) {
      $plugin_file = $plugins[$plugin]['path'];
      if ( file_exists( $plugin_file ) ) {
        require_once ( $plugin_file );
        return $plugins[$plugin];
      }
    }
  }

  /**
   * Call plugin methods.
   */
  public function _call_class_method( $class, $method, $args = array() ) {
    $reflection = new \ReflectionClass( $class );
    $method = $reflection->getMethod( $method );
    $pluginClass = new $class();
    return $method->invokeArgs( $pluginClass, $args );
  }

  /**
   * Get plugin path.
   */
  public function _get_class_path( $class ) {
    $reflection = new \ReflectionClass( $class );
    $file = $reflection->getFileName();
    return $file;
  }

  /**
   * Supported plugins.
   */
  public function supported_plugins() {
    $plugins = array();
    $paths = array();
    $paths[] = WP_ExternalMedia_PATH . "/plugins/*.php";
    if ( $extended_plugins = apply_filters( 'extended_plugins_path', $paths ) ) {
      $paths = array_merge( $paths, $extended_plugins );
    }
    foreach ( $paths as $path ) {
      foreach ( glob( $path ) as $file ) {
        include_once $file;
      }
    }
    foreach ( get_declared_classes() as $class ) {
      if ( is_subclass_of( $class, 'WP_ExternalPluginBase' ) ) {
        $name = $this->_call_class_method( $class, 'name' );
        $plugins[$class] = array(
          'name'         => $name,
          'phpClassName' => $class,
          'weight'       => $this->_call_class_method( $class, 'weight' ),
          'path'         => $this->_get_class_path( $class ),
        );
      }
    }
    return $plugins;
  }

  /**
   * Callback for `uasort` function.
   */
  protected function sortPlugins( $a, $b ) {
    $a_weight = ( is_array( $a ) && isset( $a['weight'] ) ) ? $a['weight'] : 0;
    $b_weight = ( is_array( $b ) && isset( $b['weight'] ) ) ? $b['weight'] : 0;
    if ( $a_weight == $b_weight ) {
      return 0;
    }
    return ( $a_weight < $b_weight ) ? -1 : 1;
  }

}
