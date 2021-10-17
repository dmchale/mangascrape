<?php

namespace mangascrape;

/**
 * Class MSHelpers
 * @package mangascrape
 *
 * This class is meant to host static functions that can be called at any time
 *
 */
class MSHelpers {

	/**
	 * Modified from https://stackoverflow.com/questions/2021624/string-sanitizer-for-filename
	 *
	 * @param $str
	 *
	 * @return false|string
	 */
	public static function make_valid_foldername( $str ) {

		// Replace any of "[]()." with _
		$str = mb_ereg_replace( "([\[\]\(\).])", '_', $str );

		// Replace anything NOT a word, space, digit, dash or underscore with "nothing"
		$str = mb_ereg_replace( "([^\w\s\d\-_])", '', $str );

		// Remove any runs of periods
		return mb_ereg_replace( "([\.]{2,})", '', $str );

	}

	/**
	 * Create folder and do error handling as needed
	 *
	 * @param $new_dir
	 * @param bool $hide_errors
	 */
	public static function create_dir( $new_dir, bool $hide_errors = false ) {

		// First check to see if the folder exists already
		if ( is_dir( $new_dir ) ) {
			if ( ! $hide_errors ) {
				wp_die( 'ERROR: Folder with the name `' . $new_dir . '` already exists. Try something unique!' );
			}
		}

		// Now try and create the folder
		if ( ! @mkdir( $new_dir, 0700 ) ) {
			if ( ! $hide_errors ) {
				wp_die( 'Failed creating `' . $new_dir . '` directory, check folder permissions.' );
			}
		}

	}

}
