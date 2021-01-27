<?php

namespace mangascrape;

class MSDownloader {

	private $results = array();
	private $destination_folder = '';
	private $sleep_min = 1;
	private $sleep_max = 7;
	private $cookies = array();

	function __construct( $results, $destination_folder ) {
		$this->results            = ( array ) $results;
		$this->destination_folder = $destination_folder;
	}

	public function process_downloads( $add_extension = '', $sleep_min = null, $sleep_max = null, $recursive = false ) {

		if ( $sleep_min ) {
			$this->sleep_min = $sleep_min;
		}
		if ( $sleep_max ) {
			$this->sleep_max = $sleep_max;
		}

		foreach ( $this->results as $result ) {

			$file_url  = $result;
			$file_name = basename( $result );

			$args = array(
				'cookies'   => $this->cookies,
			);

			var_dump( $args );
			var_dump( $file_url );

			// Download the file to memory
			$result = wp_remote_get( $file_url, $args );

			var_dump( $result );

			// Save cookies to object
			foreach ( $result['cookies'] as $cookie ) {
				$this->cookies[] = $cookie;
			}

			// If we didn't get a 200, and this was our first try, then try AGAIN. Next time we'll have a cookie
			if ( ! $recursive && 200 != $result['response']['code'] ) {
				$this->process_downloads( $add_extension, $sleep_min, $sleep_max, true );
			}

			wp_die();

			// Sets file final destination.
			$file_path = $this->destination_folder . '/' . $file_name . $add_extension;

			var_dump($file_path);

			// Copies the file to the final destination and deletes temporary file.
			copy( $tmp_file, $file_path );
			@unlink( $tmp_file );

			// Sleep before looping
			$sleep_for = rand( $this->sleep_min, $this->sleep_max );
			sleep( $sleep_for );

			wp_die();

		}

	}

}
