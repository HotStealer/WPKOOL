<?php

/**
 * WP_ExternalMedia settings administration panel.
 *
 * @package WP_ExternalMedia
 * @subpackage Settings
 */

$title = __('External Media Settings');

?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<div class="external-media-donation-box">
  <p><?php _e('This is a 100% free plugin developed by one developer. All your donations help improve plugin and add new features. <a href="https://goo.gl/C2cBDF" class="button button-primary" target="_blank">Donate Now</a>'); ?></p>
</div>
<p><?php _e('Please configure each plugin that you would like to use. Once configured you should be able to see a new Media upload buttons.'); ?></p>

<form action="options.php" method="post">
<?php @settings_fields('WPExternalMedia'); ?>

<ul id="wp-external-media-settings-tabs">
  <li><span><?php _e('General'); ?></span></li>
  <?php foreach ( $plugins as $plugin => $info ) : ?>
    <li><span><?php _e($info['name']); ?></span></li>
  <?php endforeach; ?>
</ul>
<ul id="wp-external-media-settings-tab-contents">
    <li>
      <div class="content"><table class="form-table">
          <tr>
          <th scope="row"></th>
            <td>
            <input name="<?php echo $prefix; ?>_prepend_plugin_name" type="checkbox" id="<?php echo $prefix; ?>_prepend_plugin_name" value="1" <?php checked( '1', get_option( $prefix . '_prepend_plugin_name', 1 ) ); ?>/>
            <label for="<?php echo $prefix; ?>_prepend_plugin_name" class="enable"><?php _e('Prepend plugin name to file names.'); ?></label>
              <p class="description"><?php _e('Example: If you import file <strong>MyFile.jpg</strong> from Dropbox the file name will be changed on import and the new name will be <strong>Dropbox_MyFile.jpg</strong>. This is done to prevent possible name file name conflicts.'); ?></p>
            </td>
          </tr>
          <tr>
          <th scope="row"></th>
            <td>
            <input name="<?php echo $prefix; ?>_assets_frontend" type="checkbox" id="<?php echo $prefix; ?>_assets_frontend" value="0" <?php checked( '0', get_option( $prefix . '_assets_frontend', 0 ) ); ?>/>
            <label for="<?php echo $prefix; ?>_assets_frontend" class="enable"><?php _e('Add support for front end.'); ?></label>
              <p class="description"><?php _e('This option will add JS and CSS assets on the public site (non-admin pages).'); ?></p>
            </td>
          </tr>
        </table>
      </div>
    </li>
  <?php foreach ( $plugins as $plugin => $info ) : ?>
    <?php $settings = $this->_call_class_method( $info['phpClassName'], 'configForm', array() ); ?>
    <?php $showLinkButton = $this->_call_class_method( $info['phpClassName'], 'showLinkButton', array() ); ?>
    <li>
      <div class="content">
        <table class="form-table">
          <tr>
          <th scope="row"></th>
            <td>
            <input name="<?php echo $prefix . $plugin; ?>_enable" type="checkbox" id="<?php echo $prefix . $plugin; ?>_enable" value="1" <?php checked( '1', get_option( $prefix . $plugin . '_enable', 0 ) ); ?>/>
            <label for="<?php echo $prefix . $plugin; ?>_enable" class="enable"><?php _e('Enable this plugin'); ?></label>
            </td>
          </tr>
          <?php // Hide options that current plugin doesn't support. ?>
          <?php if ( $showLinkButton ) : ?>
          <tr>
          <th scope="row"></th>
            <td>
            <input name="<?php echo $prefix . $plugin; ?>_insert_url_only" type="checkbox" id="<?php echo $prefix . $plugin; ?>_insert_url_only" value="1" <?php checked( '1', get_option( $prefix . $plugin . '_insert_url_only', 0 ) ); ?>/>
            <label for="<?php echo $prefix . $plugin; ?>_insert_url_only" class="enable"><?php _e('Only allow insert URL to remote files'); ?></label>
              <p class="description"><?php _e('If this option is checked you will be able to insert URLs to remote files only. No file import will be available.'); ?></p>
            </td>
          </tr>
          <?php endif; ?>
          <?php foreach ( $settings as $name => $item ) : ?>
          <tr>
            <th scope="row"><?php if ( !empty( $item['#title'] ) ) : ?><label for="<?php echo $prefix . $name; ?>"><?php echo $item['#title'] ?></label><?php endif; ?></th>
          <td>
            <?php if ( $item['#type'] == 'markup' ) : ?>
              <?php echo $item['#markup']; ?>
            <?php elseif ( $item['#type'] == 'textfield' ) : ?>
              <input name="<?php echo $prefix . $name; ?>" <?php if ( !empty( $item['#placeholder'] ) ) : ?>placeholder="<?php echo $item['#placeholder']; ?>" <?php endif; ?>type="text" id="<?php echo $prefix . $name; ?>" value="<?php form_option( $prefix . $name ); ?>" class="regular-text" />
            <?php elseif ( $item['#type'] == 'password' ) : ?>
              <input name="<?php echo $prefix . $name; ?>" <?php if ( !empty( $item['#placeholder'] ) ) : ?>placeholder="<?php echo $item['#placeholder']; ?>" <?php endif; ?>type="password" id="<?php echo $prefix . $name; ?>" value="<?php form_option( $prefix . $name ); ?>" class="regular-text" />
            <?php elseif ( $item['#type'] == 'checkbox' ) : ?>
              <input name="<?php echo $prefix . $name; ?>" type="checkbox" id="<?php echo $prefix . $name; ?>" value="1" <?php checked( '1', get_option( $prefix . $name, $item['#default_value'] ) ); ?>/>
              <label for="<?php echo $prefix . $name; ?>" class="enable"><?php echo !empty($item['#label']) ? $item['#label'] : $item['#title']; ?></label>
            <?php elseif ( $item['#type'] == 'textarea' ) : ?>
              <textarea name="<?php echo $prefix . $name; ?>" <?php if ( !empty( $item['#placeholder'] ) ) : ?>placeholder="<?php echo $item['#placeholder']; ?>" <?php endif; ?>rows="10" id="<?php echo $prefix . $name; ?>" class="large-text code" /><?php echo get_option( $prefix . $name, (int) $item['#default_value'] ); ?></textarea>
            <?php elseif ( $item['#type'] == 'select' ) : ?>
              <select name="<?php echo $prefix . $name; ?>" id="<?php echo $prefix . $name; ?>" />
                <?php foreach ($item['#options'] as $key => $value) : ?>
                  <?php $default_value = !empty($item['#default_value']) ? $item['#default_value'] : ''; ?>
                  <?php if (get_option( $prefix . $name, $default_value ) == $key) : ?>
                    <option value="<?php echo $key; ?>" selected><?php echo $value; ?></option>
                  <?php else : ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
            <?php if ( !empty( $item['#description'] ) ) : ?>
              <p class="description"><?php echo $item['#description']; ?></p>
            <?php endif; ?>
          </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php @do_settings_sections('WPExternalMedia'); ?>

<div id="button-wrapper"><?php submit_button(); ?></div>

</form>

</div>
