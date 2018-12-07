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
  <?php
    echo $page['content'];
    $counts = $page['data']['counts'];
    $profile = $page['data']['profile'];
    $photos = $page['data']['photos'];
  ?>
  <div id="files"></div>

  <div class="instagram-picker-wrapper">

    <nav id="bar">
      <div class="container">
        <?php if (!empty($counts)) : ?>
          <span class="counts">
            <span class="posts"><?php echo $counts['media']; ?></span>
            <span class="followed_by"><?php echo $counts['followed_by']; ?></span>
            <span class="follows"><?php echo $counts['follows']; ?></span>
          </span>
        <?php endif; ?>
        <span class="name">
          <?php echo $profile->username; ?>
          <?php if (!empty($profile->full_name)) : ?>
            (<?php echo $profile->full_name; ?>)
          <?php endif; ?>
        </span>
      </div>
    </nav>

    <div id="photos">
      <?php foreach ($photos as $index => $photo) : ?>
        <a href="#" class="photo" data-index="<?php echo $index; ?>"  data-link="<?php echo $photo['link']; ?>" data-standard-img="<?php echo $photo['standard_resolution']->url; ?>"><img width="150" height="150" src="<?php echo $photo['thumbnail']->url; ?>" border="0" /></a>
      <?php endforeach; ?>
    </div>

    <div id="controls">
      <div class="container">
        <span id="count"></span>
        <a href="#" id="pick"><?php _e('Choose'); ?></a>
        <a href="#" id="cancel"><?php _e('Cancel'); ?></a>
      </div>
    </div>

  </div>
  <?php echo $page['footer']; ?>
</body>
</html>
