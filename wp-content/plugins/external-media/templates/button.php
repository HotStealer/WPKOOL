<?php

/**
 * WP_ExternalMedia Insert from URL buttons.
 *
 * @package WP_ExternalMediaSE
 * @subpackage upload buttons
 */

?>
<?php if ( $button_type == 'url' ) : ?>
<div style="display:none;" id="tmpl-external-media-links">
  <?php if ( count( $buttons ) ) : ?>
   <?php foreach ( $buttons as $button ) : ?>
       <a href="#" id="<?php echo $button['id']; ?>" <?php echo $button['attributes']; ?> <?php echo $button['attributes']; ?> class="button button-primary <?php echo $button['id']; ?>"><?php echo $button['label']; ?></a>
    <?php endforeach; ?>
    <p class="description"><?php _e('If you would like to permanently upload file instead of linking to it use Insert Media > Upload Files.'); ?></p>
  <?php else : ?>
    <p><?php _e('Please configure External Media plugin in order to be able to insert links to files from third-party services.'); ?></p>
  <?php endif; ?>
</div>
<?php else : ?>
<div class="external-media-buttons-wrapper">
  <button id="<?php echo $id; ?>" <?php echo $attributes; ?> class="button button-large external-media-button <?php echo $id; ?>">
    <?php echo $label; ?>
  </button>
  <?php echo $html; ?>
</div>
<?php endif; ?>
