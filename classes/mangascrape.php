<?php

namespace mangascrape;

class MangaScrape {

	/**
	 * MangaScrape constructor.
	 *
	 * @param $file
	 */
	function __construct( $file ) {
		register_activation_hook( $file, array( &$this, 'activate' ) );
		register_deactivation_hook( $file, array( &$this, 'deactivate' ) );
	}

	/**
	 * Activation hook
	 */
	public function activate() {

		// Make sure we create our folder on plugin activation
		if ( ! is_dir( MANGASCRAPE_UPLOAD_DIR ) ) {
			if ( ! @mkdir( MANGASCRAPE_UPLOAD_DIR, 0700 ) ) {
				wp_die( 'Failed creating /wp-content/uploads/mangascrape/ directory during plugin activation' );
			}
		}

	}


	/**
	 * Deactivation hook
	 */
	public function deactivate() {
		// Do nothing... for now
	}

}
