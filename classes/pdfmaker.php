<?php

namespace mangascrape;

class MSPDFMaker {

	private $source_folder = '';
	private $destination_folder = '';

	function __construct( $source_folder, $destination_folder ) {
		$this->source_folder      = $source_folder;
		$this->destination_folder = $destination_folder;
	}

	public function make_pdfs() {

		$chapters = $this->read_dirs( $this->source_folder );
		foreach ( $chapters as $chapter ) {
			echo 'Found `' . $chapter . '`<br />';
		}

	}

	private function make_pdf( $this_path ) {

	}

	private function read_dirs( $path ) {
		$arr_folders = array();

		$dirHandle = opendir( $path );
		while ( $item = readdir( $dirHandle ) ) {
			$new_path = $path . "/" . $item;
			if ( is_dir( $new_path ) && $item != '.' && $item != '..' ) {
				$arr_folders[] = $new_path;
			}
		}

		return $arr_folders;

	}

}
