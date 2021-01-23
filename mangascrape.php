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

// Define plugin constants
define( 'MANGASCRAPE_UPLOAD_DIR', wp_upload_dir()['basedir'] . '/mangascrape/' );

// Initilize main class
new \mangascrape\MangaScrape( __FILE__ );

// Initiliaze admin class
if ( is_admin() ) {
	new \mangascrape\AdminTools();
}
