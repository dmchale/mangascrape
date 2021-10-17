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

use mangascrape\AdminTools;
use mangascrape\MangaScrape;

require_once( 'classes/classes.php' );

// Define plugin constants
define( 'MANGASCRAPE_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MANGASCRAPE_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'MANGASCRAPE_UPLOAD_DIR', wp_upload_dir()['basedir'] . '/mangascrape/' );

// Initialize main class
new MangaScrape( __FILE__ );

// Initialize admin class
if ( is_admin() ) {
	new AdminTools();
}
