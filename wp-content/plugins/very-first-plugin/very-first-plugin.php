<?php
/*
Plugin Name: My First plugin
Plugin URI: https://karlpodra.ikt.khk.ee/wordpress5
Description: This is my first WordPress Plugin
Author: Karl Podra
Author URI: https://karlpodra.ikt.khk.ee/wordpress5
Version: 1.0
*/
function my_plugin_test() {
	echo 'Hello World'; exit;
}
add_action('admin_head', 'my_plugin_test');