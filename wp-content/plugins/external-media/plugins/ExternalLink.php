<?php

/**
 * @package WP_ExternalMedia
 * External Media plugin class.
 */

/**
 * External Link
 */
class ExternalLink extends WP_ExternalPluginBase {

  protected static $external_link_loaded = false;

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
    return __('URL');
  }

  /**
   * {@inheritdoc}
   */
  public function weight() {
    return -11;
  }

  /**
   * {@inheritdoc}
   */
  public function showLinkButton() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function importLabel() {
    return __('Import from External URL');
  }

  /**
   * {@inheritdoc}
   */
  public function chooserLabel() {
    return __('Link to External File');
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return 'em-external-link';
  }

  /**
   * {@inheritdoc}
   */
  public function html() {
    add_thickbox();
    return '<div id="em-external-link-modal" class="em-external-link-modal" style="display:none;"><div class="em-form">
      <table class="form-table">
          <tr>
            <th scope="row"><label for="url">' . __('Import a file from remote URL') . '</label></th>
          <td>
            <input type="url" name="url" id="url" class="url" placeholder="' . __('Mostly image files allowed') . '" regular-text" />
          </td>
        </tr>
        <tr>
          <th scope="row"></th>
          <td align="right">
            <input type="button" name="insert" id="el-insert" class="button button-primary button-hero el-insert" value="' . __('Import') . '">
            <input type="button" name="cancel" id="el-cancel" class="button button-hero el-cancel" value="' . __('Cancel') . '">
          </td>
      </table>
    </div></div>';
  }

  /**
   * {@inheritdoc}
   */
  public function configForm() {
    $elements['external_link'] = array(
      '#title' => '',
      '#type' => 'markup',
      '#markup' => '<div><p>' . __('This plugin enables an option to import files from any remote URL.') . '</p></div>',
    );
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function attributes( $items ) {
    $attributes = array();
    foreach ( $items as $attribute => $value ) {
      // External Link button attributes.
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
    if ( $this::$external_link_loaded ) {
      return;
    }
    $class = get_class($this);
    wp_register_script( $class, plugins_url( '/plugins/js/ExternalLink.js', WP_ExternalMedia_PluginName ), array( 'jquery', 'WP_ExternalMedia_admin_view_js' ) );
    wp_enqueue_script( $class );
    wp_register_style( $class . '_css', plugins_url( '/css/external-link-popup.css', WP_ExternalMedia_PluginName ) );
    wp_enqueue_style( $class . '_css' );
    $this::$external_link_loaded = true;
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

}
