<?php
/**
 * Plugin Name: Dark Mode
 * Plugin URI: https://wordpress.org/plugins/dark-mode/
 * Description: Lets your users make the WordPress admin dashboard darker.
 * Author: Daniel James
 * Author URI: https://www.danieltj.co.uk/
 * Text Domain: dark-mode
 * Version: 3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

require( 'class-dark-mode.php' );

$dark_mode = new Dark_Mode();
