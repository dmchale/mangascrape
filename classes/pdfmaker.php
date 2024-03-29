<?php

namespace mangascrape;

use FPDF;

class MSPDFMaker {

	private string $source_folder;
	private string $destination_folder;

	function __construct( $source_folder, $destination_folder ) {
		$this->source_folder      = $source_folder;
		$this->destination_folder = $destination_folder;
	}


	/**
	 *
	 */
	public function make_pdfs() {

		$chapters = $this->read_dirs( $this->source_folder );
		foreach ( $chapters as $chapter ) {
			$this->make_pdf( $chapter );
		}

	}


	/**
	 * @param $source_path
	 */
	private function make_pdf( $source_path ) {

		require_once( MANGASCRAPE_DIR_PATH . 'lib/fpdf/fpdf.php' );

		$clean_base_name = basename( $source_path );
		$pdf_name = $clean_base_name . '.pdf';

		/*
		 * Taken and modified from https://legacy.imagemagick.org/discourse-server/viewtopic.php?t=27216
		 */
		$pdf = new FPDF( 'P', 'pt', 'Letter' );

		// THESE VARS WILL BE SET DYNAMICALLY
		$pdf->SetTitle( $clean_base_name, 1 );
		$pdf->SetAuthor( 'MangaScrape: a WordPress Plugin', 1 );
		$pdf->SetSubject( 'Manga', 1 );
		$pdf->SetCompression( 1 );

		// LETTER size pages
		// UNIT IS POINTS, 72 PTS = 1 INCH
		$pageW       = 612 - 24; // 8.5 inches wide with .125 margin left and right
		$pageH       = 792 - 24; // 11 inches tall with .125 margin top and bottom
		$fixedMargin = 12; // .25 inch
		$threshold   = $pageW / $pageH;

		$images = glob( $source_path . '/*.jpg' );
		natsort( $images );     // Sorts the array the way a human would .... _1 to _2 to _3 instead of _1 to _10 to _11

		foreach ( $images as $image ) {
			$currentImage = $image;

			// IF IMAGE W÷H IS UNDER THRESHOLD, CONSTRAIN THE HEIGHT
			// IF IMAGE W÷H IS OVER THRESHOLD, CONSTRAIN THE WIDTH
			$reSized      = $this->sizeImage( $currentImage, $pageW, $pageH, $fixedMargin, $threshold );
			$width        = $reSized['width'];
			$leftMargin   = $reSized['leftMargin'];
			$pdf->AddPage();

			$pdf->Image( $currentImage, $leftMargin, 18, $width );
		}

		$pdf->Output( $this->destination_folder . '/' . $pdf_name, 'F' );

	}


	/**
	 * @param $thisImage
	 * @param $pageW
	 * @param $pageH
	 * @param $fixedMargin
	 * @param $threshold
	 *
	 * @return array
	 */
	private function sizeImage( $thisImage, $pageW, $pageH, $fixedMargin, $threshold ): array {

		list( $thisW, $thisH ) = getimagesize( $thisImage );

		if ( $thisW <= $pageW && $thisH <= $pageH ) {
			// DO NOT RESIZE IMAGE, JUST CENTER IT HORIZONTALLY
			$newLeftMargin = $this->centerMe( $thisW, $pageW );
			$leftMargin    = $newLeftMargin;

			return array( 'leftMargin' => $leftMargin, 'width' => $thisW );
		} else {
			$thisThreshold = $thisW / $thisH;
			if ( $thisThreshold >= $threshold ) {
				$width      = $pageW;
				$leftMargin = $fixedMargin;
			} else {
				$thisMultiplier = $pageH / $thisH;
				$width          = $thisW * $thisMultiplier;
				$width          = round( $width, 0, PHP_ROUND_HALF_DOWN );
				// CENTER ON PAGE IF NOT FULL WIDTH
				$newLeftMargin = $this->centerMe( $width, $pageW );
				$leftMargin    = $newLeftMargin;
			}

			return array( 'leftMargin' => $leftMargin, 'width' => $width );
		}
	}


	/**
	 * @param $thisWidth
	 * @param $pageW
	 *
	 * @return float
	 */
	private function centerMe( $thisWidth, $pageW ): float {
		$newMargin = ( $pageW - $thisWidth ) / 2;
		return round( $newMargin, 0, PHP_ROUND_HALF_DOWN );
	}


	/**
	 * @param $path
	 *
	 * @return array
	 */
	private function read_dirs( $path ): array {
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
