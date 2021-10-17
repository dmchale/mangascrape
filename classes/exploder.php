<?php

namespace mangascrape;

use ZipArchive;

class MSExploder {

	private string $source_folder;
	private string $destination_folder;

	function __construct( $source_folder, $destination_folder ) {
		$this->source_folder      = $source_folder;
		$this->destination_folder = $destination_folder;
	}

	public function detonate() {

		$str_glob = $this->source_folder . '/*.zip';
		$files    = glob( $str_glob );

		$zip_archive = new ZipArchive;

		foreach ( $files as $file ) {

			// Create destination folder for this zip
			$folder_name = $this->destination_folder . '/' . str_replace( '.zip', '', basename( $file ) );
			MSHelpers::create_dir( $folder_name, true );

			// Open zip and explode to our desired location
			if ( true === $zip_archive->open( $file ) ) {
				$zip_archive->extractTo( $folder_name );
				$zip_archive->close();
				echo '`' . basename( $file ) . '` successfully exploded!<br />';
			} else {
				echo 'Failed to unpack ' . basename( $file );
			}

		}

	}

}
