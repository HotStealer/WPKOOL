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
  <?php echo $page['footer']; ?>
</body>
</html>
