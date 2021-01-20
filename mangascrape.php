<?php
/*
Plugin Name: MangaScrape
Plugin URI: https://www.binarytemplar.com
Description: Download manga
Version: 0.1
Author: Dave McHale
Author URI: https://www.binarytemplar.com
Text Domain: mangascrape
Domain Path: /languages
*/

require_once( 'classes/classes.php' );

use mangascrape\AdminTools;

if ( is_admin() ) {
	$admin = new AdminTools();
}