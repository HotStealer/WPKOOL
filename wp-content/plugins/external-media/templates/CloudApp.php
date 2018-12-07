<?php

/**
 * WP_ExternalMedia callback page template.
 *
 * @package WP_ExternalMediaSE
 * @subpackage callback
 */

?>
<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title><?php if ( empty( $page['title'] ) ) : _e('Redirect callback'); else : echo esc_html( $page['title'] ); endif; ?></title>
  <?php echo $page['head']; ?>
</head>
<body>
  <?php echo $page['content']; ?>
  <div id="files"></div>

  <div class="cloudapp-picker-wrapper">

    <nav id="bar">
      <div class="container">
        <span class="name">
          <?php if (empty($page['data']['media']['error'])) : ?>
            <?php _e('We recommend to not import files from CloudApp instead just use "Insert from URL" option.') ?>
          <?php endif; ?>
        </span>
      </div>
    </nav>

    <div id="media-items">
      <?php if (!empty($page['data']['media']['error'])) : ?>
        <div id="error">
          <div class="wrapper">
            <div class="message">
              <?php _e('<p>Please make sure you provided correct email address and password.</p><p>If you do not have an account please signup to <a href="http://www.shareasale.com/r.cfm?B=1027572&U=1597643&M=71652&urllink=" target="_blank">CloudApp</a>.</p>') ?>
            </div>
          </div>
        </div>
      <?php else : ?>
        <?php foreach ($page['data']['media'] as $index => $item) : ?>
          <a href="#" class="item" data-index="<?php echo $index; ?>" data-link="<?php echo $item->url; ?>" data-standard-img="<?php echo $item->source_url; ?>"><div class="icon"><img src="<?php echo $item->icon; ?>" border="0" /></div>
            <div class="thumb">
              <?php if ($item->item_type == 'video') : ?>
                <img width="150" height="150" src="<?php echo plugins_url( '/plugins/CloudApp/video.png', WP_ExternalMedia_PluginName ); ?>" title="<?php echo $item->name; ?>" border="0" />
              <?php elseif ($item->item_type == 'audio') : ?>
                <img width="150" height="150" src="<?php echo plugins_url( '/plugins/CloudApp/audio.png', WP_ExternalMedia_PluginName ); ?>" title="<?php echo $item->name; ?>" border="0" />
              <?php else : ?>
                <img width="150" height="150" src="<?php echo $item->thumbnail_url; ?>" title="<?php echo $item->name; ?>" border="0" />
              <?php endif; ?>
            </div></a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div id="controls">
      <div class="container">
        <?php if (empty($page['data']['media']['error'])) : ?>
          <span id="count"></span>
          <a href="#" id="pick"><?php _e('Choose'); ?></a>
          <a href="#" id="cancel"><?php _e('Cancel'); ?></a>
        <?php endif; ?>
      </div>
    </div>

  </div>
  <?php echo $page['footer']; ?>
</body>
</html>
