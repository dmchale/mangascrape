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
			$this->make_pdf( $chapter );
		}

	}

	private function make_pdf( $this_path ) {

		require_once( plugin_dir_path( __FILE__ ) . '../lib/fpdf/fpdf.php' );

		$pdf_name = basedir( $this_path ) . '.pdf';

		require($_SERVER['DOCUMENT_ROOT'].'/banjo-glossary/fpdf/fpdf.php');

		// TODO: test and modify this code!!
		// Taken from https://legacy.imagemagick.org/discourse-server/viewtopic.php?t=27216

/*
		$pdf = new FPDF('P','pt','Letter');
// THESE VARS WILL BE SET DYNAMICALLY
		$pdf->SetTitle('Create PDF test using FPDF',1);
		$pdf->SetAuthor('The Banjo Glossary Project',1);
		$pdf->SetSubject('Ongoing tests to create pdf files',1);
		$pdf->SetCompression(1);

// LETTER size pages
// UNIT IS POINTS, 72 PTS = 1 INCH
		$pageW = 612 - 36; // 8.5 inches wide with .25 margin left and right
		$pageH = 792 - 36; // 11 inches tall with .25 margin top and bottom
		$fixedMargin = 18; // .25 inch
		$threshold = $pageW / $pageH;

// IF IMAGE W÷H IS UNDER THRESHOLD, CONSTRAIN THE HEIGHT
// IF IMAGE W÷H IS OVER THRESHOLD, CONSTRAIN THE WIDTH

		$readPath = $_SERVER['DOCUMENT_ROOT'].'/banjo-glossary/_temp_/';
		$writePath = $_SERVER['DOCUMENT_ROOT'].'/banjo-glossary/';

		function sizeImage($thisImage) {
			global $pageW,$pageH,$fixedMargin,$threshold;

			list($thisW,$thisH) = getimagesize($thisImage);

			if($thisW<=$pageW && $thisH<=$pageH){
				// DO NOT RESIZE IMAGE, JUST CENTER IT HORIZONTALLY
				$newLeftMargin = centerMe($thisW);
				$leftMargin = $newLeftMargin;
				return array('leftMargin' => $leftMargin, 'width' => $thisW);
			} else {
				$thisThreshold = $thisW / $thisH;
				if($thisThreshold>=$threshold) {
					$width = $pageW;
					$leftMargin = $fixedMargin;
				} else {
					$thisMultiplier = $pageH / $thisH;
					$width = $thisW * $thisMultiplier;
					$width = round($width, 0, PHP_ROUND_HALF_DOWN);
					// CENTER ON PAGE IF NOT FULL WIDTH
					$newLeftMargin = centerMe($width);
					$leftMargin = $newLeftMargin;
				}
				return array('leftMargin' => $leftMargin, 'width' => $width);
			}
		}

		function centerMe($thisWidth){
			global $pageW;
			$newMargin = ($pageW - $thisWidth) / 2;
			$newMargin = round($newMargin, 0, PHP_ROUND_HALF_DOWN);
			return $newMargin;
		}

// THIS VAR WILL BE POPULATED DYNAMICALLY BUT HARD CORDED FOR THIS EXAMPLE
		$imageLIST = array('tab-angeline-the-ba-12739-5552112112010.jpg','tab-at-the-end-of-t-11988-541238102009.jpg','tab-blue-night-(lam-12956-2337161222010.jpg','tab-eighth-of-janua-12698-2650161012010.jpg','tab-foggy-mountain--19894-3131218122013.jpg','tallThin.jpg');

		foreach ($imageLIST as $value) {
			$currentImage = $readPath.$value;
			$reSized = sizeImage($currentImage);
			$width = $reSized['width'];
			$leftMargin = $reSized['leftMargin'];
			$pdf->AddPage();
			$pdf->Image($currentImage,$leftMargin,18,$width);

		} // LOOP

		$pdf->Output($writePath.'/TEST-PDFwrite99.pdf','F');

		echo 'All done.';
		*/


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
