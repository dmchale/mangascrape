<?php
namespace mangascrape;

class MSDownloader {

	private $results = array();
	private $destination_folder = '';

	function __construct( $results, $destination_folder ) {
		$this->results              = ( array ) $results;
		$this->destination_folder   = $destination_folder;
	}

	public function process_downloads() {

		foreach ( $this->results as $result ) {

			$file_url   = $result;
			$file_name  = basename( $result );

			// Download the file to memory
			$tmp_file = download_url( $file_url );

			// Sets file final destination.
			$file_path = $this->destination_folder . '/' . $file_name . '.zip';

			// Copies the file to the final destination and deletes temporary file.
			copy( $tmp_file, $file_path );
			@unlink( $tmp_file );

			// Sleep before looping
			$sleep_for = rand( 1, 7 );
			sleep( $sleep_for );

		}

	}

}
