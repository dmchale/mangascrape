<?php
namespace mangascrape;

class MSExploder {

	private $source_folder = '';
	private $destination_folder = '';

	function __construct( $source_folder, $destination_folder ) {
		$this->source_folder        = $source_folder;
		$this->destination_folder   = $destination_folder;
	}

	public function detonate() {

		$str_glob = $this->source_folder . '/*.zip';
		$files = glob( $str_glob );

		$za = new \ZipArchive;

		foreach ( $files as $file ) {

			// Create destination folder for this zip
			$folder_name = $this->destination_folder . '/' . str_replace( '.zip', '', basename( $file ) );
			MSHelpers::create_dir( $folder_name, true );

			// Open zip and explode to our desired location
			if ( true === $za->open( $file ) ) {
				$za->extractTo( $folder_name );
				$za->close();
				echo '`' . basename( $file ) . '` successfully exploded!<br />';
			} else {
				echo 'Failed to unpack ' . basename( $file );
			}

		}

	}

}
